<?php

namespace App\Livewire\Admin\Attribute;

use App\Models\Attribute;
use Livewire\Component;

use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;



//php artisan make:livewire Admin/Attribute/AttributeList
#[Title('Atributos')]
class AttributeList extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    // ── Filtros ─────────────────────────────────────────────
    public string $search   = '';
    public string $status   = 'all'; // all | active | inactive
    public int    $perPage  = 10;

    // ── Selección masiva ─────────────────────────────────────
    public array $selectedItems = [];
    public bool  $selectAll     = false;

    // ── Ordenamiento ─────────────────────────────────────────
    public string $sortField     = 'order';
    public string $sortDirection = 'asc';

    // ── Columnas opcionales ──────────────────────────────────
    public array $columns = [
        'order'        => false,
        'values_count' => true,
    ];

    protected string $paginationTheme = 'tailwind';

    // ── Ciclo de vida ────────────────────────────────────────

    public function mount(): void
    {
        // Verifica permiso para ver el listado
        $this->authorize('viewAny', Attribute::class);
    }

    // ── Observadores ─────────────────────────────────────────

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    // ── Ordenamiento ─────────────────────────────────────────

    public function sortBy(string $field): void
    {
        // Solo campos válidos
        if (! in_array($field, ['id', 'name', 'order', 'state'], true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
        $this->resetSelection();
    }

    // ── Selección masiva ─────────────────────────────────────

    /**
     * Cuando se marca "seleccionar todos", carga los IDs del filtro activo.
     */
    public function updatedSelectAll(bool $value): void
    {
        if (! $value) {
            $this->selectedItems = [];
            return;
        }

        $this->selectedItems = $this->baseQuery()
            ->select('id')
            ->pluck('id')
            ->mapWithKeys(fn ($id) => [$id => true])
            ->toArray();
    }

    /**
     * Conteo de elementos seleccionados (computed property).
     */
    public function getSelectedCountProperty(): int
    {
        return count(array_keys(array_filter($this->selectedItems)));
    }

    /**
     * Resetea selección y checkbox global.
     */
    private function resetSelection(): void
    {
        $this->selectAll     = false;
        $this->selectedItems = [];
    }

    // ── Toggle estado ────────────────────────────────────────

    public function toggleState(int $id): void
    {
        try {
            $attribute = Attribute::findOrFail($id);
            $this->authorize('update', $attribute);

            $attribute->state = ! $attribute->state;
            $attribute->save();

            $this->dispatch('show-swalindex', [
                'title' => 'Actualizado',
                'text'  => 'Estado del atributo actualizado',
                'icon'  => 'success',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('show-swalindex', [
                'title' => 'Error',
                'text'  => 'No se pudo actualizar: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }

    // ── Eliminación individual ───────────────────────────────

    #[On('deleteSingle')]
    public function deleteSingle(int $id, string $name): void
    {
        try {
            $attribute = Attribute::findOrFail($id);
            $this->authorize('delete', $attribute);

            // ✅ Verificar si tiene valores asignados a variantes de producto
            if ($attribute->hasValuesInUse()) {
                $this->dispatch('show-swalindex', [
                    'title' => 'No se puede eliminar',
                    'text'  => 'El atributo "' . $name . '" tiene valores asignados a variantes de producto. Elimínalos primero.',
                    'icon'  => 'warning',
                ]);
                return;
            }

            // ✅ Verificar si tiene valores (aunque no asignados a variantes)
            if ($attribute->values()->exists()) {
                $this->dispatch('show-swalindex', [
                    'title' => 'No se puede eliminar',
                    'text'  => 'El atributo "' . $name . '" tiene valores registrados. Elimínalos primero.',
                    'icon'  => 'warning',
                ]);
                return;
            }

            $attribute->delete();

            unset($this->selectedItems[$id]);

            $this->dispatch('itemDeleted', [
                'title' => 'Eliminado',
                'text'  => 'El atributo "' . $name . '" fue eliminado correctamente',
                'icon'  => 'success',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('show-swalindex', [
                'title' => 'Error',
                'text'  => 'Error al eliminar: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }

    // ── Eliminación masiva ───────────────────────────────────

    #[On('confirmDeleteSelected')]
    public function deleteSelected(): void
    {
        try {
            $ids = array_keys(array_filter($this->selectedItems));

            if (empty($ids)) {
                return;
            }

            $attributes = Attribute::withCount('values')->whereIn('id', $ids)->get();

            // Verifica permiso por cada registro
            foreach ($attributes as $attribute) {
                $this->authorize('delete', $attribute);
            }

            // ✅ Verificar si alguno tiene valores asignados a variantes
            $hasVariantValues = Attribute::whereIn('id', $ids)
                ->whereHas('values.variants')
                ->exists();

            if ($hasVariantValues) {
                $this->dispatch('show-swalindex', [
                    'title' => 'No se puede eliminar',
                    'text'  => 'Uno o más atributos tienen valores asignados a variantes de producto. Elimínalos primero.',
                    'icon'  => 'warning',
                ]);
                return;
            }

            // ✅ Verificar si alguno tiene valores registrados
            $hasValues = Attribute::whereIn('id', $ids)
                ->whereHas('values')
                ->exists();

            if ($hasValues) {
                $this->dispatch('show-swalindex', [
                    'title' => 'No se puede eliminar',
                    'text'  => 'Uno o más atributos tienen valores registrados. Elimínalos primero.',
                    'icon'  => 'warning',
                ]);
                return;
            }

            Attribute::whereIn('id', $ids)->delete();

            $this->resetSelection();

            $this->dispatch('itemDeleted', [
                'title' => 'Eliminado',
                'text'  => count($ids) . ' atributo(s) eliminado(s) correctamente',
                'icon'  => 'success',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('show-swalindex', [
                'title' => 'Error',
                'text'  => 'Error al eliminar: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }

    // ── Query base reutilizable ──────────────────────────────

    private function baseQuery()
    {
        $query = Attribute::query();

        if ($this->search) {
            $s = trim($this->search);
            $query->where('name', 'like', "%{$s}%");
        }

        if ($this->status === 'active') {
            $query->where('state', true);
        } elseif ($this->status === 'inactive') {
            $query->where('state', false);
        }

        return $query;
    }

    // ── Render ───────────────────────────────────────────────

    public function render()
    {
        // Incluye conteo de valores para mostrar en tabla (evita N+1)
        $attributes = $this->baseQuery()
            ->withCount('values')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.attribute.attribute-list', compact('attributes'));
    }
}
