<?php

namespace App\Livewire\Admin\Pricelist;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

use App\Models\Pricelist;
use App\Models\PricelistItem;
use App\Models\ProductTemplate;
use App\Models\ProductVariant;
use App\Models\Category;

// php artisan make:livewire Admin/Pricelist/PricelistItemManager
class PricelistItemManager extends Component
{
    use WithPagination;

    public Pricelist $pricelist;

    public string $search = '';
    public int $perPage = 10;

    // Para selects livianos
    public string $productSearch = '';

    public array $new = [
        'applied_on' => 'all',          // all | category | template | variant
        'category_id' => null,
        'product_template_id' => null,
        'product_variant_id' => null,

        'sequence' => 10,
        'min_qty' => 1,

        'compute_method' => 'fixed',    // fixed | discount | formula
        'fixed_price' => null,
        'percent_discount' => null,

        'base' => 'price_sale',
        'base_pricelist_id' => null,
        'price_multiplier' => null,
        'price_surcharge' => null,
        'rounding' => null,
        'min_margin' => null,
        'max_margin' => null,

        'date_start' => null,
        'date_end' => null,

        'active' => true,
    ];

    // Edición inline (por ahora solo arranque)
    public array $editing = [];
    public array $row = [];

    public function mount(Pricelist $pricelist)
    {
        $this->pricelist = $pricelist;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatedNewAppliedOn()
    {
        // Resetea ids cuando cambias el ámbito
        $this->new['category_id'] = null;
        $this->new['product_template_id'] = null;
        $this->new['product_variant_id'] = null;
    }

    public function updatedNewComputeMethod()
    {
        if ($this->new['compute_method'] !== 'fixed') $this->new['fixed_price'] = null;
        if ($this->new['compute_method'] !== 'discount') $this->new['percent_discount'] = null;

        if ($this->new['compute_method'] !== 'formula') {
            $this->new['base'] = 'price_sale';
            $this->new['base_pricelist_id'] = null;
            $this->new['price_multiplier'] = null;
            $this->new['price_surcharge'] = null;
            $this->new['rounding'] = null;
            $this->new['min_margin'] = null;
            $this->new['max_margin'] = null;
        }
    }

    private function rulesNew(): array
    {
        return [
            'new.applied_on' => ['required', Rule::in(['all', 'category', 'template', 'variant'])],

            'new.category_id' => [
                'nullable',
                Rule::requiredIf(fn() => $this->new['applied_on'] === 'category'),
                'exists:categories,id'
            ],

            'new.product_template_id' => [
                'nullable',
                Rule::requiredIf(fn() => $this->new['applied_on'] === 'template'),
                'exists:product_templates,id'
            ],

            'new.product_variant_id' => [
                'nullable',
                Rule::requiredIf(fn() => $this->new['applied_on'] === 'variant'),
                'exists:product_variants,id'
            ],

            'new.sequence' => ['required', 'integer', 'min:0'],
            'new.min_qty' => ['required', 'numeric', 'min:1'],

            'new.compute_method' => ['required', Rule::in(['fixed', 'discount', 'formula'])],

            'new.fixed_price' => [
                'nullable',
                Rule::requiredIf(fn() => $this->new['compute_method'] === 'fixed'),
                'numeric',
                'min:0'
            ],

            'new.percent_discount' => [
                'nullable',
                Rule::requiredIf(fn() => $this->new['compute_method'] === 'discount'),
                'numeric',
                'min:0',
                'max:100'
            ],

            // Formula
            'new.base' => ['nullable', Rule::in(['price_sale', 'cost', 'other_pricelist'])],

            'new.base_pricelist_id' => [
                'nullable',
                Rule::requiredIf(fn() => $this->new['compute_method'] === 'formula' && $this->new['base'] === 'other_pricelist'),
                'exists:pricelists,id'
            ],

            'new.price_multiplier' => ['nullable', 'numeric'],
            'new.price_surcharge' => ['nullable', 'numeric'],
            'new.rounding' => ['nullable', 'numeric'],
            'new.min_margin' => ['nullable', 'numeric'],
            'new.max_margin' => ['nullable', 'numeric'],

            'new.date_start' => ['nullable', 'date'],
            'new.date_end' => ['nullable', 'date', 'after_or_equal:new.date_start'],

            'new.active' => ['boolean'],
        ];
    }

    /*  public function addLine()
    {
        $this->validate($this->rulesNew());

        PricelistItem::create([
            'pricelist_id' => $this->pricelist->id,
            ...$this->new,
        ]);


        $this->new['category_id'] = null;
        $this->new['product_template_id'] = null;
        $this->new['product_variant_id'] = null;
        $this->new['fixed_price'] = null;
        $this->new['percent_discount'] = null;

        $this->dispatch('swal', icon: 'success', title: 'Bien Hecho', text: 'Regla agregada.');
    } */

    public function addLine()
    {
        $this->validate($this->rulesNew());

        // 1) Copia data
        $data = $this->new;

        // 2) Limpieza según compute_method (igual que en save)
        if (($data['compute_method'] ?? 'fixed') !== 'fixed') {
            $data['fixed_price'] = null;
        }
        if (($data['compute_method'] ?? 'fixed') !== 'discount') {
            $data['percent_discount'] = null;
        }
        if (($data['compute_method'] ?? 'fixed') !== 'formula') {
            $data['base'] = 'price_sale';
            $data['base_pricelist_id'] = null;
            $data['price_multiplier'] = null;
            $data['price_surcharge'] = null;
            $data['rounding'] = null;
            $data['min_margin'] = null;
            $data['max_margin'] = null;
        }

        // 3) Limpieza según applied_on
        if (($data['applied_on'] ?? 'all') !== 'category') $data['category_id'] = null;
        if (($data['applied_on'] ?? 'all') !== 'template') $data['product_template_id'] = null;
        if (($data['applied_on'] ?? 'all') !== 'variant') $data['product_variant_id'] = null;

        // 4) Guardar
        PricelistItem::create([
            'pricelist_id' => $this->pricelist->id,
            ...$data,
        ]);

        // Reset suave
        $this->new['category_id'] = null;
        $this->new['product_template_id'] = null;
        $this->new['product_variant_id'] = null;
        $this->new['fixed_price'] = null;
        $this->new['percent_discount'] = null;

        $this->dispatch('swal', icon: 'success', title: 'Bien Hecho', text: 'Regla agregada.');
    }



    public function startEdit(int $id)
    {
        $item = PricelistItem::where('pricelist_id', $this->pricelist->id)->findOrFail($id);

        $this->editing[$id] = true;
        $this->row[$id] = $item->only([
            'applied_on',
            'category_id',
            'product_template_id',
            'product_variant_id',
            'sequence',
            'min_qty',
            'compute_method',
            'fixed_price',
            'percent_discount',
            'base',
            'base_pricelist_id',
            'price_multiplier',
            'price_surcharge',
            'rounding',
            'min_margin',
            'max_margin',
            'date_start',
            'date_end',
            'active'
        ]);
    }

    public function cancelEdit(int $id)
    {
        unset($this->editing[$id], $this->row[$id]);
    }

    public function save(int $id)
    {
        $item = PricelistItem::where('pricelist_id', $this->pricelist->id)->findOrFail($id);

        $data = $this->row[$id] ?? [];

        validator($data, [
            'applied_on' => ['required', Rule::in(['all', 'category', 'template', 'variant'])],
            'category_id' => ['nullable', 'exists:categories,id'],
            'product_template_id' => ['nullable', 'exists:product_templates,id'],
            'product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'sequence' => ['required', 'integer', 'min:0'],
            'min_qty' => ['required', 'numeric', 'min:1'],
            'compute_method' => ['required', Rule::in(['fixed', 'discount', 'formula'])],
            'fixed_price' => ['nullable', 'numeric', 'min:0'],
            'percent_discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'base' => ['nullable', Rule::in(['price_sale', 'cost', 'other_pricelist'])],
            'base_pricelist_id' => ['nullable', 'exists:pricelists,id'],
            'date_start' => ['nullable', 'date'],
            'date_end' => ['nullable', 'date'],
            'active' => ['boolean'],
        ])->validate();

        // Limpieza según compute_method
        if (($data['compute_method'] ?? 'fixed') !== 'fixed') $data['fixed_price'] = null;
        if (($data['compute_method'] ?? 'fixed') !== 'discount') $data['percent_discount'] = null;
        if (($data['compute_method'] ?? 'fixed') !== 'formula') {
            $data['base_pricelist_id'] = null;
            $data['price_multiplier'] = null;
            $data['price_surcharge'] = null;
            $data['rounding'] = null;
            $data['min_margin'] = null;
            $data['max_margin'] = null;
        }

        // Limpieza según applied_on
        if (($data['applied_on'] ?? 'all') !== 'category') $data['category_id'] = null;
        if (($data['applied_on'] ?? 'all') !== 'template') $data['product_template_id'] = null;
        if (($data['applied_on'] ?? 'all') !== 'variant') $data['product_variant_id'] = null;

        $item->update($data);

        $this->cancelEdit($id);

        $this->dispatch('swal', icon: 'success', title: 'Guardado', text: 'Regla actualizada.');
    }

    public function deleteSingle(int $id)
    {
        PricelistItem::where('pricelist_id', $this->pricelist->id)->where('id', $id)->delete();
        $this->dispatch('swal', icon: 'success', title: 'Eliminado', text: 'Regla eliminada.');
    }





    public function render()
    {
        $items = PricelistItem::query()
            ->with([
                'category:id,name',
                'productTemplate:id,name',
                'productVariant:id,sku,variant_name',
                'basePricelist:id,name',
            ])
            ->where('pricelist_id', $this->pricelist->id)
            ->when($this->search, function ($q) {
                $s = trim($this->search);
                $q->where(function ($qq) use ($s) {
                    $qq->where('applied_on', 'like', "%{$s}%")
                        ->orWhere('compute_method', 'like', "%{$s}%")
                        ->orWhere('min_qty', 'like', "%{$s}%")
                        ->orWhere('fixed_price', 'like', "%{$s}%")
                        ->orWhere('percent_discount', 'like', "%{$s}%");
                });
            })
            ->orderBy('sequence')
            ->orderBy('min_qty')
            ->paginate($this->perPage);

        /* $categories = Category::query()
            ->select('id', 'name')
            ->when($this->productSearch, fn($q) => $q->where('name', 'like', '%' . trim($this->productSearch) . '%'))
            ->orderBy('name')
            ->limit(30)
            ->get(); */

        $categoryTree = Category::tree()->get(); // usa tu scopeTree()

        $categories = $this->flattenCategoriesForSelect($categoryTree);

        $templates = ProductTemplate::query()
            ->select('id', 'name')
            ->when($this->productSearch, fn($q) => $q->where('name', 'like', '%' . trim($this->productSearch) . '%'))
            ->orderBy('name')
            ->limit(30)
            ->get();

        $variants = ProductVariant::query()
            ->select('id', 'sku', 'variant_name', 'product_template_id')
            ->when($this->productSearch, function ($q) {
                $s = trim($this->productSearch);
                $q->where('sku', 'like', "%{$s}%")
                    ->orWhere('variant_name', 'like', "%{$s}%");
            })
            ->orderBy('sku')
            ->limit(30)
            ->get();

        $pricelists = Pricelist::select('id', 'name')->orderBy('name')->get();

        return view('livewire.admin.pricelist.pricelist-item-manager', [
            'items' => $items,
            'categories' => $categories,
            'templates' => $templates,
            'variants' => $variants,
            'allPricelists' => $pricelists,
        ]);
    }


    private function flattenCategoriesForSelect($nodes, string $trail = ''): array
    {
        $out = [];

        foreach ($nodes as $cat) {
            $currentTrail = $trail ? ($trail . ' / ' . $cat->name) : $cat->name;

            $out[] = [
                'id' => $cat->id,
                'name' => $currentTrail,
            ];

            if ($cat->childrenRecursive && $cat->childrenRecursive->count()) {
                $out = array_merge(
                    $out,
                    $this->flattenCategoriesForSelect($cat->childrenRecursive, $currentTrail)
                );
            }
        }

        return $out;
    }
}
