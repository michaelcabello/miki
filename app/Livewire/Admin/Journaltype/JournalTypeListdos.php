<?php

namespace App\Livewire\Admin\Journaltype;

use Livewire\Component;
use App\Models\JournalType;
use App\Traits\WithStandardTable; // 🚀 Importado de la nueva ubicación
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

#[Title('Tipos de Diario')]
class JournalTypeListdos extends Component
{

    use WithStandardTable;
    public $showTrashed = false; // 🚀 Agrega esta propiedad al inicio de la clase

    public function mount(): void
    {
        // 🔒 Seguridad de Nivel 1: Ver la lista
        $this->authorize('viewAny', JournalType::class);
        $this->sortField = 'order'; // Valor inicial específico para este módulo
    }


    #[On('deleteSingle')]
    public function deleteSingle($data): void
    {
        // Livewire 3 recibe los parámetros del dispatch de JS como un array
        $id = $data['id'];
        $name = $data['name'];

        $item = JournalType::findOrFail($id);
        $this->authorize('delete', $item);

        if ($item->journals()->exists()) {
            $this->dispatch('show-swalindex', ['text' => 'Tiene diarios asociados.', 'icon' => 'warning']);
            return;
        }

        $item->delete();
        $this->dispatch('itemDeleted', [
            'title' => 'Eliminado',
            'text' => "Registro '{$name}' eliminado correctamente",
            'icon' => 'success'
        ]);
    }



    public function baseQuery()
    {
        // 🚀 Si $showTrashed es true, usamos onlyTrashed()
        $query = $this->showTrashed
            ? JournalType::onlyTrashed()
            : JournalType::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('code', 'like', "%{$this->search}%");
            });
        }

        if ($this->status !== 'all' && !$this->showTrashed) {
            $query->where('state', $this->status === 'active');
        }

        return $query;
    }

    // 🚀 MÉTODO PARA RESTAURAR
    public function restore($id)
    {
        $this->authorize('JournalType Restore'); // Permiso específico

        $item = JournalType::onlyTrashed()->findOrFail($id);
        $item->restore();

        $this->dispatch('show-swalindex', [
            'title' => 'Restaurado',
            'text' => "El registro '{$item->name}' ha vuelto a la lista activa.",
            'icon' => 'success'
        ]);
    }


    #[On('confirmRestoreSelected')]
    public function restoreSelected()
    {
        $this->authorize('JournalType Restore');

        $ids = array_keys(array_filter($this->selectedItems, fn($val) => $val === true));

        if (empty($ids)) return;

        JournalType::onlyTrashed()->whereIn('id', $ids)->restore();

        $this->resetSelection();
        $this->dispatch('itemDeleted', [
            'title' => '¡Recuperados!',
            'text'  => count($ids) . ' registros han sido restaurados.',
            'icon'  => 'success'
        ]);
    }





    public function toggleState($id)
    {
        $item = JournalType::findOrFail($id);
        // 🔒 Seguridad de Nivel 2: Permiso de edición
        $this->authorize('update', $item);

        $item->state = !$item->state;
        $item->save();
    }



    #[On('confirmDeleteSelected')]
    public function deleteSelected(): void
    {
        try {
            // 1. Extraemos solo las llaves (IDs) de los elementos que son exactamente true o truthy
            $ids = array_keys(array_filter($this->selectedItems, fn($value) => $value == true));

            if (empty($ids)) {
                $this->dispatch('show-swalindex', [
                    'title' => 'Atención',
                    'text'  => 'No hay registros seleccionados válidos.',
                    'icon'  => 'warning',
                ]);
                return;
            }

            // 2. Iniciamos transacción para asegurar que la eliminación se ejecute
            DB::transaction(function () use ($ids) {
                $items = JournalType::whereIn('id', $ids)->get();

                foreach ($items as $item) {
                    // Validación de seguridad (Policy/Roles)
                    $this->authorize('delete', $item);

                    // Validación de integridad (evitar borrar si tiene diarios)
                    if ($item->journals()->exists()) {
                        throw new \Exception("El registro '{$item->name}' no puede eliminarse porque tiene movimientos asociados.");
                    }

                    // 🚀 Ejecución del borrado (Soft Delete)
                    $item->delete();
                }
            });

            // 3. Limpiar selección y refrescar interfaz
            $this->resetSelection();

            $this->dispatch('itemDeleted', [
                'title' => '¡Éxito!',
                'text'  => count($ids) . ' registro(s) eliminado(s) correctamente.',
                'icon'  => 'success',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('show-swalindex', [
                'title' => 'Error',
                'text'  => $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }



    public function render()
    {
        return view('livewire.admin.journaltype.journal-type-listdos', [
            'journalTypes' => $this->baseQuery()
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage),
            'selectedCount' => $this->selectedCount // Propiedad computada del Trait
        ]);
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

        // Obtenemos los IDs del query filtrado actual
        $ids = $this->baseQuery()->select('id')->pluck('id');

        // 🚀 TIP SENIOR: Convertimos el ID a string para que el binding
        // de Livewire "selectedItems.1" funcione perfectamente siempre.
        $this->selectedItems = $ids->mapWithKeys(fn($id) => [(string)$id => true])->toArray();
    }
}
