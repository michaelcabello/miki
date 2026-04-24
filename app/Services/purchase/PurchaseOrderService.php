<?php

namespace App\Services\Purchase;

use App\Models\Company;
use App\Models\DocumentSetting;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\StockPicking; // Asumiendo que existe


class PurchaseOrderService
{
    public function createRFQ(array $data, array $lines): PurchaseOrder
    {
        return DB::transaction(function () use ($data, $lines) {
            $count = PurchaseOrder::whereYear('created_at', now()->year)->count() + 1;
            $data['name'] = 'RFQ/' . now()->year . '/' . str_pad($count, 5, '0', STR_PAD_LEFT);
            $data['user_id'] = auth()->id();

            $order = PurchaseOrder::create($data);

            foreach ($lines as $line) {
                $taxes = $line['taxes_data'] ?? [];
                unset($line['taxes_data']);
                $orderLine = $order->lines()->create($line);

                if (!empty($taxes)) {
                    $orderLine->taxes()->attach(collect($taxes)->pluck('id'));
                }
            }

            $this->generateAndUploadPdf($order);
            return $order;
        });
    }



    public function generateAndUploadPdf(PurchaseOrder $order)
    {
        // 1. Buscamos la plantilla activa con su tipo de comprobante
        $template = DocumentSetting::with('comprobanteType')
            ->whereHas('comprobanteType', fn($q) => $q->where('code', 'RFQ'))
            ->where('activate', true)
            ->first();

        $view = $template ? $template->blade_path : 'admin.pdf.purchase-order';
        $paperSize = $template ? $template->paper_size : 'a4';


        $company = Company::with(['department', 'province', 'district'])->first();

        // 2. Generar el PDF (Ya no necesitamos procesar Base64 aquí)
        $pdf = Pdf::loadView($view, [
            'record' => $order->load(['lines.product', 'partner', 'currency', 'lines.uom']),
            'company' => $company,
            'settings' => $template,
        ]);

        // 🚀 IMPORTANTE: Esto permite que DomPDF cargue la URL de S3
        $pdf->setOption([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'chroot' => public_path(),
        ]);

        if ($paperSize == '80mm') {
            // 80mm son aprox 226pt. El largo (500pt) puede ser mayor si el ticket es largo.
            $pdf->setPaper([0, 0, 226.77, 600], 'portrait');
        } else {
            $pdf->setPaper($paperSize, 'portrait');
        }

        //$pdf->setPaper($paperSize, 'portrait');

        // 3. Guardar en S3_PRIVATE
        $path = "purchases/rfqs/{$order->id}_" . str_replace('/', '_', $order->name) . ".pdf";
        \Storage::disk('s3_private')->put($path, $pdf->output(), 'private');

        $order->update(['pdf_path' => $path]);
    }


    /**
     * 🚀 HELPER SENIOR: Procesa el logo con manejo de errores y Fallback de URL
     */
    private function prepareLogo($company)
    {
        if (!$company || !$company->logo) return null;

        try {
            $disk = Storage::disk('s3_public');

            // Limpiamos la ruta por si acaso tenga un slash inicial
            $cleanPath = ltrim($company->logo, '/');

            if ($disk->exists($cleanPath)) {
                $content = $disk->get($cleanPath);
                $extension = strtolower(pathinfo($cleanPath, PATHINFO_EXTENSION));

                // 💡 DomPDF prefiere 'jpeg' en lugar de 'jpg' para el mime-type
                $mime = ($extension == 'jpg') ? 'jpeg' : $extension;

                return 'data:image/' . $mime . ';base64,' . base64_encode($content);
            } else {
                // 🚀 ÚLTIMO RECURSO: Intentamos descargarlo por URL pública si el disco falla internamente
                $url = $disk->url($cleanPath);
                $content = @file_get_contents($url);
                if ($content) {
                    $extension = pathinfo($cleanPath, PATHINFO_EXTENSION);
                    $mime = ($extension == 'jpg') ? 'jpeg' : $extension;
                    return 'data:image/' . $mime . ';base64,' . base64_encode($content);
                }
            }
        } catch (\Exception $e) {
            Log::error("Error crítico en prepareLogo: " . $e->getMessage());
        }

        return null;
    }

    public function getSecureUrl(PurchaseOrder $order): ?string
    {
        if (!$order->pdf_path) return null;
        return Storage::disk('s3_private')->temporaryUrl($order->pdf_path, now()->addMinutes(15));
    }


    public function confirmOrderBack(PurchaseOrder $order): PurchaseOrder
    {
        return DB::transaction(function () use ($order) {
            // 1. Cambiamos identidad y estado
            $order->convertToPurchaseOrder();
            $order->state = 'purchase';
            $order->date_approve = now();
            $order->save(); // 💾 Guardamos el cambio de estado primero

            // 2. Intentamos generar el inventario
            try {
                $this->generateStockPicking($order);
            } catch (\Exception $e) {
                // Logeamos el error pero no matamos la confirmación de la compra
                Log::error("Error al generar picking: " . $e->getMessage());
                // Si prefieres que sea obligatorio, quita este try/catch
            }

            return $order->fresh();
        });
    }


    public function confirmOrder(PurchaseOrder $order): PurchaseOrder
    {
        return DB::transaction(function () use ($order) {
            // 1. Transformación de identidad (RFQ -> P)
            $order->convertToPurchaseOrder();

            // 2. Cambio de estado y sellado de fechas
            $order->state = 'purchase';
            $order->date_approve = now(); // 🚀 Ahora que existe, lo usamos
            $order->date_order = now();   // Sincronizamos la fecha oficial de compra

            $order->save();

            // 3. Logística (Comentado para pruebas iniciales)
            $this->generateStockPicking($order);

            return $order->fresh();
        });
    }




    /**
     * 🚀 ACTUALIZAR RFQ (Solicitud de Cotización)
     * Maneja la actualización de cabecera, líneas e impuestos.
     */
    public function updateRFQ(PurchaseOrder $order, array $data, array $lines): PurchaseOrder
    {
        return DB::transaction(function () use ($order, $data, $lines) {
            // 1. Actualizamos los datos básicos de la cabecera
            $order->update($data);

            // 2. Gestionamos las líneas (Estrategia: Reemplazo Total)
            // En Odoo, mientras el documento sea Borrador, podemos reconstruir las líneas.
            $order->lines()->delete();

            foreach ($lines as $line) {
                $taxes = $line['taxes_data'] ?? [];

                // Limpiamos datos que no van directamente a la tabla purchase_order_lines
                unset($line['taxes_data']);

                // Creamos la nueva línea vinculada a la orden
                $orderLine = $order->lines()->create($line);

                // Sincronizamos los impuestos
                if (!empty($taxes)) {
                    $orderLine->taxes()->attach(collect($taxes)->pluck('id'));
                }
            }

            // 3. Regeneramos el PDF para que refleje los nuevos cambios (Precios, cantidades)
            $this->generateAndUploadPdf($order);

            // 4. Refrescamos el modelo para devolverlo actualizado
            return $order->fresh(['lines.taxes', 'partner', 'currency']);
        });
    }


    // En PurchaseOrderService.php

    // App\Services\Purchase\PurchaseOrderService.php

    protected function generateStockPicking(PurchaseOrder $order): void
    {
        // 1. Creamos la cabecera (Picking)
        $picking = \App\Models\StockPicking::create([
            'name'              => 'WH/IN/' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
            'operation_type_id' => 1, // Recepción
            'location_from_id'  => 1, // Ubicación Proveedor (Vendor)
            'location_to_id'    => 2, // Ubicación Stock
            'partner_id'        => $order->partner_id,
            'purchase_order_id' => $order->id,
            'state'             => 'assigned',
            'scheduled_date'    => now(),
        ]);

        // 2. Creamos los movimientos (Stock Moves)
        foreach ($order->lines as $line) {
            \App\Models\StockMove::create([
                'stock_picking_id'       => $picking->id,          // ✅ Corregido
                'product_variant_id'     => $line->product_id,     // ✅ Corregido
                'location_from_id'       => $picking->location_from_id, // ✅ Corregido
                'location_to_id'         => $picking->location_to_id,   // ✅ Corregido
                'purchase_order_line_id' => $line->id,             // ✅ Trazabilidad
                'qty_demand'             => $line->product_qty,    // ✅ Corregido
                'price_unit'             => $line->price_unit,     // ✅ Agregado
                'state'                  => 'assigned',            // ✅ Según tu enum
            ]);
        }
    }
}
