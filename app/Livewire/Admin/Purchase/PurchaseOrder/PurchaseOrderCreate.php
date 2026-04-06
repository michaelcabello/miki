<?php

namespace App\Livewire\Admin\Purchase\PurchaseOrder;

use App\Models\Company;
use Livewire\Component;
use App\Models\Currency;
use App\Models\CurrencyRate;
use App\Models\Warehouse;
use App\Models\ProductTemplate;
use App\Models\ProductVariant;
use App\Models\Partner;
use App\Models\Uom;
use App\Services\TaxService;
use Illuminate\Support\Facades\Auth;
use App\Services\Purchase\PurchaseOrderService; // 🚀 AGREGA ESTA LÍNEA

class PurchaseOrderCreate extends Component
{
    // Propiedades de la Orden
    public $partner_id, $partner_name, $warehouse_id, $date_order, $date_approve, $notes;
    public $currency_id, $moneda_base_id, $currency_rate = 1, $currency_symbol = 'S/', $currency_code = 'PEN';
    public $uoms = [];
    // Totales
    public $amount_untaxed = 0, $amount_tax = 0, $amount_total = 0;
    public $tax_group = [];
    public $precision = 2;

    // Líneas y Búsquedas
    public $lines = [];
    public $search_partner = '';
    public $partner_results = [];
    public $currencies;
    public $warehouses = [];

    public function mount()
    {
        $company = Company::first();

        if (!$company) {
            dd("Error crítico: No existe ningún registro en la tabla 'companies'.");
        }

        $this->uoms = Uom::where('active', true)->get();

        $this->currencies = Currency::where('active', true)->get();
        $this->warehouses = Warehouse::all();

        $this->moneda_base_id = $company->currency_id;
        $this->currency_id = $this->moneda_base_id;

        $baseCurrency = $this->currencies->find($this->currency_id);
        $this->currency_symbol = $baseCurrency->abbreviation ?? 'S/';
        $this->currency_code = $baseCurrency->name ?? 'PEN';

        $this->precision = $company->decimal_purchase ?? 2;
        $this->date_order = now()->format('Y-m-d');
        $this->date_approve = now()->addDays(7)->format('Y-m-d');

        $this->addLine();
    }

    /**
     * 🚀 Búsqueda en tiempo real de Proveedores (Partners)
     */
    public function updatedSearchPartner($value)
    {
        if (strlen($value) < 2) {
            $this->partner_results = [];
            return;
        }

        $this->partner_results = Partner::where(function ($query) use ($value) {
            $query->where('name', 'like', "%{$value}%")
                ->orWhere('document_number', 'like', "%{$value}%");
        })
            ->limit(10)
            ->get(['id', 'name', 'document_number'])
            ->toArray();
    }

    /**
     * 🚀 Seleccionar Proveedor
     */
    public function selectPartner($id, $name)
    {
        $this->partner_id = $id;
        $this->partner_name = $name;
        $this->partner_results = [];
        $this->search_partner = '';
    }

    /**
     * 🚀 Seleccionar Producto (Template -> Variant) y conversión de moneda
     */
    public function selectProductBack($index, $templateId)
    {
        $template = ProductTemplate::with('uom')->find($templateId);

        if (!$template) return;

        $variant = ProductVariant::where('product_template_id', $template->id)
            ->where('active', true)
            ->orderBy('is_default', 'desc')
            ->first();

        if (!$variant) {
            session()->flash('error', 'El producto no tiene variantes activas.');
            return;
        }

        $priceInBaseCurrency = floatval($variant->price_purchase ?? 0);

        if ($this->currency_id != $this->moneda_base_id) {
            $convertedPrice = $priceInBaseCurrency / max($this->currency_rate, 0.0001);
        } else {
            $convertedPrice = $priceInBaseCurrency;
        }

        $this->lines[$index]['product_id'] = $variant->id;
        $this->lines[$index]['product_search'] = $template->name;
        $this->lines[$index]['uom_name'] = $template->uom->name ?? 'Unidad';
        $this->lines[$index]['price_unit'] = round($convertedPrice, $this->precision);
        $this->lines[$index]['product_results'] = [];

        $this->calculateTotals();
    }




    public function selectProduct($index, $templateId)
    {
        // 🚀 Cargamos el template con sus impuestos de compra
        $template = ProductTemplate::with(['uom', 'purchaseTaxes'])->find($templateId);

        if (!$template) return;

        $variant = ProductVariant::where('product_template_id', $template->id)
            ->where('active', true)
            ->orderBy('is_default', 'desc')
            ->first();

        if (!$variant) {
            session()->flash('error', 'El producto no tiene variantes activas.');
            return;
        }

        $priceInBaseCurrency = floatval($variant->price_purchase ?? 0);

        // Lógica de conversión de moneda
        if ($this->currency_id != $this->moneda_base_id) {
            $convertedPrice = $priceInBaseCurrency / max($this->currency_rate, 0.0001);
        } else {
            $convertedPrice = $priceInBaseCurrency;
        }

        $this->lines[$index]['product_id'] = $variant->id;
        $this->lines[$index]['product_search'] = $template->name;
        $this->lines[$index]['uom_name'] = $template->uom->name ?? 'Unidad';
        $this->lines[$index]['price_unit'] = round($convertedPrice, $this->precision);
        // Asignamos el ID de la unidad para el select
        $this->lines[$index]['uom_id'] = $template->uom_id;
        $this->lines[$index]['uom_name'] = $template->uom->name ?? 'Unidad';

        // 🚀 LA CLAVE: Guardamos la DATA COMPLETA de los impuestos en la línea
        // Tu TaxService recorre este array buscando 'amount_type', 'price_include', etc.
        $this->lines[$index]['taxes'] = $template->purchaseTaxes->toArray();

        $this->lines[$index]['product_results'] = [];

        $this->calculateTotals();
    }






    public function updatedLines($value, $key)
    {
        if (str_contains($key, 'product_qty') || str_contains($key, 'price_unit')) {
            $this->calculateTotals();
        }

        if (str_contains($key, 'product_search')) {
            $parts = explode('.', $key);
            $index = $parts[0];

            if (strlen($value) > 1) {
                $this->lines[$index]['product_results'] = ProductTemplate::query()
                    ->select('product_templates.id', 'product_templates.name', 'product_variants.price_purchase')
                    ->join('product_variants', 'product_templates.id', '=', 'product_variants.product_template_id')
                    ->where('product_variants.is_default', true)
                    ->where('product_templates.name', 'like', "%{$value}%")
                    ->limit(5)
                    ->get()
                    ->toArray();
            } else {
                $this->lines[$index]['product_results'] = [];
            }
        }
    }

    public function updatedCurrencyId($value)
    {
        $company = Company::first();
        $currency = $this->currencies->find($value);

        if ($currency) {
            $this->currency_symbol = $currency->abbreviation;
            $this->currency_code = $currency->name;
        }

        if ($value == $company->currency_id) {
            $this->currency_rate = 1;
        } else {
            $rateEntry = CurrencyRate::where('currency_id', $value)
                ->where('date', '<=', $this->date_order)
                ->orderBy('date', 'desc')
                ->first();

            $this->currency_rate = $rateEntry ? $rateEntry->sell_rate : 1;
        }

        $this->recalculateLinePrices();
        $this->calculateTotals();
    }

    public function recalculateLinePrices()
    {
        foreach ($this->lines as $index => $line) {
            if ($line['product_id']) {
                $variant = ProductVariant::find($line['product_id']);
                $priceInBase = floatval($variant->price_purchase ?? 0);

                if ($this->currency_id != $this->moneda_base_id) {
                    $newPrice = $priceInBase / max($this->currency_rate, 0.0001);
                } else {
                    $newPrice = $priceInBase;
                }

                $this->lines[$index]['price_unit'] = round($newPrice, $this->precision);
            }
        }
    }

    public function calculateTotals()
    {
        $taxService = new TaxService($this->precision);
        $this->amount_untaxed = 0;
        $this->amount_tax = 0;
        $this->tax_group = [];

        foreach ($this->lines as $index => $line) {
            if (!($line['product_id'] ?? null)) continue;

            $result = $taxService->computeTaxes(
                floatval($line['price_unit']),
                floatval($line['product_qty']),
                $line['taxes'] ?? [],
                floatval($this->currency_rate)
            );

            $this->lines[$index]['price_subtotal'] = $result['total_excluded'];
            $this->lines[$index]['price_total'] = $result['total_included'];

            foreach ($result['taxes'] as $tax) {
                $name = $tax['name'];
                $this->tax_group[$name] = ($this->tax_group[$name] ?? 0) + $tax['amount'];
            }

            $this->amount_untaxed += $result['total_excluded'];
            $this->amount_tax += $result['total_taxes'];
        }

        $this->amount_total = round($this->amount_untaxed + $this->amount_tax, $this->precision);
    }

    public function addLine()
    {
        $this->lines[] = [
            'product_id' => null,
            'uom_id' => null, // 🚀 Agregamos esta llave
            'product_search' => '',
            'product_results' => [],
            'uom_name' => '',
            'product_qty' => 1,
            'price_unit' => 0,
            'taxes' => [],
            'price_subtotal' => 0,
            'price_total' => 0,
        ];
    }

    public function removeLine($index)
    {
        unset($this->lines[$index]);
        $this->lines = array_values($this->lines);
        $this->calculateTotals();
    }


    public function save(PurchaseOrderService $service)
    {
        $this->validate([
            'partner_id' => 'required',
            'lines' => 'required|array|min:1',
        ]);

        $orderData = [
            'partner_id' => $this->partner_id,
            'currency_id' => $this->currency_id,
            'warehouse_id' => $this->warehouse_id ?? 1, // Default por ahora
            'date_order' => $this->date_order,
            'date_approve' => $this->date_approve,
            'amount_untaxed' => $this->amount_untaxed,
            'amount_tax' => $this->amount_tax,
            'amount_total' => $this->amount_total,
            'currency_rate' => $this->currency_rate,
        ];

        // Mapeamos las líneas del componente al formato de la DB
        $linesData = collect($this->lines)->map(fn($l) => [
            'product_id' => $l['product_id'],
            'product_uom_id' => $l['uom_id'], // 🚀 Mapeo correcto a la DB
            'name' => $l['product_search'],
            'product_qty' => $l['product_qty'],
            'price_unit' => $l['price_unit'],
            'price_subtotal' => $l['price_subtotal'],
            'price_total' => $l['price_total'],
            'taxes_data' => $l['taxes'],
        ])->toArray();

        $order = $service->createRFQ($orderData, $linesData);

        session()->flash('success', "Solicitud {$order->name} guardada correctamente.");
        return redirect()->route('purchase.order.index');
    }






    public function render()
    {
        return view('livewire.admin.purchase.purchase-order.purchase-order-create');
    }
}
