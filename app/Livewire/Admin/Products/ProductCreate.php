<?php

namespace App\Livewire\Admin\Products;

use App\Models\Account;
use Livewire\Component;

use App\Models\UomCategory;
use App\Models\Uom;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\ProductTemplate;
use App\Models\ProductVariant;
use Illuminate\Support\Str;
use App\Models\Tax;
use App\Models\Detraction;
use App\Models\Brand;
use App\Models\Modello;
use App\Models\Season;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\Models\PosCategory;
use App\Models\PricelistItem;
use App\Models\Pricelist;

//php artisan make:livewire Admin/Products/ProductCreate
class ProductCreate extends Component
{

    // En tu componente Livewire (ProductTemplateForm.php o similar)
    // 1. Asegúrate de tener estas propiedades declaradas arriba
    public ?int $product_id = null; // ID si estamos editando
    public array $temporary_prices = []; // Para guardar precios antes de crear el producto
    public array $allPricelists = []; // Para el select del modal

    public $is_subscription = false; // Solo para controlar la UI (frontend)

    // Variables que se guardarán en la DB
    public $subscription_plan_id;
    public $recurring_price;
    public $start_date;
    public $status = 'active';

    public string $newPosCategoryName = '';

    public string $posCategorySearch = '';
    public array $filteredPosCategories = [];
    public array $selectedPosCategories = []; // opcional para vista, además de pos_category_ids

    public string $additionalProductSearch = '';
    public array $filteredAdditionalProducts = [];
    public array $selectedAdditionalProducts = []; // opcional para vista


    public array $posCategoryOptions = [];
    public array $pos_category_ids = [];

    public array $additionalProductOptions = [];
    public array $additional_product_ids = [];



    // Product template
    public string $name = '';
    public string $type = 'goods';
    public bool $sale_ok = true;
    public bool $purchase_ok = false;
    public bool $pos_ok = true;
    public bool $active = true;

    //public ?int $tax_id = null;
    public array $sale_tax_ids = [];      // impuestos de venta (multi)
    public array $purchase_tax_ids = [];  // impuestos de compra (multi)

    public ?int $detraction_id = null;
    public ?int $brand_id = null;
    public ?int $modello_id = null;
    public ?string $tracking = 'quantity'; // quantity|serial|lot (o null)

    public array $taxOptions = [];
    public array $detractionOptions = [];
    public array $brandOptions = [];
    public array $modelloOptions = [];
    public array $seasonOptions = [];

    public array $attributeLines = []; // líneas estilo Odoo
    public $catalogAttributes = [];

    public ?int $uom_id = null;
    public ?int $uom_po_id = null;

    //public $uom_id = null;
    //public $uom_po_id = null;

    public $category_id = null;
    public array $categoryOptions = [];

    public array $uomCategories = [];      // para el select agrupado
    public array $uomPurchaseOptions = []; // opciones filtradas por categoría



    //para escoger variantes
    public int $variants_count = 1;
    public array $variant_preview = []; // para mostrar algunas combinaciones
    public int $preview_limit = 12;

    // Default variant base price (IMPORTANT: price lives in variants)
    public $base_price_sale = 0;

    // Optional sku prefix
    public string $sku_prefix = '';

    // Attributes / values selection
    public array $selectedAttributes = []; // [attribute_id => true]
    public array $selectedValues = [];     // [attribute_id => [value_id => true]]

    public string $tab = 'general';
    public $barcode = null;
    public $reference = null;

    public array $accountOptions = [];

    public ?int $account_sell_id = null;
    public ?int $account_buy_id = null;

    private $accountSettings;


    public ?int $defaultSellGoodsId = null;
    public ?int $defaultSellServiceId = null;

    public ?int $defaultBuyGoodsId = null;
    public ?int $defaultBuyServiceId = null;

    // Para no pisar si el usuario ya cambió manualmente
    public bool $sellTouched = false;
    public bool $buyTouched = false;

    public $showPriceModal = false;
    public $modalRule = [
        'pricelist_id' => '',
        'compute_method' => 'fixed',
        'fixed_price' => 0,
        'percent_discount' => 0,
        'min_qty' => 0,
        'date_start' => '',
        'date_end' => '',
    ];


    public function mount(): void
    {
        $this->tab = 'general';
        $this->attributeLines = []; // sin atributos al inicio
        $this->recalcVariants();

        $this->catalogAttributes = Attribute::query()
            ->where('state', true)
            ->with(['values' => function ($q) {
                $q->where('active', true)->orderBy('sort_order');
            }])
            ->orderBy('order')
            ->get()
            ->toArray();

        //$this->attributeLines = [];
        //$this->recalcVariants();

        // Taxes
        $this->taxOptions = Tax::query()
            ->where('active', true)
            ->orderBy('sequence')
            ->get(['id', 'name', 'amount', 'amount_type'])
            ->map(fn($t) => $t->toArray())
            ->all();

        // Detractions
        $this->detractionOptions = Detraction::query()
            ->where('active', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'rate'])
            ->map(fn($d) => $d->toArray())
            ->all();

        // seasons
        $this->seasonOptions = Season::query()
            ->where('active', true)
            ->orderBy('order')
            ->get(['id', 'order', 'name'])
            ->map(fn($d) => $d->toArray())
            ->all();



        // Brands
        $this->brandOptions = Brand::query()
            ->where('state', true)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($b) => $b->toArray())
            ->all();

        // Modellos (vacío hasta escoger marca)
        $this->modelloOptions = [];



        // Categorías + UoMs (para el select principal)
        $this->uomCategories = UomCategory::query()
            ->where('active', true)
            ->with(['uoms' => function ($q) {
                $q->where('active', true)->orderBy('sort_order')->orderBy('name');
            }])
            ->orderBy('name')
            ->get()
            ->map(fn($cat) => [
                'id' => $cat->id,
                'name' => $cat->name,
                'uoms' => $cat->uoms->map(fn($u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                    'symbol' => $u->symbol,
                ])->toArray(),
            ])->toArray();

        // Si ya hay uom_id, cargar opciones compra
        $this->refreshPurchaseUoms();

        $tree = Category::whereNull('parent_id')
            ->orderBy('order')
            ->orderBy('name')
            ->with('childrenRecursive')
            ->get();

        $this->categoryOptions = $this->flattenCategories($tree);


        $this->accountOptions = Account::query()
            ->where('isrecord', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name'])
            ->map(fn($a) => [
                'id' => $a->id,
                'label' => trim(($a->code ? $a->code . ' - ' : '') . $a->name),
            ])
            ->all();




        $settings = DB::table('account_settings')->where('active', true)->first();

        $this->defaultSellGoodsId   = $settings->default_income_goods_account_id   ?? $settings->default_income_account_id ?? null;
        $this->defaultSellServiceId = $settings->default_income_service_account_id ?? $settings->default_income_account_id ?? null;

        $this->defaultBuyGoodsId    = $settings->default_expense_goods_account_id  ?? $settings->default_expense_account_id ?? null;
        $this->defaultBuyServiceId  = $settings->default_expense_service_account_id ?? $settings->default_expense_account_id ?? null;

        // Set inicial según el type actual
        $this->applyAccountDefaultsByType();

        $this->posCategoryOptions = PosCategory::query()
            ->where('state', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($c) => $c->toArray())
            ->all();

        $this->additionalProductOptions = ProductTemplate::query()
            ->where('active', true)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($p) => $p->toArray())
            ->all();


        $tree = \App\Models\PosCategory::query()
            ->whereNull('parent_id')
            ->where('state', true)
            ->with('childrenRecursive')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $this->posCategoryOptions = $this->flattenPosCategories($tree);

        $this->refreshFilteredPosCategories();

        $this->additionalProductOptions = \App\Models\ProductTemplate::query()
            ->where('active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug'])
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
            ])->all();

        $this->refreshFilteredAdditionalProducts();

        //carga las listas de precios disponibles
        $this->allPricelists = \App\Models\Pricelist::where('state', true)
            ->orderBy('name')
            ->get(['id', 'name', 'currency_id'])
            ->toArray();
    }

    // Para mostrar los planes en el select
    public function getPlansProperty()
    {
        return SubscriptionPlan::where('active', true)->get();
    }



    public function updatedType($value): void
    {
        // cada vez que cambie goods/service/combo
        $this->applyAccountDefaultsByType();
    }

    public function updatedAccountSellId(): void
    {
        $this->sellTouched = true;
    }

    public function updatedAccountBuyId(): void
    {
        $this->buyTouched = true;
    }

    private function applyAccountDefaultsByType(): void
    {
        $isService = ($this->type === 'service');

        $sellDefault = $isService
            ? $this->defaultSellServiceId
            : $this->defaultSellGoodsId; // goods y combo usan goods

        $buyDefault = $isService
            ? $this->defaultBuyServiceId
            : $this->defaultBuyGoodsId;

        // Solo setear si el usuario NO lo tocó (para no fastidiar)
        if (!$this->sellTouched) {
            $this->account_sell_id = $sellDefault;
        }
        if (!$this->buyTouched) {
            $this->account_buy_id = $buyDefault;
        }

        // (Opcional) si quieres que al cambiar type siempre se resetee aunque el usuario tocó:
        // $this->account_sell_id = $sellDefault;
        // $this->account_buy_id = $buyDefault;
    }




    public function updatedBrandId($value): void
    {
        $this->modello_id = null;

        if (!$value) {
            $this->modelloOptions = [];
            return;
        }

        $this->modelloOptions = Modello::query()
            ->where('brand_id', $value)
            ->where('state', true)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($m) => $m->toArray())
            ->all();
    }


    private function flattenCategories($categories, int $level = 0): array
    {
        $out = [];

        foreach ($categories as $cat) {
            $out[] = [
                'id' => $cat->id,
                'label' => str_repeat('— ', $level) . $cat->name,
            ];

            if ($cat->childrenRecursive && $cat->childrenRecursive->count()) {
                $out = array_merge($out, $this->flattenCategories($cat->childrenRecursive, $level + 1));
            }
        }

        return $out;
    }




    public function updatedUomId($value): void
    {
        // Si cambió la UoM base, resetea la de compra y recarga opciones
        $this->uom_po_id = null;
        $this->refreshPurchaseUoms();
    }

    private function refreshPurchaseUoms(): void
    {
        if (! $this->uom_id) {
            $this->uomPurchaseOptions = [];
            return;
        }

        $base = Uom::query()->find($this->uom_id);

        if (! $base) {
            $this->uomPurchaseOptions = [];
            return;
        }

        // Solo UoMs de la MISMA categoría (como Odoo)
        $this->uomPurchaseOptions = Uom::query()
            ->where('active', true)
            ->where('uom_category_id', $base->uom_category_id)
            ->orderByRaw("FIELD(uom_type, 'reference', 'bigger', 'smaller')") // opcional
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'symbol'])
            ->toArray();
    }



    public function addAttributeLine(): void
    {
        $this->attributeLines[] = [
            'attribute_id' => null,
            'value_ids' => [],
        ];

        $this->tab = 'attributes';
        $this->recalcVariants();
    }

    public function removeAttributeLine(int $index): void
    {
        unset($this->attributeLines[$index]);
        $this->attributeLines = array_values($this->attributeLines);

        $this->recalcVariants();
    }

    public function updatedAttributeLines(): void
    {
        $this->recalcVariants();
    }

    private function getSelectedValuesByAttributeFromLines(): array
    {
        $selected = [];

        foreach ($this->attributeLines as $line) {
            $attrId = (int) ($line['attribute_id'] ?? 0);
            $vals = $line['value_ids'] ?? [];
            $vals = array_values(array_filter(array_map('intval', $vals)));

            if ($attrId > 0 && count($vals) > 0) {
                $selected[$attrId] = $vals;
            }
        }

        ksort($selected);
        return $selected;
    }

    /* private function recalcVariants(): void
    {
        $selectedByAttr = $this->getSelectedValuesByAttributeFromLines();

        if (empty($selectedByAttr)) {
            $this->variants_count = 1;
            $this->variant_preview = ['Default'];
            return;
        }

        $groups = array_values($selectedByAttr);

        $count = 1;
        foreach ($groups as $g) {
            $count *= count($g);
        }
        $this->variants_count = max(1, $count);

        $combos = $this->cartesian($groups);
        $combos = array_slice($combos, 0, $this->preview_limit);

        $valueIds = collect($combos)->flatten()->unique()->values()->all();
        $names = \App\Models\AttributeValue::whereIn('id', $valueIds)->pluck('name', 'id')->toArray();

        $this->variant_preview = array_map(function ($combo) use ($names) {
            $parts = array_map(fn($id) => $names[$id] ?? '?', $combo);
            return implode(' - ', $parts);
        }, $combos);
    } */

    private function recalcVariants(): void
    {
        $selectedByAttr = $this->getSelectedValuesByAttributeFromLines();

        if (empty($selectedByAttr)) {
            $this->variants_count = 1;
            $this->variant_preview = ['Default'];
            return;
        }

        $groups = array_values($selectedByAttr);

        $count = 1;
        foreach ($groups as $g) {
            $count *= count($g);
        }
        $this->variants_count = max(1, $count);

        $combos = $this->cartesian($groups);
        $combos = array_slice($combos, 0, $this->preview_limit);

        $valueIds = collect($combos)->flatten()->unique()->values()->all();

        // ✅ trae todo lo necesario de una
        $values = AttributeValue::with('attribute')
            ->whereIn('id', $valueIds)
            ->get()
            ->keyBy('id');

        $this->variant_preview = array_map(function ($combo) use ($values) {
            $parts = [];

            foreach ($combo as $id) {
                $v = $values->get($id);
                $parts[] = $v?->name ?? '?';
                // si quieres mostrar atributo también:
                // $parts[] = ($v?->attribute?->name ?? 'Attr') . ': ' . ($v?->name ?? '?');
            }

            return implode(' - ', $parts);
        }, $combos);
    }



    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }


    private function cartesian(array $arrays): array
    {
        $result = [[]];
        foreach ($arrays as $propertyValues) {
            $tmp = [];
            foreach ($result as $resultItem) {
                foreach ($propertyValues as $propertyValue) {
                    $tmp[] = array_merge($resultItem, [$propertyValue]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }




    public function render()
    {

        $attributes = Attribute::where('state', true)
            ->with(['values' => function ($q) {
                $q->where('active', true)->orderByRaw('COALESCE(sort_order, 999999) asc')->orderBy('name');
            }])
            ->orderByRaw('COALESCE(`order`, 999999) asc')
            ->orderBy('name')
            ->get();

        return view('livewire.admin.products.product-create', [
            'attributes' => $attributes,
        ]);
    }

    public function toggleAttribute(int $attributeId): void
    {
        // Si lo desmarcan, limpiamos sus valores
        if (empty($this->selectedAttributes[$attributeId])) {
            unset($this->selectedValues[$attributeId]);
        }

        $this->tab = 'attributes';
        $this->recalcVariants();
    }

    public function updatedSelectedValues(): void
    {
        $this->recalcVariants();
    }

    public function updatedSelectedAttributes(): void
    {
        $this->recalcVariants();
    }



    public function save()
    {
        $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'type' => ['required', 'in:goods,service,combo'],
            'base_price_sale' => ['nullable', 'numeric', 'min:0'],
            'sku_prefix' => ['nullable', 'string', 'max:20'],
            'sale_ok' => ['boolean'],
            'purchase_ok' => ['boolean'],
            'pos_ok' => ['boolean'],

            'pos_category_ids' => [
                Rule::requiredIf($this->pos_ok),
                'array'
            ],
            'pos_category_ids.*' => ['integer', 'exists:pos_categories,id'],

            'additional_product_ids' => ['array'],
            'additional_product_ids.*' => ['integer', 'exists:product_templates,id'],


            'active' => ['boolean'],
            'category_id' => ['nullable', 'exists:categories,id'],
            //'tax_id' => ['nullable', 'exists:taxes,id'],
            'sale_tax_ids' => ['array'],
            'sale_tax_ids.*' => ['integer', 'exists:taxes,id'],

            'purchase_tax_ids' => ['array'],
            'purchase_tax_ids.*' => ['integer', 'exists:taxes,id'],

            'detraction_id' => ['nullable', 'exists:detractions,id'],

            'brand_id' => ['nullable', 'exists:brands,id'],
            'modello_id' => ['nullable', 'exists:modellos,id'],

            'tracking' => ['nullable', 'in:quantity,serial,lot'],

            'account_sell_id' => ['required', 'exists:accounts,id'],
            'account_buy_id'  => ['required', 'exists:accounts,id'],

            // NUEVAS VALIDACIONES PARA SUSCRIPCIÓN
            'is_subscription' => ['boolean'],
            'subscription_plan_id' => [
                Rule::requiredIf($this->is_subscription),
                'nullable',
                'exists:subscription_plans,id'
            ],
            'recurring_price' => [
                Rule::requiredIf($this->is_subscription),
                'nullable',
                'numeric',
                'min:0'
            ],

        ]);

        if ($this->modello_id && $this->brand_id) {
            $ok = Modello::where('id', $this->modello_id)
                ->where('brand_id', $this->brand_id)
                ->exists();

            if (!$ok) {
                $this->addError('modello_id', 'El modelo no pertenece a la marca seleccionada.');
                return;
            }
        }


        $name = trim($this->name);

        // slug único
        $slug = Str::slug($name);
        $baseSlug = $slug;
        $i = 2;
        while (ProductTemplate::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i++;
        }

        // 1) Crear template (sin precio)
        /* $template = ProductTemplate::create([
            'name' => $name,
            'slug' => $slug,
            'type' => $this->type,
            'sale_ok' => $this->sale_ok,
            'purchase_ok' => $this->purchase_ok,
            'pos_ok' => $this->pos_ok,
            'active' => $this->active,

            'category_id' => $this->category_id ?: null,

            'uom_id' => $this->uom_id ?: null,
            'uom_po_id' => $this->uom_po_id ?: null,
        ]); */

        $template = ProductTemplate::create([
            'name' => $name,
            'slug' => $slug,
            'type' => $this->type,

            'sale_ok' => $this->sale_ok,
            'purchase_ok' => $this->purchase_ok,
            'pos_ok' => $this->pos_ok,
            'active' => $this->active,

            // nuevos
            'category_id' => $this->category_id,
            'uom_id' => $this->uom_id,
            'uom_po_id' => $this->uom_po_id,

            //'tax_id' => $this->tax_id,
            'detraction_id' => $this->detraction_id,

            'brand_id' => $this->brand_id,
            'modello_id' => $this->modello_id,

            'account_sell_id' => $this->account_sell_id, // ✅ cuenta contable VENTAS (70xx)
            'account_buy_id'  => $this->account_buy_id,  // ✅ cuenta contable COMPRAS (60xx)

            // CAMPOS DE SUSCRIPCIÓN (MAESTROS)
            'is_subscription' => $this->is_subscription,
            'subscription_plan_id' => $this->is_subscription ? $this->subscription_plan_id : null,
            'recurring_price' => $this->is_subscription ? $this->recurring_price : null,

        ]);


        $template->posCategories()->sync(
            $this->pos_ok
                ? collect($this->pos_category_ids)->filter()->map(fn($x) => (int) $x)->values()->all()
                : []
        );

        $template->additionalProducts()->sync(
            $this->pos_ok
                ? collect($this->additional_product_ids)
                ->filter(fn($id) => (int) $id !== (int) $template->id)
                ->mapWithKeys(fn($id) => [
                    (int) $id => ['sequence' => 10, 'active' => true]
                ])
                ->all()
                : []
        );


        // Impuestos (muchos a muchos)
        $template->saleTaxes()->sync(
            collect($this->sale_tax_ids)->filter()->map(fn($x) => (int)$x)->values()->all()
        );

        $template->purchaseTaxes()->sync(
            collect($this->purchase_tax_ids)->filter()->map(fn($x) => (int)$x)->values()->all()
        );




        // 2) Procesar selección (atributos->valores)
        $selectedByAttr = $this->getSelectedValuesByAttribute();


        // Creamos líneas por atributo
        foreach ($selectedByAttr as $attrId => $valueIds) {

            $line = \App\Models\ProductTemplateAttribute::create([
                'product_template_id' => $template->id,
                'attribute_id' => $attrId,
            ]);

            // Guardamos valores permitidos para ese atributo dentro del producto
            $line->values()->sync($valueIds);
        }

        // 3) Si no hay valores => SOLO variante default
        if (empty($selectedByAttr)) {
            $this->createDefaultVariant($template);
            return redirect()->route('admin.products.index')
                ->with('swal', ['icon' => 'success', 'title' => 'Listo', 'text' => 'Producto creado (sin variantes)']);
        }

        // 4) Crear variantes por combinaciones
        $combinations = $this->cartesian(array_values($selectedByAttr));
        $first = true;

        foreach ($combinations as $valueIds) {
            sort($valueIds);

            // Cargar valores + atributo para construir key / nombre / extra
            $values = AttributeValue::with('attribute')
                ->whereIn('id', $valueIds)
                ->get()
                ->keyBy('id');

            $pairs = [];
            $nameParts = [];
            $extraTotal = 0;

            foreach ($valueIds as $vid) {
                $v = $values[$vid] ?? null;
                if (!$v) continue;

                $pairs[] = $v->attribute_id . ':' . $v->id;
                $nameParts[] = $v->name;
                $extraTotal += (float) $v->extra_price;
            }

            $combinationKey = implode('|', $pairs);
            $variantName = implode(' - ', $nameParts);

            // SKU (simple + único)
            $sku = $this->buildSku($template->id, $nameParts);

            $barcode = blank($this->barcode) ? null : trim($this->barcode);
            $reference = blank($this->reference) ? null : trim($this->reference);

            $variant = ProductVariant::create([
                'product_template_id' => $template->id,
                'sku' => $sku,
                //'barcode' => $this->barcode ?: null,
                // si hay variantes: barcode solo para default (o null para todas)
                'barcode' => $first ? $barcode : null,
                'reference' => $reference,

                // precio SOLO en variantes:
                'price_sale' => (float) $this->base_price_sale + $extraTotal,
                'price_wholesale' => null,
                'price_purchase' => null,

                'active' => true,
                'is_default' => $first,

                'combination_key' => $combinationKey,
                'variant_name' => $variantName,

                'tracking' => $this->tracking,


            ]);

            // Pivot
            $variant->values()->sync($valueIds);

            $first = false;
        }


        return redirect()->route('admin.products.index')
            ->with('swal', ['icon' => 'success', 'title' => 'Listo', 'text' => 'Producto creado con variantes']);
    }



    public function updatedPosOk($value): void
    {
        if (! $value) {
            $this->pos_category_ids = [];
            $this->additional_product_ids = [];
        }
    }




    private function getSelectedValuesByAttribute(): array
    {
        $attributeIds = array_keys(array_filter($this->selectedAttributes));
        $selectedByAttr = [];

        foreach ($attributeIds as $attrId) {
            $vals = array_keys(array_filter($this->selectedValues[$attrId] ?? []));
            if (!empty($vals)) {
                $selectedByAttr[$attrId] = $vals;
            }
        }

        // Orden estable por attribute_id para que combination_key sea consistente
        ksort($selectedByAttr);

        return $selectedByAttr;
    }

    private function createDefaultVariant(ProductTemplate $template): void
    {
        $sku = $this->buildSku($template->id, ['DEFAULT']);

        $barcode = blank($this->barcode) ? null : trim($this->barcode);
        $reference = blank($this->reference) ? null : trim($this->reference);

        ProductVariant::create([
            'product_template_id' => $template->id,
            'sku' => $sku,
            'barcode' => $barcode,
            'reference' => $reference,


            'price_sale' => (float) $this->base_price_sale,
            'price_wholesale' => null,
            'price_purchase' => null,

            'active' => true,
            'is_default' => true,

            'combination_key' => null,
            'variant_name' => 'Default',
        ]);
    }

    private function buildSku(int $templateId, array $parts): string
    {
        $prefix = trim($this->sku_prefix);
        $prefix = $prefix !== '' ? strtoupper($prefix) . '-' : 'PRD-';

        $code = implode('-', array_map(fn($p) => strtoupper(Str::slug($p)), $parts));
        $sku = $prefix . $templateId . '-' . $code;

        // asegurar único
        $base = $sku;
        $i = 2;
        while (ProductVariant::where('sku', $sku)->exists()) {
            $sku = $base . '-' . $i++;
        }

        return $sku;
    }

    private function flattenPosCategories($categories, int $level = 0): array
    {
        $out = [];

        foreach ($categories as $cat) {
            $out[] = [
                'id' => $cat->id,
                'name' => $cat->name,
                'complete_name' => $cat->complete_name ?: $cat->name,
                'label' => str_repeat('— ', $level) . $cat->name,
                'level' => $level,
            ];

            if ($cat->childrenRecursive && $cat->childrenRecursive->count()) {
                $out = array_merge($out, $this->flattenPosCategories($cat->childrenRecursive, $level + 1));
            }
        }

        return $out;
    }

    public function updatedPosCategorySearch(): void
    {
        $this->refreshFilteredPosCategories();
    }

    private function refreshFilteredPosCategories(): void
    {
        $search = trim(mb_strtolower($this->posCategorySearch));

        $items = $this->posCategoryOptions ?? [];

        if ($search === '') {
            $this->filteredPosCategories = array_values(array_filter($items, function ($item) {
                return !in_array($item['id'], $this->pos_category_ids);
            }));

            return;
        }

        $this->filteredPosCategories = array_values(array_filter($items, function ($item) use ($search) {
            return !in_array($item['id'], $this->pos_category_ids)
                && (
                    str_contains(mb_strtolower($item['name']), $search) ||
                    str_contains(mb_strtolower($item['complete_name']), $search)
                );
        }));
    }

    public function updatedAdditionalProductSearch(): void
    {
        $this->refreshFilteredAdditionalProducts();
    }

    private function refreshFilteredAdditionalProducts(): void
    {
        $search = trim(mb_strtolower($this->additionalProductSearch));

        $items = $this->additionalProductOptions ?? [];

        if ($search === '') {
            $this->filteredAdditionalProducts = array_values(array_filter($items, function ($item) {
                return !in_array($item['id'], $this->additional_product_ids);
            }));

            return;
        }

        $this->filteredAdditionalProducts = array_values(array_filter($items, function ($item) use ($search) {
            return !in_array($item['id'], $this->additional_product_ids)
                && str_contains(mb_strtolower($item['name']), $search);
        }));
    }

    public function addPosCategory(int $id): void
    {
        if (!in_array($id, $this->pos_category_ids)) {
            $this->pos_category_ids[] = $id;
        }

        $this->posCategorySearch = '';
        $this->refreshFilteredPosCategories();
    }

    public function removePosCategory(int $id): void
    {
        $this->pos_category_ids = array_values(array_filter(
            $this->pos_category_ids,
            fn($item) => (int) $item !== $id
        ));

        $this->refreshFilteredPosCategories();
    }


    public function addAdditionalProduct(int $id): void
    {
        if (!in_array($id, $this->additional_product_ids) && (int) $id !== 0) {
            $this->additional_product_ids[] = $id;
        }

        $this->additionalProductSearch = '';
        $this->refreshFilteredAdditionalProducts();
    }

    public function removeAdditionalProduct(int $id): void
    {
        $this->additional_product_ids = array_values(array_filter(
            $this->additional_product_ids,
            fn($item) => (int) $item !== $id
        ));

        $this->refreshFilteredAdditionalProducts();
    }

    public function getSelectedPosCategoryItemsProperty(): array
    {
        $all = collect($this->posCategoryOptions);

        return $all->whereIn('id', $this->pos_category_ids)->values()->all();
    }

    public function getSelectedAdditionalProductItemsProperty(): array
    {
        $all = collect($this->additionalProductOptions);

        return $all->whereIn('id', $this->additional_product_ids)->values()->all();
    }

    public function getCanCreatePosCategoryProperty(): bool
    {
        $name = trim($this->posCategorySearch);

        if ($name === '') {
            return false;
        }

        $exists = \App\Models\PosCategory::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->exists();

        return ! $exists;
    }

    public function createPosCategory(): void
    {


        $name = trim($this->posCategorySearch);

        if ($name === '') {
            return;
        }


        if (mb_strlen($name) < 2) {
            $this->addError('posCategorySearch', 'La categoría debe tener al menos 2 caracteres.');
            return;
        }

        $this->resetErrorBag('posCategorySearch');


        $exists = \App\Models\PosCategory::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->first();

        if ($exists) {
            if (!in_array($exists->id, $this->pos_category_ids)) {
                $this->pos_category_ids[] = $exists->id;
            }

            $this->posCategorySearch = '';
            $this->refreshFilteredPosCategories();
            return;
        }

        $baseSlug = \Illuminate\Support\Str::slug($name);
        $slug = $baseSlug;
        $i = 2;

        while (\App\Models\PosCategory::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i++;
        }

        $category = \App\Models\PosCategory::create([
            'name' => $name,
            'slug' => $slug,
            'parent_id' => null, // solo primer nivel
            'complete_name' => $name,
            'state' => true,
            'order' => 0,
            'image' => null,
        ]);

        // Recargar catálogo completo
        $tree = \App\Models\PosCategory::query()
            ->whereNull('parent_id')
            ->where('state', true)
            ->with('childrenRecursive')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $this->posCategoryOptions = $this->flattenPosCategories($tree);

        // Seleccionarla automáticamente
        if (!in_array($category->id, $this->pos_category_ids)) {
            $this->pos_category_ids[] = $category->id;
        }

        $this->posCategorySearch = '';
        $this->refreshFilteredPosCategories();
    }

    //para lista de precios
    public function getPriceRulePreviewProperty()
    {
        $query = \App\Models\PricelistItem::query()
            ->with([
                'pricelist:id,name,currency_id',
                'category:id,name',
            ])
            ->where('active', true)
            ->where(function ($q) {
                $q->where('applied_on', 'all');

                if ($this->category_id) {
                    $q->orWhere(function ($sub) {
                        $sub->where('applied_on', 'category')
                            ->where('category_id', $this->category_id);
                    });
                }
            })
            ->orderBy('sequence')
            ->orderBy('min_qty')
            ->get();

        return $query->map(function ($item) {
            return [
                'pricelist' => $item->pricelist->name ?? '—',
                'applied_on' => $item->applied_on,
                'category' => $item->category->name ?? '—',
                'min_qty' => $item->min_qty,
                'compute_method' => $item->compute_method,
                'fixed_price' => $item->fixed_price,
                'percent_discount' => $item->percent_discount,
                'base' => $item->base,
                'price_multiplier' => $item->price_multiplier,
                'price_surcharge' => $item->price_surcharge,
                'rounding' => $item->rounding,
                'date_start' => $item->date_start,
                'date_end' => $item->date_end,
            ];
        })->toArray();
    }


    // Para mostrar las reglas en la tabla
    public function getProductPriceRulesProperty()
    {
        $rules = [];

        // Si el producto ya existe (Edición), traer de la base de datos
        if ($this->product_id) {
            $rules = PricelistItem::where('product_template_id', $this->product_id)
                ->with('pricelist')
                ->get()
                ->toArray();
        }

        // IMPORTANTE: Combinar con los temporales creados en esta sesión
        // Usamos array_merge para que la tabla muestre ambos
        return array_merge($rules, $this->temporary_prices);
    }


    public function openPriceRuleModal()
    {
        // Limpiamos la regla anterior
        $this->modalRule = [
            'pricelist_id' => '',
            'compute_method' => 'fixed',
            'fixed_price' => 0,
            'percent_discount' => 0,
            'min_qty' => 0,
            'date_start' => '',
            'date_end' => '',
        ];
        $this->showPriceModal = true;
    }



    /*  public function savePriceRule()
    {
        $this->validate([
            'modalRule.pricelist_id' => 'required',
            'modalRule.compute_method' => 'required',
        ]);

        PricelistItem::create([
            'pricelist_id' => $this->modalRule['pricelist_id'],
            'applied_on' => 'template',
            'product_template_id' => $this->product_id, // El ID del producto actual
            'compute_method' => $this->modalRule['compute_method'],
            'fixed_price' => $this->modalRule['fixed_price'],
            'percent_discount' => $this->modalRule['percent_discount'],
            'min_qty' => $this->modalRule['min_qty'] ?? 0,
            'date_start' => $this->modalRule['date_start'] ?: null,
            'date_end' => $this->modalRule['date_end'] ?: null,
        ]);

        $this->showPriceModal = false;
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Regla de precio agregada']);
    } */

    public function savePriceRule()
    {
        // 1. Validar datos mínimos
        if (empty($this->modalRule['pricelist_id'])) {
            // Opcional: enviar una alerta si no seleccionó lista
            return;
        }

        // 2. Obtener el nombre de la lista para mostrarlo en la tabla (ya que no hay relación de DB aún)
        $pricelistName = 'N/A';
        foreach ($this->allPricelists as $pl) {
            if ($pl['id'] == $this->modalRule['pricelist_id']) {
                $pricelistName = $pl['name'];
                break;
            }
        }

        // 3. Crear el set de datos para la tabla
        $newRule = [
            'pricelist_id'     => $this->modalRule['pricelist_id'],
            // Creamos una estructura que imite al modelo para que la vista no falle
            'pricelist'        => ['name' => $pricelistName],
            'compute_method'   => $this->modalRule['compute_method'],
            'fixed_price'      => $this->modalRule['fixed_price'] ?? 0,
            'percent_discount' => $this->modalRule['percent_discount'] ?? 0,
            'min_qty'          => $this->modalRule['min_qty'] ?? 0,
        ];

        // 4. Agregar al array temporal
        $this->temporary_prices[] = $newRule;

        // 5. Cerrar modal y limpiar
        $this->showPriceModal = false;
        $this->reset('modalRule');

        // Opcional: resetear valores por defecto del modal
        $this->modalRule['compute_method'] = 'fixed';
    }


    public function removePriceRule($index, $ruleId = null)
    {
        // 1. Si la regla tiene un ID, significa que ya existe en la base de datos
        if ($ruleId) {
            $rule = \App\Models\PricelistItem::find($ruleId);
            if ($rule) {
                $rule->delete();
                $this->dispatch('notify', ['type' => 'success', 'message' => 'Regla eliminada de la base de datos']);
            }
        } else {
            // 2. Si no tiene ID, es una regla temporal (producto nuevo)
            // La eliminamos del array usando el índice
            if (isset($this->temporary_prices[$index])) {
                unset($this->temporary_prices[$index]);
                // Reindexamos el array para evitar huecos en los índices
                $this->temporary_prices = array_values($this->temporary_prices);
                $this->dispatch('notify', ['type' => 'info', 'message' => 'Regla temporal removida']);
            }
        }
    }
}
