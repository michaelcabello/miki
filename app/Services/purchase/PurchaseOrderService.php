<?php

namespace App\Services\Purchase;

use App\Models\Company;
use App\Models\DocumentSetting;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

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
}
