<?php

namespace App\Livewire\Admin\Attribute;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Livewire\Attributes\On;

//php artisan make:livewire Admin/Attribute/AttributeValueManager
class AttributeValueManager extends Component
{
    use WithPagination;
    public Attribute $attribute;
    public string $search = '';
    public int $perPage = 10;

    // Form rápido “Agregar línea”
    public string $newName = '';
    public $newExtraPrice = 0;

    // Para edición inline por fila
    public array $editing = [];   // [id => true]
    public array $name = [];      // [id => 'S']
    public array $extra_price = []; // [id => 0.00]
    public array $active = [];    // [id => true]




    public function updatingSearch()
    {
        $this->resetPage();
    }


 public function startEdit(int $id): void
    {
        $value = AttributeValue::where('attribute_id', $this->attribute->id)->findOrFail($id);

        $this->editing[$id] = true;
        $this->name[$id] = $value->name;
        $this->extra_price[$id] = (float) $value->extra_price;
        $this->active[$id] = (bool) $value->active;
    }

    public function cancelEdit(int $id): void
    {
        unset($this->editing[$id], $this->name[$id], $this->extra_price[$id], $this->active[$id]);
    }

    private function normalizeValueName(string $name): string
    {
        $name = trim($name);
        $name = preg_replace('/\s+/', ' ', $name);

        // Si el atributo es "Talla", normalmente se guarda en MAYÚSCULA:
        if (mb_strtolower($this->attribute->name, 'UTF-8') === 'talla') {
            $name = mb_strtoupper($name, 'UTF-8');
        } else {
            // Capitaliza general
            $name = mb_convert_case(mb_strtolower($name, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
        }

        return $name;
    }

    private function existsValueName(string $name, ?int $ignoreId = null): bool
    {
        $q = AttributeValue::where('attribute_id', $this->attribute->id)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name, 'UTF-8')]);

        if ($ignoreId) {
            $q->where('id', '!=', $ignoreId);
        }

        return $q->exists();
    }

    public function addLine(): void
    {
        $this->validate([
            'newName' => ['required', 'string', 'max:50'],
            'newExtraPrice' => ['nullable', 'numeric', 'min:0'],
        ], [
            'newName.required' => 'Escribe un valor.',
        ]);

        $name = $this->normalizeValueName($this->newName);

        if ($this->existsValueName($name)) {
            $this->addError('newName', 'Este valor ya existe para este atributo.');
            return;
        }

        $maxSort = (int) (AttributeValue::where('attribute_id', $this->attribute->id)->max('sort_order') ?? 0);

        AttributeValue::create([
            'attribute_id' => $this->attribute->id,
            'name' => $name,
            'extra_price' => (float) ($this->newExtraPrice ?? 0),
            'sort_order' => $maxSort + 1,
            'active' => true,
        ]);

        $this->reset(['newName', 'newExtraPrice']);
    }

    public function save(int $id): void
    {
        $value = AttributeValue::where('attribute_id', $this->attribute->id)->findOrFail($id);

        $name = $this->normalizeValueName($this->name[$id] ?? '');

        if ($name === '') {
            $this->dispatch('toast', type: 'error', message: 'El nombre no puede estar vacío.');
            return;
        }

        if ($this->existsValueName($name, $id)) {
            $this->dispatch('toast', type: 'error', message: 'Ese valor ya existe.');
            return;
        }

        $extra = $this->extra_price[$id] ?? 0;
        $extra = is_numeric($extra) ? (float) $extra : 0;

        $value->update([
            'name' => $name,
            'extra_price' => $extra,
            'active' => (bool) ($this->active[$id] ?? false),
        ]);

        $this->cancelEdit($id);
    }

    public function delete(int $id): void
    {
        $value = AttributeValue::where('attribute_id', $this->attribute->id)->findOrFail($id);
        $value->delete();
    }


    #[On('deleteSingle')]
    public function deleteSingle($id, $name)
    {
        AttributeValue::find($id)?->delete();

        $this->dispatch('itemDeleted', title: 'TICOM', text: 'El valor ' . $name . ' con ID ' . $id . ' fue eliminado correctamente.', icon: 'success');
    }



    public function render()
    {
        $values = AttributeValue::query()
            ->where('attribute_id', $this->attribute->id)
            ->when($this->search !== '', function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%');
            })
            ->orderByRaw('COALESCE(sort_order, 999999) asc')
            ->orderBy('id', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.attribute.attribute-value-manager', [
            'values' => $values,
        ]);
    }
    /* public function render()
    {
        return view('livewire.admin.attribute.attribute-value-manager');
    } */

}
