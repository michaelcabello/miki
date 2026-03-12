<?php

namespace App\Livewire\Admin\Journaltype;

use Livewire\Component;

use App\Models\JournalType;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Title;

#[Title('Tipos de Diario')]
class JournalTypeList extends Component
{

    use WithPagination;
    use AuthorizesRequests; //para permisos

    public $search = '';
    public $status = 'all'; // all|active|inactive
    public $perPage = 10;

    public $selectedItems = [];
    public $selectAll = false;

    public $sortField = 'order';      // order|id|code|name|state
    public $sortDirection = 'asc';    // asc|desc
    public string $title = 'Tipos de Diario';

    protected $paginationTheme = 'tailwind';

    // Columnas opcionales para mostrar/ocultar
    public $columns = [
        'order'  => false,
    ];


    public function mount(): void
    {
        $this->authorize('viewAny', JournalType::class); //para ver la lista eso es viewAny
         $this->title = 'Tipos de Diario';
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->resetSelection();
    }
    public function updatingStatus()
    {
        $this->resetPage();
        $this->resetSelection();
    }
    public function updatingPerPage()
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function sortBy($field)
    {
        if (!in_array($field, ['order', 'id', 'code', 'name', 'state'])) return;

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
        $this->resetSelection();
    }

    private function resetSelection()
    {
        $this->selectAll = false;
        $this->selectedItems = [];
    }

    public function getSelectedCountProperty()
    {
        return count(array_keys(array_filter($this->selectedItems)));
    }

    /**
     * Query base
     */
    private function baseQuery()
    {
        $query = JournalType::query();

        if ($this->search) {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('code', 'like', "%{$s}%")
                    ->orWhere('name', 'like', "%{$s}%");
            });
        }

        if ($this->status === 'active') {
            $query->where('state', true);
        } elseif ($this->status === 'inactive') {
            $query->where('state', false);
        }

        return $query;
    }

    /**
     * Seleccionar todos (respeta filtros actuales)
     */
    public function updatedSelectAll($value)
    {
        if (!$value) {
            $this->selectedItems = [];
            return;
        }

        $ids = $this->baseQuery()->select('id')->pluck('id');

        $this->selectedItems = $ids->mapWithKeys(fn($id) => [$id => true])->toArray();
    }

    public function toggleState($id)
    {
        try {
            $item = JournalType::findOrFail($id);

            // Verifica permiso de actualización antes de cambiar el estado
            $this->authorize('update', $item);

            $item->state = !$item->state;
            $item->save();

            $this->dispatch('show-swalindex', [
                'title' => 'Actualizado',
                'text'  => 'Estado del tipo de diario actualizado',
                'icon'  => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('show-swalindex', [
                'title' => 'Error',
                'text'  => 'No se pudo actualizar: ' . $e->getMessage(),
                'icon'  => 'error'
            ]);
        }
    }

    #[On('confirmDeleteSelectedback')]
    public function deleteSelectedback()
    {
        try {
            $ids = array_keys(array_filter($this->selectedItems));
            if (empty($ids)) return;

            //JournalType::whereIn('id', $ids)->delete();


            $items = JournalType::whereIn('id', $ids)->get();

            foreach ($items as $item) {
                $this->authorize('delete', $item); // 🔐 verifica permiso por cada registro
            }

            JournalType::whereIn('id', $ids)->delete();


            $this->resetSelection();

            $this->dispatch('itemDeleted', [
                'title' => 'Eliminado',
                'text'  => count($ids) . ' registro(s) eliminado(s) correctamente',
                'icon'  => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('show-swalindex', [
                'title' => 'Error',
                'text'  => 'Error al eliminar: ' . $e->getMessage(),
                'icon'  => 'error'
            ]);
        }
    }

    #[On('deleteSingleback')]
    public function deleteSingleback($id, $name)
    {

        try {
            $item = JournalType::findOrFail($id);
            $this->authorize('delete', $item); // Verificar permiso antes de eliminar
            $item->delete();

            unset($this->selectedItems[$id]);

            $this->dispatch('itemDeleted', [
                'title' => 'Eliminado',
                'text'  => 'El registro "' . $name . '" fue eliminado correctamente',
                'icon'  => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('show-swalindex', [
                'title' => 'Error',
                'text'  => 'Error al eliminar: ' . $e->getMessage(),
                'icon'  => 'error'
            ]);
        }
    }



    // ─────────────────────────────────────────
    // deleteSelected() — eliminación múltiple
    // ─────────────────────────────────────────
    #[On('confirmDeleteSelected')]
    public function deleteSelected(): void
    {
        try {
            $ids = array_keys(array_filter($this->selectedItems));
            if (empty($ids)) return;

            $items = JournalType::whereIn('id', $ids)->get();

            // Verifica permiso de eliminación por cada registro
            foreach ($items as $item) {
                $this->authorize('delete', $item);
            }

            // ✅ PUNTO 7 — Verifica si alguno tiene diarios asociados
            $hasRelations = JournalType::whereIn('id', $ids)
                ->whereHas('journals')
                ->exists();

            if ($hasRelations) {
                $this->dispatch('show-swalindex', [
                    'title' => 'No se puede eliminar',
                    'text'  => 'Uno o más tipos de diario tienen diarios asociados. Elimínalos primero.',
                    'icon'  => 'warning',
                ]);
                return; // ← aborta aquí, no elimina nada
            }

            JournalType::whereIn('id', $ids)->delete();

            $this->resetSelection();

            $this->dispatch('itemDeleted', [
                'title' => 'Eliminado',
                'text'  => count($ids) . ' registro(s) eliminado(s) correctamente',
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


    // ─────────────────────────────────────────
    // deleteSingle() — eliminación individual
    // ─────────────────────────────────────────
    #[On('deleteSingle')]
    public function deleteSingle(int $id, string $name): void
    {
        try {
            $item = JournalType::findOrFail($id);

            // Verifica permiso antes de eliminar
            $this->authorize('delete', $item);

            // ✅ PUNTO 7 — Verifica si tiene diarios asociados
            if ($item->journals()->exists()) {
                $this->dispatch('show-swalindex', [
                    'title' => 'No se puede eliminar',
                    'text'  => 'El tipo "' . $name . '" tiene diarios asociados. Elimínalos primero.',
                    'icon'  => 'warning',
                ]);
                return; // ← aborta aquí
            }

            $item->delete();

            unset($this->selectedItems[$id]);

            $this->dispatch('itemDeleted', [
                'title' => 'Eliminado',
                'text'  => 'El registro "' . $name . '" fue eliminado correctamente',
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






    public function render()
    {

        $journalTypes = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.journaltype.journal-type-list', compact('journalTypes'));
    }
}
