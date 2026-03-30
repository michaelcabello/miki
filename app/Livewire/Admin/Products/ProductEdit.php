<?php

namespace App\Livewire\Admin\Products;

use App\Models\Account;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Detraction;
use App\Models\Modello;
use App\Models\PosCategory;
use App\Models\Pricelist;
use App\Models\PricelistItem;
use App\Models\ProductTemplate;
use App\Models\ProductVariant;
use App\Models\Season;
use App\Models\SubscriptionPlan;
use App\Models\Tax;
use App\Models\Uom;
use App\Models\UomCategory;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Edición de plantilla de producto — espejo exacto del flujo de creación.
 *
 * Patrón Odoo aplicado:
 *  - El template guarda datos maestros (nombre, tipo, marca, cuentas, etc.)
 *  - Las variantes guardan precios + SKU individuales
 *  - En edición los atributos/combinaciones existentes se muestran read-only
 *  - Impuestos, categorías POS y productos adicionales se sincronizan (sync)
 *
 * php artisan make:livewire Admin/Products/ProductEdit
 */
#[Title('Editar Producto')]
class ProductEdit extends Component
{
    // ── Model binding ──────────────────────────────────────────
    public ProductTemplate $product_template;

    // ── Tabs ───────────────────────────────────────────────────
    public string $tab = 'general';

    // ── Campos del template ────────────────────────────────────
    public string $name        = '';
    public string $type        = 'goods';
    public bool   $sale_ok     = true;
    public bool   $purchase_ok = false;
    public bool   $pos_ok      = false;
    public bool   $active      = true;

    // Rastreo de inventario
    public ?string $tracking = 'quantity'; // quantity | serial | lot

    // Identificación
    public ?string $barcode   = null; // barcode de la variante default
    public ?string $reference = null; // SKU/referencia interna de la variante default

    // Precio base (variante default)
    public $base_price_sale = 0;

    // UoM
    public ?int $uom_id    = null;
    public ?int $uom_po_id = null;

    // Clasificación
    public ?int $category_id   = null;
    public ?int $brand_id      = null;
    public ?int $modello_id    = null;
    public ?int $season_id     = null;
    public ?int $detraction_id = null;

    // Cuentas contables
    public ?int $account_sell_id = null;
    public ?int $account_buy_id  = null;

    // Impuestos (multi-select)
    public array $sale_tax_ids     = [];
    public array $purchase_tax_ids = [];

    // Suscripciones
    public bool   $is_recurring        = false;
    public ?int   $subscription_plan_id = null;
    public        $recurring_price      = null;

    // Web / SEO
    public ?string $short_description  = null;
    public ?string $long_description   = null;
    public ?string $title_google       = null;
    public ?string $description_google = null;
    public ?string $keywords_google    = null;

    // Punto de Venta
    public array  $pos_category_ids      = [];
    public array  $additional_product_ids = [];

    // ── Variantes existentes (solo edición de precios/barcode) ─
    // variants[variant_id] = ['price_sale', 'price_wholesale', 'price_purchase', 'barcode', 'active', 'label', 'sku']
    public array $variants = [];

    // ── Lista de precios ───────────────────────────────────────
    public array $dbPricelistItems = []; // Reglas guardadas en DB
    public array $allPricelists    = []; // Para el select del modal
    public bool  $showPriceModal   = false;
    public array $modalRule        = [
        'pricelist_id'     => '',
        'compute_method'   => 'fixed',
        'fixed_price'      => 0,
        'percent_discount' => 0,
        'min_qty'          => 1,
        'date_start'       => '',
        'date_end'         => '',
    ];

    // ── Catálogos (arrays planos para la vista) ────────────────
    public array $uomCategories      = [];
    public array $uomPurchaseOptions = [];
    public array $categoryOptions    = [];
    public array $brands             = [];
    public array $modelloOptions     = [];
    public array $seasons            = [];
    public array $detractionOptions  = [];
    public array $taxOptions         = [];
    public array $subscriptionPlans  = [];
    public array $accountOptions     = [];
    public array $posCategoryOptions = [];
    public array $additionalProductOptions = [];

    // Control de cuentas por defecto
    public ?int $defaultSellGoodsId    = null;
    public ?int $defaultSellServiceId  = null;
    public ?int $defaultBuyGoodsId     = null;
    public ?int $defaultBuyServiceId   = null;
    public bool $sellTouched           = false;
    public bool $buyTouched            = false;

    // Búsquedas live
    public string $posCategorySearch       = '';
    public array  $filteredPosCategories   = [];
    public string $additionalProductSearch = '';
    public array  $filteredAdditionalProducts = [];

    // ── Ciclo de vida ──────────────────────────────────────────

    public function mount(ProductTemplate $product_template): void
    {
        $this->product_template = $product_template;

        // Hidrata campos del template
        $this->name        = (string) $product_template->name;
        $this->type        = (string) $product_template->type;
        $this->sale_ok     = (bool)   $product_template->sale_ok;
        $this->purchase_ok = (bool)   $product_template->purchase_ok;
        $this->pos_ok      = (bool)   $product_template->pos_ok;
        $this->active      = (bool)   $product_template->active;
        $this->is_recurring = (bool)  $product_template->is_recurring;

        $this->uom_id             = $product_template->uom_id;
        $this->uom_po_id          = $product_template->uom_po_id;
        $this->category_id        = $product_template->category_id;
        $this->brand_id           = $product_template->brand_id;
        $this->modello_id         = $product_template->modello_id;
        $this->season_id          = $product_template->season_id;
        $this->detraction_id      = $product_template->detraction_id;
        $this->account_sell_id    = $product_template->account_sell_id;
        $this->account_buy_id     = $product_template->account_buy_id;
        $this->subscription_plan_id = $product_template->subscription_plan_id;
        $this->recurring_price    = $product_template->recurring_price;

        // Campos web / SEO
        $this->short_description  = $product_template->short_description;
        $this->long_description   = $product_template->long_description;
        $this->title_google       = $product_template->title_google;
        $this->description_google = $product_template->description_google;
        $this->keywords_google    = $product_template->keywords_google;

        // Hidrata variantes
        $this->loadVariants();

        // Hidrata impuestos (multi-select)
        $this->sale_tax_ids = $product_template
            ->saleTaxes()
            ->pluck('taxes.id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->toArray();

        $this->purchase_tax_ids = $product_template
            ->purchaseTaxes()
            ->pluck('taxes.id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->toArray();

        // Hidrata categorías POS
        $this->pos_category_ids = $product_template
            ->posCategories()
            ->pluck('pos_categories.id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->toArray();

        // Hidrata productos adicionales
        $this->additional_product_ids = $product_template
            ->additionalProducts()
            ->pluck('additional_product_template_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->toArray();

        // Carga tracking + barcode de la variante default
        $default = $product_template->variants()->where('is_default', true)->first();
        if ($default) {
            $this->tracking  = $default->tracking ?? 'quantity';
            $this->barcode   = $default->barcode;
            $this->reference = $default->reference;
            $this->base_price_sale = $default->price_sale ?? 0;
        }

        // Carga catálogos
        $this->loadCatalogs();

        // Carga reglas de precio existentes
        $this->loadPricelistItems();
    }

    // ── Carga de variantes ─────────────────────────────────────

    private function loadVariants(): void
    {
        $this->variants = $this->product_template
            ->variants()
            ->with(['values.attribute:id,name'])
            ->orderByDesc('is_default')
            ->orderBy('variant_name')
            ->get()
            ->mapWithKeys(fn ($v) => [
                $v->id => [
                    'sku'             => $v->sku ?? '',
                    'variant_name'    => $v->variant_name ?? 'Default',
                    'is_default'      => (bool) $v->is_default,
                    'active'          => (bool) $v->active,
                    'price_sale'      => $v->price_sale,
                    'price_wholesale' => $v->price_wholesale,
                    'price_purchase'  => $v->price_purchase,
                    'barcode'         => $v->barcode ?? '',
                    'label'           => $v->variant_name ?? 'Default',
                ],
            ])->toArray();
    }

    // ── Carga de reglas de precio ──────────────────────────────

    private function loadPricelistItems(): void
    {
        $this->dbPricelistItems = PricelistItem::query()
            ->where('product_template_id', $this->product_template->id)
            ->where('active', true)
            ->with('pricelist:id,name')
            ->orderBy('sequence')
            ->orderBy('min_qty')
            ->get()
            ->map(fn ($item) => [
                'id'               => $item->id,
                'pricelist_name'   => $item->pricelist->name ?? '—',
                'pricelist_id'     => $item->pricelist_id,
                'compute_method'   => $item->compute_method,
                'fixed_price'      => $item->fixed_price,
                'percent_discount' => $item->percent_discount,
                'min_qty'          => $item->min_qty,
                'date_start'       => $item->date_start,
                'date_end'         => $item->date_end,
            ])->toArray();
    }

    // ── Carga de catálogos ─────────────────────────────────────

    private function loadCatalogs(): void
    {
        // UoMs agrupadas por categoría
        $this->uomCategories = UomCategory::query()
            ->where('active', true)
            ->with(['uoms' => fn ($q) => $q->where('active', true)->orderBy('sort_order')->orderBy('name')])
            ->orderBy('name')
            ->get()
            ->map(fn ($cat) => [
                'id'   => $cat->id,
                'name' => $cat->name,
                'uoms' => $cat->uoms->map(fn ($u) => [
                    'id'     => $u->id,
                    'name'   => $u->name,
                    'symbol' => $u->symbol,
                ])->toArray(),
            ])->toArray();

        // UoMs de compra según la UoM base actual
        $this->refreshPurchaseUoms();

        // Árbol de categorías aplanado
        $tree = Category::whereNull('parent_id')
            ->orderBy('order')->orderBy('name')
            ->with('childrenRecursive')
            ->get();
        $this->categoryOptions = $this->flattenCategories($tree);

        // Marcas activas
        $this->brands = Brand::select('id', 'name')
            ->where('state', true)->orderBy('name')->get()->toArray();

        // Modelos de la marca actual
        $this->refreshModelloOptions();

        // Temporadas
        $this->seasons = Season::select('id', 'name')
            ->where('active', true)->orderBy('name')->get()->toArray();

        // Detracciones
        $this->detractionOptions = Detraction::select('id', 'code', 'name', 'rate')
            ->where('active', true)->orderBy('code')->get()->toArray();

        // Impuestos
        $this->taxOptions = Tax::query()
            ->where('active', true)
            ->orderBy('sequence')
            ->get(['id', 'name', 'amount', 'amount_type'])
            ->map(fn ($t) => $t->toArray())
            ->all();

        // Planes de suscripción
        $this->subscriptionPlans = SubscriptionPlan::select('id', 'name', 'interval_count', 'interval_unit')
            ->where('active', true)->orderBy('order')->orderBy('name')->get()->toArray();

        // Cuentas contables (solo cuentas movimiento)
        $this->accountOptions = Account::query()
            ->where('isrecord', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name'])
            ->map(fn ($a) => [
                'id'    => $a->id,
                'label' => trim(($a->code ? $a->code . ' - ' : '') . $a->name),
            ])->all();

        // Cuentas por defecto desde account_settings
        $settings = DB::table('account_settings')->where('active', true)->first();
        $this->defaultSellGoodsId   = $settings->default_income_goods_account_id   ?? $settings->default_income_account_id  ?? null;
        $this->defaultSellServiceId = $settings->default_income_service_account_id ?? $settings->default_income_account_id  ?? null;
        $this->defaultBuyGoodsId    = $settings->default_expense_goods_account_id  ?? $settings->default_expense_account_id ?? null;
        $this->defaultBuyServiceId  = $settings->default_expense_service_account_id ?? $settings->default_expense_account_id ?? null;

        // Categorías POS
        $posTree = PosCategory::query()
            ->whereNull('parent_id')
            ->where('state', true)
            ->with('childrenRecursive')
            ->orderBy('order')->orderBy('name')
            ->get();
        $this->posCategoryOptions = $this->flattenPosCategories($posTree);
        $this->refreshFilteredPosCategories();

        // Productos adicionales (para POS cross-sell)
        $this->additionalProductOptions = ProductTemplate::query()
            ->where('active', true)
            ->where('id', '!=', $this->product_template->id) // no se agrega a sí mismo
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($p) => ['id' => $p->id, 'name' => $p->name])
            ->all();
        $this->refreshFilteredAdditionalProducts();

        // Listas de precios disponibles
        $this->allPricelists = Pricelist::where('state', true)
            ->orderBy('name')
            ->get(['id', 'name', 'currency_id'])
            ->toArray();
    }

    // ── Observadores reactivos ─────────────────────────────────

    public function updatedUomId(): void
    {
        $this->uom_po_id = null;
        $this->refreshPurchaseUoms();
    }

    public function updatedBrandId(): void
    {
        $this->modello_id = null;
        $this->refreshModelloOptions();
    }

    public function updatedIsRecurring(): void
    {
        // Si desactiva recurrencia, limpia el plan asociado
        if (! $this->is_recurring) {
            $this->subscription_plan_id = null;
            $this->recurring_price      = null;
        }
    }

    public function updatedType(): void
    {
        // Aplica cuentas por defecto según el tipo, solo si el usuario no las tocó
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

    public function updatedPosCategorySearch(): void
    {
        $this->refreshFilteredPosCategories();
    }

    public function updatedAdditionalProductSearch(): void
    {
        $this->refreshFilteredAdditionalProducts();
    }

    // ── Helpers privados ───────────────────────────────────────

    private function refreshPurchaseUoms(): void
    {
        if (! $this->uom_id) {
            $this->uomPurchaseOptions = [];
            return;
        }

        $base = Uom::find($this->uom_id);
        if (! $base) {
            $this->uomPurchaseOptions = [];
            return;
        }

        $this->uomPurchaseOptions = Uom::query()
            ->where('active', true)
            ->where('uom_category_id', $base->uom_category_id)
            ->orderByRaw("FIELD(uom_type, 'reference', 'bigger', 'smaller')")
            ->orderBy('sort_order')->orderBy('name')
            ->get(['id', 'name', 'symbol'])
            ->toArray();
    }

    private function refreshModelloOptions(): void
    {
        if (! $this->brand_id) {
            $this->modelloOptions = [];
            return;
        }

        $this->modelloOptions = Modello::select('id', 'name')
            ->where('brand_id', $this->brand_id)
            ->where('state', true)
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    private function applyAccountDefaultsByType(): void
    {
        $isService   = ($this->type === 'service');
        $sellDefault = $isService ? $this->defaultSellServiceId : $this->defaultSellGoodsId;
        $buyDefault  = $isService ? $this->defaultBuyServiceId  : $this->defaultBuyGoodsId;

        // Solo sobreescribe si el usuario NO tocó manualmente la cuenta
        if (! $this->sellTouched && ! $this->account_sell_id) {
            $this->account_sell_id = $sellDefault;
        }
        if (! $this->buyTouched && ! $this->account_buy_id) {
            $this->account_buy_id = $buyDefault;
        }
    }

    private function flattenCategories($categories, int $level = 0): array
    {
        $out = [];
        foreach ($categories as $cat) {
            $out[] = ['id' => $cat->id, 'label' => str_repeat('— ', $level) . $cat->name];
            if ($cat->childrenRecursive && $cat->childrenRecursive->count()) {
                $out = array_merge($out, $this->flattenCategories($cat->childrenRecursive, $level + 1));
            }
        }
        return $out;
    }

    private function flattenPosCategories($categories, int $level = 0): array
    {
        $out = [];
        foreach ($categories as $cat) {
            $out[] = ['id' => $cat->id, 'label' => str_repeat('— ', $level) . $cat->name];
            if ($cat->childrenRecursive && $cat->childrenRecursive->count()) {
                $out = array_merge($out, $this->flattenPosCategories($cat->childrenRecursive, $level + 1));
            }
        }
        return $out;
    }

    private function refreshFilteredPosCategories(): void
    {
        $search = mb_strtolower(trim($this->posCategorySearch));

        $this->filteredPosCategories = collect($this->posCategoryOptions)
            ->filter(fn ($c) => $search === '' || str_contains(mb_strtolower($c['label']), $search))
            ->reject(fn ($c) => in_array($c['id'], $this->pos_category_ids))
            ->values()
            ->take(10)
            ->all();
    }

    private function refreshFilteredAdditionalProducts(): void
    {
        $search = mb_strtolower(trim($this->additionalProductSearch));

        $this->filteredAdditionalProducts = collect($this->additionalProductOptions)
            ->filter(fn ($p) => $search === '' || str_contains(mb_strtolower($p['name']), $search))
            ->reject(fn ($p) => in_array($p['id'], $this->additional_product_ids))
            ->values()
            ->take(10)
            ->all();
    }

    // ── Acciones POS: Categorías ───────────────────────────────

    public function addPosCategory(int $id): void
    {
        if (! in_array($id, $this->pos_category_ids)) {
            $this->pos_category_ids[] = $id;
        }
        $this->posCategorySearch = '';
        $this->refreshFilteredPosCategories();
    }

    public function removePosCategory(int $id): void
    {
        $this->pos_category_ids = array_values(
            array_filter($this->pos_category_ids, fn ($x) => $x !== $id)
        );
        $this->refreshFilteredPosCategories();
    }

    public function createPosCategory(): void
    {
        $name = trim($this->posCategorySearch);
        if ($name === '' || mb_strlen($name) < 2) {
            $this->addError('posCategorySearch', 'Mínimo 2 caracteres.');
            return;
        }

        $this->resetErrorBag('posCategorySearch');

        // Reutiliza si ya existe con ese nombre
        $existing = PosCategory::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();
        if ($existing) {
            $this->addPosCategory($existing->id);
            return;
        }

        $slug = Str::slug($name);
        $base = $slug;
        $i    = 2;
        while (PosCategory::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        $cat = PosCategory::create([
            'name'          => $name,
            'slug'          => $slug,
            'parent_id'     => null,
            'complete_name' => $name,
            'state'         => true,
            'order'         => 0,
            'image'         => null,
        ]);

        // Recarga árbol completo
        $tree = PosCategory::whereNull('parent_id')
            ->where('state', true)
            ->with('childrenRecursive')
            ->orderBy('order')->orderBy('name')->get();
        $this->posCategoryOptions = $this->flattenPosCategories($tree);

        $this->addPosCategory($cat->id);
    }

    // ── Acciones POS: Productos adicionales ───────────────────

    public function addAdditionalProduct(int $id): void
    {
        if (! in_array($id, $this->additional_product_ids)) {
            $this->additional_product_ids[] = $id;
        }
        $this->additionalProductSearch = '';
        $this->refreshFilteredAdditionalProducts();
    }

    public function removeAdditionalProduct(int $id): void
    {
        $this->additional_product_ids = array_values(
            array_filter($this->additional_product_ids, fn ($x) => $x !== $id)
        );
        $this->refreshFilteredAdditionalProducts();
    }

    // ── Acciones Lista de precios ─────────────────────────────

    public function openPriceRuleModal(): void
    {
        $this->modalRule = [
            'pricelist_id'     => '',
            'compute_method'   => 'fixed',
            'fixed_price'      => 0,
            'percent_discount' => 0,
            'min_qty'          => 1,
            'date_start'       => '',
            'date_end'         => '',
        ];
        $this->showPriceModal = true;
    }

    public function savePriceRule(): void
    {
        if (empty($this->modalRule['pricelist_id'])) {
            $this->addError('modalRule.pricelist_id', 'Selecciona una lista de precios.');
            return;
        }

        // Persiste directamente en DB (el producto ya existe)
        PricelistItem::create([
            'pricelist_id'        => $this->modalRule['pricelist_id'],
            'product_template_id' => $this->product_template->id,
            'applied_on'          => '1_product',
            'compute_method'      => $this->modalRule['compute_method'],
            'fixed_price'         => $this->modalRule['fixed_price'] ?? 0,
            'percent_discount'    => $this->modalRule['percent_discount'] ?? 0,
            'min_qty'             => $this->modalRule['min_qty'] ?? 1,
            'date_start'          => !empty($this->modalRule['date_start']) ? $this->modalRule['date_start'] : null,
            'date_end'            => !empty($this->modalRule['date_end'])   ? $this->modalRule['date_end']   : null,
            'sequence'            => 10,
            'active'              => true,
        ]);

        $this->showPriceModal = false;
        $this->loadPricelistItems(); // Refresca la tabla
    }

    public function removePriceRule(int $itemId): void
    {
        PricelistItem::where('id', $itemId)
            ->where('product_template_id', $this->product_template->id)
            ->delete();

        $this->loadPricelistItems();
    }

    // ── Tabs ───────────────────────────────────────────────────

    public function setTab(string $tab): void
    {
        $allowed   = ['general', 'attributes', 'precios', 'pdv', 'accounting', 'subscriptions', 'web'];
        $this->tab = in_array($tab, $allowed, true) ? $tab : 'general';
    }

    // ── Reglas de validación ───────────────────────────────────

    protected function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'min:2', 'max:150'],
            'type'        => ['required', Rule::in(['goods', 'service', 'combo'])],
            'sale_ok'     => ['boolean'],
            'purchase_ok' => ['boolean'],
            'pos_ok'      => ['boolean'],
            'active'      => ['boolean'],
            'tracking'    => ['nullable', 'in:quantity,serial,lot'],
            'barcode'     => ['nullable', 'string', 'max:100'],
            'reference'   => ['nullable', 'string', 'max:64'],

            'base_price_sale' => ['nullable', 'numeric', 'min:0'],

            'uom_id'      => ['nullable', 'exists:uoms,id'],
            'uom_po_id'   => ['nullable', 'exists:uoms,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'brand_id'    => ['nullable', 'exists:brands,id'],
            'modello_id'  => ['nullable', 'exists:modellos,id'],
            'season_id'   => ['nullable', 'exists:seasons,id'],
            'detraction_id' => ['nullable', 'exists:detractions,id'],

            'account_sell_id' => ['required', 'exists:accounts,id'],
            'account_buy_id'  => ['required', 'exists:accounts,id'],

            'sale_tax_ids'    => ['array'],
            'sale_tax_ids.*'  => ['integer', 'exists:taxes,id'],
            'purchase_tax_ids'    => ['array'],
            'purchase_tax_ids.*'  => ['integer', 'exists:taxes,id'],

            'pos_category_ids'    => ['array'],
            'pos_category_ids.*'  => ['integer', 'exists:pos_categories,id'],

            'additional_product_ids'   => ['array'],
            'additional_product_ids.*' => ['integer', 'exists:product_templates,id'],

            'is_recurring'        => ['boolean'],
            'subscription_plan_id' => [
                Rule::requiredIf($this->is_recurring),
                'nullable',
                'exists:subscription_plans,id',
            ],
            'recurring_price' => [
                Rule::requiredIf($this->is_recurring),
                'nullable',
                'numeric',
                'min:0',
            ],

            // Web / SEO
            'short_description'  => ['nullable', 'string', 'max:255'],
            'long_description'   => ['nullable', 'string'],
            'title_google'       => ['nullable', 'string', 'max:70'],
            'description_google' => ['nullable', 'string', 'max:160'],
            'keywords_google'    => ['nullable', 'string', 'max:255'],

            // Precios por variante
            'variants.*.price_sale'      => ['nullable', 'numeric', 'min:0'],
            'variants.*.price_wholesale' => ['nullable', 'numeric', 'min:0'],
            'variants.*.price_purchase'  => ['nullable', 'numeric', 'min:0'],
            'variants.*.barcode'         => ['nullable', 'string', 'max:100'],
            'variants.*.active'          => ['boolean'],
        ];
    }

    // ── Actualizar template + relaciones ───────────────────────

    /**
     * Actualiza el template, variantes y todas las relaciones.
     *
     * Flujo (todo-o-nada):
     *  1. Valida
     *  2. Verifica coherencia Marca/Modelo
     *  3. Transacción: template → variantes → impuestos → POS → adicionales
     */
    public function update(): mixed
    {
        // Normaliza nombre
        $this->name = trim($this->name);

        $data = $this->validate();

        // Validación lógica: modelo debe pertenecer a la marca
        if ($this->modello_id && $this->brand_id) {
            $ok = \App\Models\Modello::where('id', $this->modello_id)
                ->where('brand_id', $this->brand_id)
                ->exists();

            if (! $ok) {
                $this->addError('modello_id', 'El modelo no pertenece a la marca seleccionada.');
                return null;
            }
        }

        DB::beginTransaction();

        try {
            // ── 1. Regenera slug si cambia el nombre ──────────
            $newSlug = $this->product_template->slug;
            if ($this->product_template->name !== $data['name']) {
                $base  = Str::slug($data['name']);
                $slug  = $base;
                $i     = 2;
                while (ProductTemplate::where('slug', $slug)
                    ->where('id', '!=', $this->product_template->id)
                    ->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $newSlug = $slug;
            }

            // ── 2. Actualiza el template ───────────────────────
            $this->product_template->update([
                'name'                 => $data['name'],
                'slug'                 => $newSlug,
                'type'                 => $data['type'],
                'sale_ok'              => $data['sale_ok'],
                'purchase_ok'          => $data['purchase_ok'],
                'pos_ok'               => $data['pos_ok'],
                'active'               => $data['active'],
                'uom_id'               => $data['uom_id'],
                'uom_po_id'            => $data['uom_po_id'],
                'category_id'          => $data['category_id'],
                'brand_id'             => $data['brand_id'],
                'modello_id'           => $data['modello_id'],
                'season_id'            => $data['season_id'],
                'detraction_id'        => $data['detraction_id'],
                'account_sell_id'      => $data['account_sell_id'],
                'account_buy_id'       => $data['account_buy_id'],
                'is_recurring'         => $data['is_recurring'],
                'subscription_plan_id' => $data['is_recurring'] ? $data['subscription_plan_id'] : null,
                'recurring_price'      => $data['is_recurring'] ? $data['recurring_price'] : null,
                'short_description'    => $data['short_description'],
                'long_description'     => $data['long_description'],
                'title_google'         => $data['title_google'],
                'description_google'   => $data['description_google'],
                'keywords_google'      => $data['keywords_google'],
            ]);

            // ── 3. Actualiza variante default (barcode, tracking, precio base) ──
            $this->product_template->variants()
                ->where('is_default', true)
                ->update([
                    'tracking'   => $data['tracking'],
                    'barcode'    => blank($data['barcode']) ? null : trim($data['barcode']),
                    'reference'  => blank($data['reference']) ? null : trim($data['reference']),
                    'price_sale' => $data['base_price_sale'] ?? null,
                ]);

            // ── 4. Actualiza precios de todas las variantes ───
            foreach ($this->variants as $variantId => $variantData) {
                $barcode = trim((string) ($variantData['barcode'] ?? ''));

                ProductVariant::where('id', $variantId)
                    ->where('product_template_id', $this->product_template->id)
                    ->update([
                        'price_sale'      => is_numeric($variantData['price_sale'] ?? null)
                            ? $variantData['price_sale'] : null,
                        'price_wholesale' => is_numeric($variantData['price_wholesale'] ?? null)
                            ? $variantData['price_wholesale'] : null,
                        'price_purchase'  => is_numeric($variantData['price_purchase'] ?? null)
                            ? $variantData['price_purchase'] : null,
                        'barcode'         => $barcode !== '' ? $barcode : null,
                        'active'          => (bool) ($variantData['active'] ?? true),
                    ]);
            }

            // ── 5. Sincroniza impuestos (muchos a muchos) ──────
            $this->product_template->saleTaxes()->sync(
                collect($data['sale_tax_ids'])->filter()->map(fn ($x) => (int) $x)->values()->all()
            );
            $this->product_template->purchaseTaxes()->sync(
                collect($data['purchase_tax_ids'])->filter()->map(fn ($x) => (int) $x)->values()->all()
            );

            // ── 6. Sincroniza categorías POS ──────────────────
            $this->product_template->posCategories()->sync(
                collect($data['pos_category_ids'])->filter()->map(fn ($x) => (int) $x)->values()->all()
            );

            // ── 7. Sincroniza productos adicionales ───────────
            $additionalSync = collect($data['additional_product_ids'])
                ->filter(fn ($id) => (int) $id !== $this->product_template->id)
                ->mapWithKeys(fn ($id) => [(int) $id => ['sequence' => 10, 'active' => true]])
                ->all();
            $this->product_template->additionalProducts()->sync($additionalSync);

            DB::commit();

            session()->flash('swal', [
                'icon'  => 'success',
                'title' => '¡Actualizado!',
                'text'  => 'Producto "' . $data['name'] . '" actualizado correctamente.',
            ]);

            return redirect()->route('admin.products.index');

        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('ProductEdit: Error DB al actualizar', [
                'id'      => $this->product_template->id,
                'error'   => $e->getMessage(),
                'usuario' => auth()->id(),
            ]);
            session()->flash('swal', [
                'icon'  => 'error',
                'title' => 'Error de base de datos',
                'text'  => 'No se pudo actualizar. Por favor intenta nuevamente.',
            ]);
            return null;

        } catch (AuthorizationException $e) {
            DB::rollBack();
            session()->flash('swal', [
                'icon'  => 'error',
                'title' => 'Sin permiso',
                'text'  => 'No tienes permiso para editar este producto.',
            ]);
            return redirect()->route('admin.products.index');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ProductEdit: Error inesperado al actualizar', [
                'id'      => $this->product_template->id,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'usuario' => auth()->id(),
            ]);
            session()->flash('swal', [
                'icon'  => 'error',
                'title' => 'Error inesperado',
                'text'  => 'Ocurrió un problema. Contacta al administrador.',
            ]);
            return null;
        }
    }

    // ── Computed: etiquetas de seleccionados ──────────────────

    /** Devuelve los objetos de las categorías POS seleccionadas */
    public function getSelectedPosCategoriesProperty(): array
    {
        return collect($this->posCategoryOptions)
            ->whereIn('id', $this->pos_category_ids)
            ->values()
            ->all();
    }

    /** Devuelve los objetos de los productos adicionales seleccionados */
    public function getSelectedAdditionalProductsProperty(): array
    {
        return collect($this->additionalProductOptions)
            ->whereIn('id', $this->additional_product_ids)
            ->values()
            ->all();
    }

    /** Determina si se puede crear una categoría POS con el texto buscado */
    public function getCanCreatePosCategoryProperty(): bool
    {
        $name = trim($this->posCategorySearch);
        if ($name === '') {
            return false;
        }

        return ! PosCategory::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->exists();
    }

    /** Devuelve los atributos con sus valores actuales (para la pestaña read-only) */
    public function getExistingAttributeLinesProperty(): array
    {
        return $this->product_template
            ->attributeLines()
            ->with([
                'attribute:id,name',
                'values:id,name,extra_price',
            ])
            ->get()
            ->map(fn ($line) => [
                'attribute_name' => $line->attribute->name ?? '—',
                'values'         => $line->values->map(fn ($v) => [
                    'name'        => $v->name,
                    'extra_price' => $v->extra_price,
                ])->toArray(),
            ])->toArray();
    }

    // ── Render ────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.admin.products.product-edit');
    }
}
