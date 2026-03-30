<?php

namespace App\Livewire\Admin\Purchase\PurchaseOrder;

use Livewire\Component;

use App\Models\Partner;
use App\Models\Currency;
use App\Models\Warehouse;
use App\Models\ProductVariant;
use App\Models\Uom;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use Illuminate\Support\Facades\{DB, Log};
use Carbon\Carbon;
// Suponiendo que usas Jantinnerezo/LivewireAlert o similar para Toasts,
// sino adaptarlo a tu sistema de notificaciones mostrado en el ejemplo (Toastr)
//use Jantinnerezo\LivewireAlert\LivewireAlert;

class PurchaseOrderCreate extends Component
{

    // Cabecera
    public $partner_id, $partner_name, $warehouse_id, $date_order, $date_approve, $currency_id, $notes;
    public $amount_untaxed = 0, $amount_tax = 0, $amount_total = 0;

    // Líneas y Búsqueda
    public $lines = [];
    public $search_partner = '', $partner_results = [];
    //public $warehouses = [], $currencies = [];
    /** @var \Illuminate\Database\Eloquent\Collection */
    public $warehouses;

    /** @var \Illuminate\Database\Eloquent\Collection */
    public $currencies;

    public function mount()
    {
        $this->warehouses = Warehouse::all(['id', 'name']);
        $this->currencies = Currency::where('state', true)->get();
        $this->date_order = Carbon::now()->format('Y-m-d');
        $this->date_approve = Carbon::now()->addDays(7)->format('Y-m-d');
        //$this->warehouse_id = $this->warehouses->first()->id ?? null;
        $this->warehouse_id = null;
        $this->currency_id = $this->currencies->where('principal', true)->first()->id ?? null;

        $this->addLine();
    }

    // --- LÓGICA DE PROVEEDORES ---
    public function updatedSearchPartner()
    {
        if (strlen($this->search_partner) < 2) {
            $this->partner_results = [];
            return;
        }

        $this->partner_results = Partner::query()
            ->where('supplier_rank', '>', 0) // REQUERIMIENTO: Solo proveedores
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search_partner . '%')
                    ->orWhere('document_number', 'like', '%' . $this->search_partner . '%');
            })
            ->limit(10)->get(['id', 'name', 'document_number']);
    }

    public function selectPartner($id, $name)
    {
        $this->partner_id = $id;
        $this->partner_name = $name;
        $this->search_partner = '';
        $this->partner_results = [];
    }

    // --- LÓGICA DE LÍNEAS (GRILLA) ---
    public function addLine()
    {
        $this->lines[] = [
            'product_id' => null,
            'product_search' => '',
            'product_results' => [],
            'name' => '',
            'product_qty' => 1,
            'uom_name' => '',
            'price_unit' => 0,
            'price_subtotal' => 0,
            'price_total' => 0, // 👈 AGREGAR ESTO: Evita el error de llave indefinida
            'taxes' => [],      // 👈 AGREGAR ESTO: Evita errores en el foreach de calculateTotals
        ];
    }

    public function removeLine($index)
    {
        unset($this->lines[$index]);
        $this->lines = array_values($this->lines); // Reindexar para mantener la integridad de Livewire
        $this->calculateTotals();
    }

    /**
     * Búsqueda Estilo Odoo 18: Template Name -> Show All Variants
     */
    public function updatedLines($value, $key)
    {
        if (str_contains($key, '.product_search')) {
            $index = explode('.', $key)[0];

            if (strlen($value) < 2) {
                $this->lines[$index]['product_results'] = [];
                return;
            }

            // REQUERIMIENTO: Buscamos por nombre de plantilla y mostramos sus variantes
            $this->lines[$index]['product_results'] = ProductVariant::query()
                ->with(['productTemplate.purchaseUom'])
                ->whereHas('productTemplate', function ($q) use ($value) {
                    $q->where('name', 'like', '%' . $value . '%');
                })
                ->limit(20) // Límite más alto para ver todas las variantes
                ->get()
                ->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        // Formato Odoo: Plantilla (Variante)
                        'display_name' => $variant->variant_name
                            ? $variant->productTemplate->name . ' (' . $variant->variant_name . ')'
                            : $variant->productTemplate->name,
                        'price_purchase' => $variant->price_purchase,
                        'uom_name' => $variant->productTemplate->purchaseUom->name ?? 'Unid'
                    ];
                })->toArray();
        }

        $this->calculateTotals();
    }

    public function selectProductBack($index, $productId)
    {
        // Cargamos la variante con su plantilla y sus impuestos de compra (relación Many-to-Many que tienes en tus migraciones)
        $product = ProductVariant::with(['productTemplate.purchaseTaxes', 'productTemplate.purchaseUom'])->find($productId);

        if ($product) {
            $this->lines[$index]['product_id'] = $product->id;
            $this->lines[$index]['name'] = $product->variant_name ?? $product->productTemplate->name;
            $this->lines[$index]['price_unit'] = $product->price_purchase ?? 0;

            // Guardamos los datos de los impuestos en la línea para no re-consultar la DB en cada cálculo
            $this->lines[$index]['taxes'] = $product->productTemplate->purchaseTaxes->map(function ($tax) {
                return [
                    'id' => $tax->id,
                    'amount' => $tax->amount,
                    'amount_type' => $tax->amount_type,
                    'price_include' => $tax->price_include,
                    'include_base_amount' => $tax->include_base_amount,
                ];
            })->toArray();

            $this->lines[$index]['uom_name'] = $product->productTemplate->purchaseUom->name ?? 'Unid';
            $this->lines[$index]['product_search'] = $this->lines[$index]['name'];
            $this->lines[$index]['product_results'] = [];

            $this->calculateTotals();
        }
    }

    public function selectProductBack2($index, $productId)
    {
        $product = ProductVariant::with(['productTemplate.purchaseTaxes', 'productTemplate.purchaseUom'])->find($productId);

        if ($product) {
            $this->lines[$index]['product_id'] = $product->id;
            $this->lines[$index]['name'] = $product->variant_name ?? $product->productTemplate->name;
            $this->lines[$index]['price_unit'] = $product->price_purchase ?? 0;

            // Aseguramos que taxes sea un array, aunque esté vacío
            $this->lines[$index]['taxes'] = $product->productTemplate->purchaseTaxes ? $product->productTemplate->purchaseTaxes->map(function ($tax) {
                return [
                    'id' => $tax->id,
                    'amount' => $tax->amount,
                    'amount_type' => $tax->amount_type,
                    'price_include' => $tax->price_include,
                    'active' => $tax->active,
                    'include_base_amount' => $tax->include_base_amount,
                ];
            })->toArray() : [];

            $this->lines[$index]['uom_name'] = $product->productTemplate->purchaseUom->name ?? 'Unid';
            $this->lines[$index]['product_search'] = $this->lines[$index]['name'];
            $this->lines[$index]['product_results'] = [];

            $this->calculateTotals();
        }
    }

    public function selectProduct($index, $productId)
    {
        // Cargamos la variante con sus relaciones
        $product = ProductVariant::with(['productTemplate.purchaseTaxes', 'productTemplate.purchaseUom'])->find($productId);

        if ($product) {
            $this->lines[$index]['product_id'] = $product->id;
            $this->lines[$index]['name'] = $product->variant_name ?? $product->productTemplate->name;
            $this->lines[$index]['price_unit'] = $product->price_purchase ?? 0;

            // 🚀 CORRECCIÓN: Mapear absolutamente todas las llaves que usa calculateTotals
            $this->lines[$index]['taxes'] = $product->productTemplate->purchaseTaxes->map(function ($tax) {
                return [
                    'id' => $tax->id,
                    'amount' => $tax->amount,
                    'amount_type' => $tax->amount_type,
                    'price_include' => (bool)$tax->price_include,
                    'include_base_amount' => (bool)$tax->include_base_amount,
                    'active' => (bool)$tax->active, // 👈 ESTA ERA LA LLAVE FALTANTE
                ];
            })->toArray();

            $this->lines[$index]['uom_name'] = $product->productTemplate->purchaseUom->name ?? 'Unid';
            $this->lines[$index]['product_search'] = $this->lines[$index]['name'];
            $this->lines[$index]['product_results'] = [];

            // Ejecutar el cálculo con los nuevos impuestos cargados
            $this->calculateTotals();
        }
    }



    public function calculateTotals()
    {
        // 1. Obtener la configuración del Tenant actual
        // Asumimos que tienes la instancia de la compañía en una variable o servicio
        $company = auth()->user()->company; // O la lógica que uses para multitenancy
        $precision = $company->decimal_purchase ?? 2;

        $this->amount_untaxed = 0;
        $this->amount_tax = 0;

        foreach ($this->lines as $index => $line) {
            $qty = floatval($line['product_qty'] ?? 0);
            $price_unit = floatval($line['price_unit'] ?? 0);
            $taxes = $line['taxes'] ?? []; // Cargados previamente en selectProduct

            if ($qty <= 0) {
                $this->lines[$index]['price_subtotal'] = 0;
                $this->lines[$index]['price_total'] = 0;
                continue;
            }

            // --- INICIO DE LÓGICA DE IMPUESTOS ---

            // A. Identificar impuestos incluidos para obtener el precio neto (Base)
            $total_included_percent = 0;
            foreach ($taxes as $tax) {
                if ($tax['active'] && $tax['price_include'] && $tax['amount_type'] === 'percent') {
                    $total_included_percent += ($tax['amount'] / 100);
                }
            }

            // B. Calcular el Precio Unitario Neto (desglosando el impuesto si está incluido)
            // Ejemplo: Si el precio es 118 y el IGV es 18% incluido -> Neto es 100
            $net_unit_price = $price_unit / (1 + $total_included_percent);

            // Redondeamos el unitario neto según la precisión de la empresa
            $net_unit_price = round($net_unit_price, $precision);

            // C. Base imponible de la línea
            $base_line = round($net_unit_price * $qty, $precision);

            // D. Calcular montos de impuestos (pueden ser varios)
            $line_tax_total = 0;
            foreach ($taxes as $tax) {
                if (!$tax['active']) continue;

                $tax_amount = 0;
                if ($tax['amount_type'] === 'percent') {
                    // Si el impuesto estaba incluido, el monto es la diferencia entre el bruto y el neto
                    if ($tax['price_include']) {
                        // Calculamos la proporción de este impuesto específico dentro del total incluido
                        $tax_ratio = ($tax['amount'] / 100) / (1 + $total_included_percent);
                        $tax_amount = ($price_unit * $tax_ratio) * $qty;
                    } else {
                        // Si no está incluido, se calcula sobre la base neta
                        $tax_amount = $base_line * ($tax['amount'] / 100);
                    }
                } elseif ($tax['amount_type'] === 'fixed') {
                    $tax_amount = $tax['amount'] * $qty;
                }

                $line_tax_total += round($tax_amount, $precision);

                // Odoo: Si el impuesto afecta la base del siguiente (impuestos en cascada)
                if ($tax['include_base_amount']) {
                    $base_line += round($tax_amount, $precision);
                }
            }

            // E. Asignación a la línea
            // En Odoo, el subtotal de la línea SIEMPRE es sin impuestos (Base Imponible)
            $this->lines[$index]['price_subtotal'] = $base_line;

            // Guardamos el total con impuestos para mostrarlo en el Retail UI
            $this->lines[$index]['price_total'] = round($base_line + $line_tax_total, $precision);

            // F. Acumuladores generales
            $this->amount_untaxed += $base_line;
            $this->amount_tax += $line_tax_total;
        }

        // Redondeo final de totales según configuración de la empresa
        $this->amount_untaxed = round($this->amount_untaxed, $precision);
        $this->amount_tax = round($this->amount_tax, $precision);
        $this->amount_total = round($this->amount_untaxed + $this->amount_tax, $precision);
    }




    public function save()
    {
        $this->validate([
            'partner_id' => 'required',
            'warehouse_id' => 'required',
            'lines.*.product_id' => 'required',
            'lines.*.product_qty' => 'required|numeric|gt:0',
            'lines.*.price_unit' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $count = PurchaseOrder::count() + 1;
            $name = 'RFQ-' . now()->format('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $po = PurchaseOrder::create([
                'name' => $name,
                'partner_id' => $this->partner_id,
                'currency_id' => $this->currency_id,
                'warehouse_id' => $this->warehouse_id,
                'date_order' => $this->date_order,
                'amount_untaxed' => $this->amount_untaxed,
                'amount_tax' => $this->amount_tax,
                'amount_total' => $this->amount_total,
                'state' => 'draft',
                'notes' => $this->notes,
            ]);

            foreach ($this->lines as $line) {
                $po->lines()->create([
                    'product_id' => $line['product_id'],
                    'name' => $line['name'],
                    'product_qty' => $line['product_qty'],
                    'price_unit' => $line['price_unit'],
                    'price_subtotal' => $line['price_subtotal'],
                ]);
            }

            DB::commit();
            session()->flash('swal', ['icon' => 'success', 'title' => '¡Éxito!', 'text' => "RFQ $name creada."]);
            return redirect()->route('purchase.order.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error RFQ: " . $e->getMessage());
            session()->flash('swal', ['icon' => 'error', 'title' => 'Error', 'text' => 'No se pudo guardar la orden.']);
        }
    }

    public function render()
    {
        return view('livewire.admin.purchase.purchase-order.purchase-order-create');
    }
}
