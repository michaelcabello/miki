<?php

namespace App\Livewire\Admin\Journal;

use Livewire\Component;
use App\Models\Journal;
use App\Models\JournalType;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Title;
//php artisan make:livewire Admin/Journal/JournalList

#[Title('Diarios')]
class JournalList extends Component
{

    use WithPagination;
    use AuthorizesRequests;

    public $search         = '';
    public $status         = 'all';   // all|active|inactive
    public $journalTypeId  = '';      // filtro por tipo de diario
    public $perPage        = 10;

    public $selectedItems  = [];
    public $selectAll      = false;

    public $sortField     = 'name';
    public $sortDirection = 'asc';

    public string $title = 'Diarios';

    protected $paginationTheme = 'tailwind';

    // Columnas opcionales visibles
    public $columns = [
        'currency'     => false,
        'use_documents' => false,
    ];

    public function mount(): void
    {
        $this->authorize('viewAny', Journal::class);
    }

    // Resetear paginación y selección al cambiar filtros
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
    public function updatingJournalTypeId()
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function sortBy($field): void
    {
        // Valida campos permitidos para ordenar
        $allowed = ['id', 'code', 'name', 'state', 'journal_type_id'];
        if (!in_array($field, $allowed)) return;

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
        $this->resetSelection();
    }

    private function resetSelection(): void
    {
        $this->selectAll     = false;
        $this->selectedItems = [];
    }

    public function getSelectedCountProperty(): int
    {
        return count(array_keys(array_filter($this->selectedItems)));
    }

    /** Query base reutilizable — aplica todos los filtros activos */
    private function baseQuery()
    {
        $query = Journal::query()->with(['journalType', 'currency']);

        if ($this->search) {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('code', 'like', "%{$s}%")
                    ->orWhere('name', 'like', "%{$s}%");
            });
        }

        if ($this->status === 'active')       $query->where('state', true);
        elseif ($this->status === 'inactive') $query->where('state', false);

        if ($this->journalTypeId) {
            $query->where('journal_type_id', $this->journalTypeId);
        }

        return $query;
    }

    /** Seleccionar todos los registros filtrados */
    public function updatedSelectAll($value): void
    {
        if (!$value) {
            $this->selectedItems = [];
            return;
        }
        $ids = $this->baseQuery()->select('id')->pluck('id');
        $this->selectedItems = $ids->mapWithKeys(fn($id) => [$id => true])->toArray();
    }

    /** Cambia el estado activo/inactivo del diario */
    public function toggleState(int $id): void
    {
        try {
            $item = Journal::findOrFail($id);
            $this->authorize('update', $item);
            $item->state = !$item->state;
            $item->save();

            $this->dispatch('show-swalindex', [
                'title' => 'Actualizado',
                'text'  => 'Estado del diario actualizado',
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

    /** Eliminación múltiple */
    #[On('confirmDeleteSelected')]
    public function deleteSelected(): void
    {
        try {
            $ids = array_keys(array_filter($this->selectedItems));
            if (empty($ids)) return;

            $items = Journal::whereIn('id', $ids)->get();
            foreach ($items as $item) {
                $this->authorize('delete', $item);
            }

            Journal::whereIn('id', $ids)->delete();
            $this->resetSelection();

            $this->dispatch('itemDeleted', [
                'title' => 'Eliminado',
                'text'  => count($ids) . ' diario(s) eliminado(s) correctamente',
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

    /** Eliminación individual */
    #[On('deleteSingle')]
    public function deleteSingle(int $id, string $name): void
    {
        try {
            $item = Journal::findOrFail($id);
            $this->authorize('delete', $item);
            $item->delete();

            unset($this->selectedItems[$id]);

            $this->dispatch('itemDeleted', [
                'title' => 'Eliminado',
                'text'  => 'El diario "' . $name . '" fue eliminado correctamente',
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
        $journals     = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        // Para el filtro de tipo de diario — solo los activos
        $journalTypes = JournalType::where('state', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return view('livewire.admin.journal.journal-list', compact('journals', 'journalTypes'));
    }

}
