<?php

namespace App\Livewire\Admin\Purchase\PurchaseOrder;

use App\Models\PurchaseOrder;
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
use Illuminate\Support\Facades\DB;

class PurchaseOrderCreate extends Component
{
    // 🚀 1. Declara las propiedades necesarias
    public ?PurchaseOrder $order = null; // Instancia de la orden si ya existe
    public $state = 'draft'; // Estado inicial por defecto
    // Propiedades de la Orden
    public $partner_id, $partner_name, $warehouse_id, $date_order, $date_approve, $notes;
    public $currency_id, $moneda_base_id, $currency_rate = 1, $currency_symbol = 'S/', $currency_code = 'PEN';
    //public $uoms = [];
    // Totales
    public $amount_untaxed = 0, $amount_tax = 0, $amount_total = 0;
    public $tax_group = [];
    public $precision = 2;

    // Líneas y Búsquedas
    public $lines = [];
    public $search_partner = '';
    public $partner_results = [];
    //public $currencies;
    //public $warehouses = [];

    public $picking_count = 0;
    public $bill_count = 0;



    public function mount($id = null)
    {
        $company = Company::first();
        if (!$company) dd("Error: Configure su compañía.");

        // Cargamos datos base
        $this->moneda_base_id = $company->currency_id;
        $this->precision = $company->decimal_purchase ?? 2;
        $this->date_order = now()->format('Y-m-d');
        $this->date_approve = now()->addDays(7)->format('Y-m-d');

        if ($id) {
            // Carga ansiosa para el proceso inicial
            $this->order = PurchaseOrder::with([
                'lines.product.template.purchaseTaxes',
                'lines.uom',
                'partner'
            ])->withCount(['pickings', 'accountMoves'])->findOrFail($id);

            $this->state = $this->order->state;
            $this->partner_id = $this->order->partner_id;
            $this->partner_name = $this->order->partner->name;
            $this->currency_id = $this->order->currency_id;
            $this->warehouse_id = $this->order->warehouse_id;
            $this->notes = $this->order->notes;

            // 🚀 SMART BUTTONS: Guardamos los conteos en variables simples
            $this->picking_count = $this->order->pickings_count;
            $this->bill_count = $this->order->account_moves_count;

            // Transformar líneas a array plano
            $this->lines = $this->order->lines->map(fn($line) => [
                'product_id'      => $line->product_id,
                'uom_id'          => $line->product_uom_id,
                'product_search'  => $line->name,
                'product_results' => [],
                'uom_name'        => $line->uom->name ?? '',
                'product_qty'     => (float) $line->product_qty,
                'price_unit'      => (float) $line->price_unit,
                'taxes'           => $line->product->template->purchaseTaxes->toArray(),
                'price_subtotal'  => (float) $line->price_subtotal,
                'price_total'     => (float) $line->price_total,
            ])->toArray();

            $this->calculateTotals();
        } else {
            $this->currency_id = $this->moneda_base_id;
            $this->addLine();
        }
    }




    // App\Livewire\Admin\Purchase\PurchaseOrder\PurchaseOrderCreate.php

    public function confirmOrder()
    {
        $service = app(PurchaseOrderService::class);
        try {
            // 1. Guardamos la lógica actual
            $this->order = $this->saveLogic($service);

            // 2. Llamamos al servicio con la orden ya cargada en la clase
            $this->order = $service->confirmOrder($this->order);

            // 3. Actualizamos estado visual
            $this->state = $this->order->state;

            session()->flash('success', "Orden {$this->order->name} confirmada y recepción generada.");
            return redirect()->route('purchase.order.edit', $this->order->id);
        } catch (\Throwable $e) {
            // 🚨 Esto te dirá exactamente qué campo falta si vuelve a fallar
            $this->dispatch('show-swal', [
                'icon' => 'error',
                'title' => 'Error de Inventario',
                'text' => $e->getMessage()
            ]);
        }
    }







    // 🚀 2. Implementa el método createBill() aquí
    public function createBill()
    {
        // Seguridad Senior: Solo facturamos si es una Orden de Compra confirmada
        if ($this->state !== 'purchase') {
            $this->dispatch('show-swal', icon: 'error', text: 'Solo se puede facturar una Orden de Compra confirmada.');
            return;
        }

        try {
            // Delegamos la lógica pesada al Service Layer que ya creamos
            // Este servicio creará el AccountMove y las líneas de asiento
            $invoice = app(\App\Services\Accounting\AccountMoveService::class)
                ->createFromPurchaseOrder($this->order);

            $this->dispatch('show-swal', icon: 'success', text: 'Factura borrador creada correctamente.');

            // Odoo Style: Redirigimos a la factura recién creada
            return redirect()->route('admin.vendor-bills.edit', $invoice->id);
        } catch (\Exception $e) {
            $this->dispatch('show-swal', icon: 'error', text: $e->getMessage());
        }
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
        // 🛡️ Blindaje contra negativos en Cantidad y Precio
        if (str_contains($key, 'product_qty') || str_contains($key, 'price_unit')) {
            $parts = explode('.', $key);
            $index = $parts[0];
            $field = $parts[1]; // 'product_qty' o 'price_unit'

            // 🛡️ Forzamos valor positivo y permitimos decimales
            $cleanValue = abs(floatval($value));

            // Si el usuario intentó poner un negativo, lo corregimos en el array
            if (floatval($value) < 0) {
                $this->lines[$index][$field] = $cleanValue;
                $this->dispatch('show-swal', icon: 'warning', text: 'No se permiten valores negativos.');
            }

            $this->calculateTotals();
        }



        // 🔍 Lógica de búsqueda de productos
        if (str_contains($key, 'product_search')) {
            $parts = explode('.', $key);
            $index = $parts[0];

            // Solo buscar si hay más de 1 caracter para no saturar la base de datos
            if (strlen($value) > 1) {
                $this->lines[$index]['product_results'] = ProductTemplate::query()
                    ->select(
                        'product_templates.id',
                        'product_templates.name',
                        'product_variants.price_purchase',
                        'product_variants.id as variant_id'
                    )
                    ->join('product_variants', 'product_templates.id', '=', 'product_variants.product_template_id')
                    ->where('product_variants.is_default', true) // Buscamos la variante principal
                    ->where('product_templates.active', true)
                    ->where('product_templates.name', 'like', "%{$value}%")
                    ->limit(5)
                    ->get()
                    ->toArray();
            } else {
                // Limpiar resultados si la búsqueda es muy corta
                $this->lines[$index]['product_results'] = [];
            }
        }
    }



    public function updatedCurrencyId($value)
    {
        $company = Company::first();
        //$currency = $this->currencies->find($value);
        $currency = Currency::find($value);

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






    public function render()
    {
        // 🚀 TIP SENIOR: Carga estas colecciones aquí.
        // Livewire las enviará a la vista en cada renderizado, evitando el error de "null".
        return view('livewire.admin.purchase.purchase-order.purchase-order-create', [
            'currencies' => Currency::where('active', true)->get(),
            'warehouses' => Warehouse::all(),
            'uoms'       => Uom::where('active', true)->get(),
        ]);
    }


    /**
     * 🚀 NUEVO: Separamos la lógica de guardado de la acción del botón
     * para poder llamarla desde confirmOrder sin errores de argumentos.
     */
    private function saveLogic(PurchaseOrderService $service)
    {
        $this->validate([
            'partner_id' => 'required',
            'lines' => 'required|array|min:1',
        ]);

        $orderData = [
            'partner_id' => $this->partner_id,
            'currency_id' => $this->currency_id,
            'warehouse_id' => $this->warehouse_id ?? 1,
            'date_order' => $this->date_order,
            'date_approve' => $this->date_approve,
            'amount_untaxed' => $this->amount_untaxed,
            'amount_tax' => $this->amount_tax,
            'amount_total' => $this->amount_total,
            'currency_rate' => $this->currency_rate,
            'notes' => $this->notes,
        ];

        $linesData = collect($this->lines)->map(fn($l) => [
            'product_id' => $l['product_id'],
            'product_uom_id' => $l['uom_id'],
            'name' => $l['product_search'],
            'product_qty' => $l['product_qty'],
            'price_unit' => $l['price_unit'],
            'price_subtotal' => $l['price_subtotal'],
            'price_total' => $l['price_total'],
            'taxes_data' => $l['taxes'],
        ])->toArray();

        if ($this->order) {
            // Lógica de actualización si ya existe
            return $service->updateRFQ($this->order, $orderData, $linesData);
        } else {
            // Creación inicial
            return $service->createRFQ($orderData, $linesData);
        }
    }




    public function save(PurchaseOrderService $service)
    {
        // 🚀 Detectamos si es una creación o una actualización
        $isNew = !$this->order || !$this->order->exists;

        $order = $this->saveLogic($service);

        // Definimos el mensaje dinámico estilo Odoo
        $message = $isNew
            ? "Solicitud de Cotización {$order->name} creada con éxito."
            : "La solicitud {$order->name} ha sido actualizada.";

        // Usamos session flash porque hay una redirección de por medio
        session()->flash('success', $message);

        // Redirigimos a la edición para que el usuario vea el número generado en la URL y el título
        return redirect()->route('purchase.order.edit', $order->id);
    }





    public function receiveProducts()
    {
        $picking = $this->order->pickings()->where('state', '!=', 'done')->first();

        if ($picking) {
            // Redirigimos al nuevo componente que acabamos de crear
            return redirect()->route('admin.stock.picking.edit', $picking->id);
        }
    }


    /**
     * 🚀 Enviar Orden de Compra por Correo
     */
    public function sendEmailPO()
    {
        $service = app(PurchaseOrderService::class);
        // Reutilizamos la lógica que ya tienes en el Listado
        // Pero inyectando la lógica aquí para evitar duplicar código
        $this->dispatch('show-swal', icon: 'info', text: 'Preparando envío de correo...');
        // ... llamar al servicio de correo
    }

    /**
     * 🚀 Cancelar la Orden
     */
    public function cancelOrder()
    {
        if ($this->state === 'purchase' && $this->picking_count > 0) {
            // Lógica Senior: Cancelar también los pickings asociados si no están 'done'
            $this->order->pickings()->where('state', '!=', 'done')->update(['state' => 'cancel']);
        }

        $this->order->update(['state' => 'cancel']);
        $this->state = 'cancel';
        $this->dispatch('show-swal', icon: 'success', text: 'Orden de compra cancelada.');
    }


    // En PurchaseOrderCreate.php
    public function getPendingPickingsCountProperty()
    {
        // Solo contamos pickings que NO estén en estado 'done' o 'cancel'
        return $this->order->pickings()
            ->whereNotIn('state', ['done', 'cancel'])
            ->count();
    }
}
