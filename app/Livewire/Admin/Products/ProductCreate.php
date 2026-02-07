<?php

namespace App\Livewire\Admin\Products;

use Livewire\Component;

use App\Models\UomCategory;
use App\Models\Uom;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductTemplate;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

//php artisan make:livewire Admin/Products/ProductCreate
class ProductCreate extends Component
{

    // Product template
    public string $name = '';
    public string $type = 'goods';
    public bool $sale_ok = true;
    public bool $purchase_ok = false;
    public bool $pos_ok = true;
    public bool $active = true;

    public array $attributeLines = []; // líneas estilo Odoo
    public $catalogAttributes = [];

    public ?int $uom_id = null;
    public ?int $uom_po_id = null;

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
        $names = \App\Models\AttributeValue::whereIn('id', $valueIds)->pluck('name', 'id')->toArray();

        $this->variant_preview = array_map(function ($combo) use ($names) {
            $parts = array_map(fn($id) => $names[$id] ?? '?', $combo);
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
            'active' => ['boolean'],
        ]);

        $name = trim($this->name);

        // slug único
        $slug = Str::slug($name);
        $baseSlug = $slug;
        $i = 2;
        while (ProductTemplate::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i++;
        }

        // 1) Crear template (sin precio)
        $template = ProductTemplate::create([
            'name' => $name,
            'slug' => $slug,
            'type' => $this->type,
            'sale_ok' => $this->sale_ok,
            'purchase_ok' => $this->purchase_ok,
            'pos_ok' => $this->pos_ok,
            'active' => $this->active,
        ]);

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

            $variant = ProductVariant::create([
                'product_template_id' => $template->id,
                'sku' => $sku,
                'barcode' => null,

                // ✅ precio SOLO en variantes:
                'price_sale' => (float) $this->base_price_sale + $extraTotal,
                'price_wholesale' => null,
                'price_purchase' => null,

                'active' => true,
                'is_default' => $first,

                'combination_key' => $combinationKey,
                'variant_name' => $variantName,
            ]);

            // Pivot
            $variant->values()->sync($valueIds);

            $first = false;
        }

        return redirect()->route('admin.products.index')
            ->with('swal', ['icon' => 'success', 'title' => 'Listo', 'text' => 'Producto creado con variantes']);
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

        ProductVariant::create([
            'product_template_id' => $template->id,
            'sku' => $sku,
            'barcode' => null,

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
}
