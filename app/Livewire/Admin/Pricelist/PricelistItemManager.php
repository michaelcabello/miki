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
use Livewire\Attributes\On;

// php artisan make:livewire Admin/Pricelist/PricelistItemManager
class PricelistItemManager extends Component
{
    use WithPagination;

    public Pricelist $pricelist;
    public string $search = '';
    public int $perPage = 10;

    //public $editing = []; // Almacena temporalmente los datos de la fila en edición

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


    /**
     * Reglas de validación unificadas (Single Source of Truth)
     * @param string $prefix Útil para diferenciar entre 'new' y 'row.id'
     */
    protected function getValidationRules($prefix = 'new.'): array
    {
        return [
            $prefix . 'applied_on' => ['required', Rule::in(['all', 'category', 'template', 'variant'])],

            // Validación condicional: El ID es obligatorio según el ámbito
            $prefix . 'category_id' => [
                Rule::requiredIf(fn() => data_get($this, $prefix . 'applied_on') === 'category'),
                'nullable',
                'exists:categories,id'
            ],
            $prefix . 'product_template_id' => [
                Rule::requiredIf(fn() => data_get($this, $prefix . 'applied_on') === 'template'),
                'nullable',
                'exists:product_templates,id'
            ],
            $prefix . 'product_variant_id' => [
                Rule::requiredIf(fn() => data_get($this, $prefix . 'applied_on') === 'variant'),
                'nullable',
                'exists:product_variants,id'
            ],

            $prefix . 'sequence' => ['required', 'integer', 'min:0'],
            $prefix . 'min_qty' => ['required', 'numeric', 'min:1'],
            $prefix . 'compute_method' => ['required', Rule::in(['fixed', 'discount', 'formula'])],

            // Precio fijo es requerido SOLO si el método es fixed
            $prefix . 'fixed_price' => [
                Rule::requiredIf(fn() => data_get($this, $prefix . 'compute_method') === 'fixed'),
                'nullable',
                'numeric',
                'min:0'
            ],

            // Descuento requerido SOLO si el método es discount
            $prefix . 'percent_discount' => [
                Rule::requiredIf(fn() => data_get($this, $prefix . 'compute_method') === 'discount'),
                'nullable',
                'numeric',
                'min:0',
                'max:100'
            ],

            // Campos de fórmula
            $prefix . 'base' => [
                Rule::requiredIf(fn() => data_get($this, $prefix . 'compute_method') === 'formula'),
                'nullable',
                Rule::in(['price_sale', 'cost', 'other_pricelist'])
            ],
            $prefix . 'base_pricelist_id' => [
                Rule::requiredIf(fn() => data_get($this, $prefix . 'compute_method') === 'formula' && data_get($this, $prefix . 'base') === 'other_pricelist'),
                'nullable',
                'exists:pricelists,id'
            ],
            $prefix . 'price_multiplier' => ['nullable', 'numeric'],
            $prefix . 'price_surcharge' => ['nullable', 'numeric'],
            $prefix . 'rounding' => ['nullable', 'numeric'],
            $prefix . 'min_margin' => ['nullable', 'numeric'],
            $prefix . 'max_margin' => ['nullable', 'numeric'],

            $prefix . 'date_start' => ['nullable', 'date'],
            $prefix . 'date_end' => ['nullable', 'date', 'after_or_equal:' . $prefix . 'date_start'],
            $prefix . 'active' => ['boolean'],
        ];
    }

    /**
     * Helper para limpiar datos antes de guardar (Odoo Logic)
     * Evita que persistan precios fijos si cambiaste a fórmula, etc.
     */
    private function cleanData(array $data): array
    {
        $method = $data['compute_method'] ?? 'fixed';
        $applied = $data['applied_on'] ?? 'all';

        // 1. Limpieza por método
        if ($method !== 'fixed') $data['fixed_price'] = null;
        if ($method !== 'discount') $data['percent_discount'] = null;
        if ($method !== 'formula') {
            $data['base'] = 'price_sale';
            $data['base_pricelist_id'] = null;
            $data['price_multiplier'] = 1.0; // Odoo default
            $data['price_surcharge'] = 0.0;
            $data['rounding'] = 0.0;
            $data['min_margin'] = 0.0;
            $data['max_margin'] = 0.0;
        }

        // 2. Limpieza por ámbito (Applied On)
        if ($applied !== 'category') $data['category_id'] = null;
        if ($applied !== 'template') $data['product_template_id'] = null;
        if ($applied !== 'variant') $data['product_variant_id'] = null;

        return $data;
    }


    public function addLineBack()
    {
        $this->validate($this->getValidationRules('new.'));

        $data = $this->cleanData($this->new);

        // SUGERENCIA: Verificar duplicados (Pricelist + AppliedOn + ID + MinQty)
        $exists = PricelistItem::where('pricelist_id', $this->pricelist->id)
            ->where('applied_on', $data['applied_on'])
            ->where('category_id', $data['category_id'])
            ->where('product_template_id', $data['product_template_id'])
            ->where('product_variant_id', $data['product_variant_id'])
            ->where('min_qty', $data['min_qty'])
            ->exists();

        if ($exists) {
            $this->addError('new.applied_on', 'Ya existe una regla idéntica para esta cantidad.');
            return;
        }

        PricelistItem::create([
            'pricelist_id' => $this->pricelist->id,
            ...$data
        ]);

        $this->reset('new'); // Reset completo a valores iniciales
        $this->dispatch('swal', icon: 'success', title: 'Regla agregada');
    }


    public function addLinebackdos()
    {
        $this->validate($this->getValidationRules('new.'));

        $data = $this->cleanData($this->new);

        if (isset($this->new['id'])) {
            // MODO EDICIÓN
            $item = PricelistItem::findOrFail($this->new['id']);
            $item->update($data);
            $this->dispatch('swal', icon: 'success', title: 'Regla actualizada');
        } else {
            // MODO CREACIÓN
            // (Opcional) Aquí puedes mantener tu validación de duplicados que ya tienes
            PricelistItem::create([
                'pricelist_id' => $this->pricelist->id,
                ...$data
            ]);
            $this->dispatch('swal', icon: 'success', title: 'Regla agregada');
        }

        $this->resetForm();
    }


public function addLine()
{
    // 1. Validamos usando tus reglas centralizadas
    $this->validate($this->rulesNew());

    // 2. Limpiamos los datos (quitar precios fijos si es descuento, etc.)
    $data = $this->cleanData($this->new);

    // 3. Verificamos duplicados (excepto si estamos editando el mismo registro)
    $exists = \App\Models\PricelistItem::where('pricelist_id', $this->pricelist->id)
        ->where('applied_on', $data['applied_on'])
        ->where('min_qty', $data['min_qty'])
        ->where(function ($query) use ($data) {
            if ($data['applied_on'] === 'category') $query->where('category_id', $data['category_id']);
            if ($data['applied_on'] === 'template') $query->where('product_template_id', $data['product_template_id']);
            if ($data['applied_on'] === 'variant')  $query->where('product_variant_id', $data['product_variant_id']);
        })
        ->when(isset($this->new['id']), fn($q) => $q->where('id', '!=', $this->new['id']))
        ->exists();

    if ($exists) {
        $this->addError('new.applied_on', 'Ya existe una regla idéntica.');
        return;
    }

    // 4. GUARDADO (Esta es la parte que faltaba)
    if (isset($this->new['id'])) {
        // MODO EDICIÓN
        $item = PricelistItem::findOrFail($this->new['id']);
        $item->update($data);
        $title = 'Regla actualizada';
    } else {
        // MODO CREACIÓN
        PricelistItem::create([
            'pricelist_id' => $this->pricelist->id,
            ...$data
        ]);
        $title = 'Regla agregada';
    }

    // 5. Limpiar y Notificar
    $this->resetForm();
    $this->dispatch('swal', icon: 'success', title: '¡Hecho!', text: $title);
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

    public function addLineAntiguo()
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


    // Función auxiliar para limpiar campos que no corresponden al método elegido
    private function prepareData($input)
    {
        if ($input['compute_method'] == 'fixed') {
            $input['percent_discount'] = 0;
            $input['price_multiplier'] = 1;
        }
        if ($input['compute_method'] == 'discount') {
            $input['fixed_price'] = 0;
            $input['price_multiplier'] = 1;
        }
        return $input;
    }

    public function startEditcorregido(int $id)
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

    public function saveantiguo(int $id)
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


    public function save(int $id)
    {

        // Validamos usando el prefijo de la fila específica en el array $row
        $this->validate($this->getValidationRules("row.{$id}."));

        $item = PricelistItem::where('pricelist_id', $this->pricelist->id)->findOrFail($id);
        $data = $this->cleanData($this->row[$id]);

        $item->update($data);

        $this->dispatch(
            'notifyd',
            title: 'TICOM',
            text: 'La información de la empresa fue actualizada correctamente.',
            icon: 'success'
        );

        $this->cancelEdit($id);
    }

    #[On('deleteSingle')]
    public function deleteSingle($id, $name)
    {
        //PricelistItem::where('pricelist_id', $this->pricelist->id)->where('id', $id)->delete();
        //$this->dispatch('swal', icon: 'success', title: 'Eliminado', text: 'Regla eliminada.');

        $item = PricelistItem::findOrFail($id);
        $item->delete();

        // Opcional: Notificación con SweetAlert2 si lo tienes configurado
        $this->dispatch('swal', [
            'title' => 'Eliminado',
            'text' => 'La regla ha sido eliminada correctamente.',
            'icon' => 'success'
        ]);
        $this->dispatch('itemDeleted', title: 'TICOM', text: 'La regla ' . $name . ' con ID ' . $id . ' fue eliminado correctamente.', icon: 'success');
    }

    /* #[On('deleteSingle')]
    public function deleteSingle($id, $name)
    {
        Category::find($id)?->delete();

        //$this->dispatch('itemDeleted', title: 'TICOM', text: 'El usuario con {{$id}} fue eliminado correctamente.', icon: 'success');
        $this->dispatch('itemDeleted', title: 'TICOM', text: 'La categoría ' . $name . ' con ID ' . $id . ' fue eliminado correctamente.', icon: 'success');
    } */






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

    public function editLine($id)
    {
        $rule = PricelistItem::findOrFail($id);
        $this->new = $rule->toArray();
        // Asegúrate de que las fechas estén en formato Y-m-d para los inputs
        $this->new['date_start'] = $rule->date_start ? $rule->date_start->format('Y-m-d') : null;
        $this->new['date_end'] = $rule->date_end ? $rule->date_end->format('Y-m-d') : null;
    }

    public function resetFormBack()
    {
        $this->new = ['applied_on' => 'all', 'compute_method' => 'fixed', 'active' => true, 'sequence' => 1, 'base' => 'price_sale'];
        $this->resetErrorBag();
    }


    public function resetForm()
    {
        $this->new = [
            'applied_on' => 'all',
            'category_id' => null,
            'product_template_id' => null,
            'product_variant_id' => null,
            'sequence' => 10,
            'min_qty' => 1,
            'compute_method' => 'fixed',
            'fixed_price' => null,
            'percent_discount' => null,
            'base' => 'price_sale',
            'active' => true,
        ];
        $this->resetErrorBag();
    }





    // En tu clase Livewire
    public function startEditxx($id)
    {
        $item = PriceListItem::findOrFail($id); // Ajusta al nombre de tu modelo

        // Volcamos los datos al array que usa el formulario
        $this->new = $item->toArray();

        // Si usas fechas, a veces hay que formatearlas para el input type="date"
        $this->new['date_start'] = $item->date_start ? $item->date_start->format('Y-m-d') : null;
        $this->new['date_end'] = $item->date_end ? $item->date_end->format('Y-m-d') : null;
    }

    public function startEdit(int $id)
    {
        // 1. Limpiar errores previos de validación
        $this->resetErrorBag();

        // 2. Buscar el item asegurando que pertenezca a esta lista de precios
        $item = PricelistItem::where('pricelist_id', $this->pricelist->id)->findOrFail($id);

        // 3. Volcar los datos al array $new que usa el formulario de arriba
        $this->new = $item->toArray();

        // 4. Formatear fechas para que los inputs tipo 'date' las reconozcan
        if ($item->date_start) {
            $this->new['date_start'] = \Carbon\Carbon::parse($item->date_start)->format('Y-m-d');
        }
        if ($item->date_end) {
            $this->new['date_end'] = \Carbon\Carbon::parse($item->date_end)->format('Y-m-d');
        }
    }
}
