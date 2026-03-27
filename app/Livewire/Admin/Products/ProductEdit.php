<?php

namespace App\Livewire\Admin\Products;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Detraction;
use App\Models\Modello;
use App\Models\ProductTemplate;
use App\Models\ProductVariant;
use App\Models\Season;
use App\Models\SubscriptionPlan;
use App\Models\Tax;
use App\Models\Uom;
use App\Models\UomCategory;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Edición de plantilla de producto + precios de variantes.
 *
 * Patrón Odoo:
 *  - El template guarda datos maestros (nombre, tipo, marca, etc.)
 *  - Las variantes guardan precios + SKU individuales
 *  - Los atributos/combinaciones NO se regeneran en edición (solo se editan precios)
 */
#[Title('Editar Producto')]
class ProductEdit extends Component
{
    //use AuthorizesRequests;

    // ── Model binding ─────────────────────────────────────────
    public ProductTemplate $product_template;

    // ── Tabs ─────────────────────────────────────────────────
    public string $tab = 'general';

    // ── Campos del template ───────────────────────────────────
    public string  $name         = '';
    public string  $type         = 'goods';
    public bool    $sale_ok      = true;
    public bool    $purchase_ok  = false;
    public bool    $pos_ok       = true;
    public bool    $active       = true;
    public bool    $is_recurring = false;

    public ?int $uom_id              = null;
    public ?int $uom_po_id           = null;
    public ?int $category_id         = null;
    public ?int $brand_id            = null;
    public ?int $modello_id          = null;
    public ?int $season_id           = null;
    public ?int $detraction_id       = null;
    public ?int $subscription_plan_id = null;

    // ── Variantes (edición de precios) ────────────────────────
    // variants[variant_id] = ['price_sale' => X, 'price_purchase' => Y, 'price_wholesale' => Z, 'barcode' => '...' ]
    public array $variants = [];

    // ── Catálogos ────────────────────────────────────────────
    public array $uomCategories      = [];
    public array $uomPurchaseOptions = [];
    public array $categoryOptions    = [];
    public array $brands             = [];
    public array $modelloOptions     = [];
    public array $seasons            = [];
    public array $detractions        = [];
    public array $subscriptionPlans  = [];

    // ── Ciclo de vida ────────────────────────────────────────

    public function mount(ProductTemplate $product_template): void
    {
        //$this->authorize('update', $product_template);

        $this->product_template = $product_template;

        // Hidrata campos del template
        $this->name              = (string) $product_template->name;
        $this->type              = (string) $product_template->type;
        $this->sale_ok           = (bool)   $product_template->sale_ok;
        $this->purchase_ok       = (bool)   $product_template->purchase_ok;
        $this->pos_ok            = (bool)   $product_template->pos_ok;
        $this->active            = (bool)   $product_template->active;
        $this->is_recurring      = (bool)   $product_template->is_recurring;
        $this->uom_id            = $product_template->uom_id;
        $this->uom_po_id         = $product_template->uom_po_id;
        $this->category_id       = $product_template->category_id;
        $this->brand_id          = $product_template->brand_id;
        $this->modello_id        = $product_template->modello_id;
        $this->season_id         = $product_template->season_id;
        $this->detraction_id     = $product_template->detraction_id;
        $this->subscription_plan_id = $product_template->subscription_plan_id;

        // Hidrata variantes para edición de precios
        $this->loadVariants();

        // Carga catálogos
        $this->loadCatalogs();
    }

    // ── Carga de variantes ────────────────────────────────────

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
                    'sku'             => $v->sku,
                    'variant_name'    => $v->variant_name ?? 'Default',
                    'is_default'      => (bool) $v->is_default,
                    'active'          => (bool) $v->active,
                    'price_sale'      => $v->price_sale,
                    'price_wholesale' => $v->price_wholesale,
                    'price_purchase'  => $v->price_purchase,
                    'barcode'         => $v->barcode ?? '',
                    // Label para la UI (ej: "M - Rojo")
                    'label'           => $v->variant_name ?? 'Default',
                ],
            ])->toArray();
    }

    // ── Carga de catálogos ────────────────────────────────────

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

        // Opciones de UoM compra (misma categoría que la UoM base)
        $this->refreshPurchaseUoms();

        // Categorías de producto aplanadas
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
        $this->detractions = Detraction::select('id', 'code', 'name', 'rate')
            ->where('active', true)->orderBy('code')->get()->toArray();

        // Planes de suscripción
        $this->subscriptionPlans = SubscriptionPlan::select('id', 'name', 'interval_count', 'interval_unit')
            ->where('active', true)->orderBy('order')->orderBy('name')->get()->toArray();
    }

    // ── Observadores reactivos ────────────────────────────────

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
        }
    }

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

    private function flattenCategories($categories, int $level = 0): array
    {
        $out = [];
        foreach ($categories as $cat) {
            $out[] = ['id' => $cat->id, 'label' => str_repeat('– ', $level) . $cat->name];
            if ($cat->childrenRecursive && $cat->childrenRecursive->count()) {
                $out = array_merge($out, $this->flattenCategories($cat->childrenRecursive, $level + 1));
            }
        }
        return $out;
    }

    // ── Tabs ─────────────────────────────────────────────────

    public function setTab(string $tab): void
    {
        $allowed   = ['general', 'variants', 'accounting'];
        $this->tab = in_array($tab, $allowed, true) ? $tab : 'general';
    }

    // ── Reglas de validación ─────────────────────────────────

    protected function rules(): array
    {
        return [
            'name'                => ['required', 'string', 'min:2', 'max:150'],
            'type'                => ['required', Rule::in(['goods', 'service', 'combo'])],
            'sale_ok'             => ['boolean'],
            'purchase_ok'         => ['boolean'],
            'pos_ok'              => ['boolean'],
            'active'              => ['boolean'],
            'is_recurring'        => ['boolean'],
            'uom_id'              => ['nullable', 'exists:uoms,id'],
            'uom_po_id'           => ['nullable', 'exists:uoms,id'],
            'category_id'         => ['nullable', 'exists:categories,id'],
            'brand_id'            => ['nullable', 'exists:brands,id'],
            'modello_id'          => ['nullable', 'exists:modellos,id'],
            'season_id'           => ['nullable', 'exists:seasons,id'],
            'detraction_id'       => ['nullable', 'exists:detractions,id'],
            'subscription_plan_id' => ['nullable', 'exists:subscription_plans,id'],

            // Validación de precios por variante
            'variants.*.price_sale'      => ['nullable', 'numeric', 'min:0'],
            'variants.*.price_wholesale' => ['nullable', 'numeric', 'min:0'],
            'variants.*.price_purchase'  => ['nullable', 'numeric', 'min:0'],
            'variants.*.barcode'         => ['nullable', 'string', 'max:100'],
            'variants.*.active'          => ['boolean'],
        ];
    }

    // ── Guardar template + variantes ──────────────────────────

    /**
     * Actualiza el template y los precios de sus variantes.
     *
     * Flujo:
     *  1. Autoriza
     *  2. Valida
     *  3. Detecta cambios
     *  4. Transacción: actualiza template → actualiza variantes
     */
    public function update(): mixed
    {
        //$this->authorize('update', $this->product_template);

        // Normaliza nombre
        $this->name = trim($this->name);

        $data = $this->validate();

        DB::beginTransaction();

        try {
            // ── 1. Actualiza el template ──────────────────────

            // Regenera slug si cambia el nombre
            $newSlug = $this->product_template->slug;
            if ($this->product_template->name !== $data['name']) {
                $baseSlug = Str::slug($data['name']);
                $slug     = $baseSlug;
                $i        = 2;
                while (ProductTemplate::where('slug', $slug)
                    ->where('id', '!=', $this->product_template->id)
                    ->exists()) {
                    $slug = $baseSlug . '-' . $i++;
                }
                $newSlug = $slug;
            }

            $this->product_template->update([
                'name'                 => $data['name'],
                'slug'                 => $newSlug,
                'type'                 => $data['type'],
                'sale_ok'              => $data['sale_ok'],
                'purchase_ok'          => $data['purchase_ok'],
                'pos_ok'               => $data['pos_ok'],
                'active'               => $data['active'],
                'is_recurring'         => $data['is_recurring'],
                'uom_id'               => $data['uom_id'],
                'uom_po_id'            => $data['uom_po_id'],
                'category_id'          => $data['category_id'],
                'brand_id'             => $data['brand_id'],
                'modello_id'           => $data['modello_id'],
                'season_id'            => $data['season_id'],
                'detraction_id'        => $data['detraction_id'],
                'subscription_plan_id' => $data['is_recurring'] ? $data['subscription_plan_id'] : null,
            ]);

            // ── 2. Actualiza precios de variantes ─────────────

            foreach ($this->variants as $variantId => $variantData) {
                // Sanitiza barcode vacío a null
                $barcode = trim((string) ($variantData['barcode'] ?? ''));
                $barcode = $barcode !== '' ? $barcode : null;

                ProductVariant::where('id', $variantId)
                    ->where('product_template_id', $this->product_template->id)
                    ->update([
                        'price_sale'      => $variantData['price_sale'] !== '' ? $variantData['price_sale'] : null,
                        'price_wholesale' => $variantData['price_wholesale'] !== '' ? $variantData['price_wholesale'] : null,
                        'price_purchase'  => $variantData['price_purchase'] !== '' ? $variantData['price_purchase'] : null,
                        'barcode'         => $barcode,
                        'active'          => (bool) ($variantData['active'] ?? true),
                    ]);
            }

            DB::commit();

            session()->flash('swal', [
                'icon'  => 'success',
                'title' => 'Bien hecho',
                'text'  => 'Producto "' . $data['name'] . '" actualizado correctamente.',
            ]);

            return redirect()->route('admin.products.index');
        } catch (QueryException $e) {
            DB::rollBack();

            Log::error('Error al actualizar ProductTemplate', [
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
                'text'  => 'No tienes permiso para editar productos.',
            ]);

            return redirect()->route('admin.products.index');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Error inesperado al actualizar ProductTemplate', [
                'id'      => $this->product_template->id,
                'error'   => $e->getMessage(),
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

    // ── Render ───────────────────────────────────────────────

    public function render()
    {
        return view('livewire.admin.products.product-edit');
    }
}
