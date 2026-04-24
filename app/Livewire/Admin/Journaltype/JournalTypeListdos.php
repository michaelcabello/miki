<?php

namespace App\Livewire\Admin\Journaltype;
//use App\Exports\Admin\JournalTypeExport;
use Livewire\Component;
use App\Models\JournalType;
use App\Traits\WithStandardTable; // 🚀 Importado de la nueva ubicación
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

//use Maatwebsite\Excel\Facades\Excel;
//use Barryvdh\DomPDF\Facade\Pdf;

#[Title('Tipos de Diario')]
class JournalTypeListdos extends Component
{
    use WithStandardTable;
    //public $showTrashed = false; // 🚀 Agrega esta propiedad al inicio de la clase, es para ocultar al inicio el softdeletes
    //public $search = '';

    public function mount(): void
    {
        // Seguridad de Nivel 1: Ver la lista
        $this->authorize('viewAny', JournalType::class);
        $this->sortField = 'order'; // Valor inicial específico para este módulo

        $this->columns = [
            'order' => false,
            'code'  => true,
            'name'  => true,
            'state' => true,
        ];
    }


    #[On('deleteSingle')] // 🚀 Escucha el evento global
    public function deleteSingle($id, $name): void // 👈 Cambiamos $data por los nombres reales
    {
        // Buscamos el registro
        $item = JournalType::findOrFail($id);
        // 🔒 Seguridad: Validamos Policy
        $this->authorize('delete', $item);
        // Validación de integridad contable
        if ($item->journals()->exists()) {
            $this->dispatch('show-swalindex', [
                'title' => 'Acción bloqueada',
                'text' => "El registro '{$name}' tiene movimientos asociados.",
                'icon' => 'warning'
            ]);
            return;
        }
        // 🚀 Borrado lógico
        $item->delete();
        // Notificación de éxito
        $this->dispatch('show-swalindex', [
            'title' => '¡Eliminado!',
            'text' => "El registro '{$name}' se movió a la papelera.",
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





    #[On('restoreSingle')]
    public function restoreSingle(int $id, string $name): void
    {
        try {
            // Paso 1: Localizar el registro en la papelera ANTES de autorizar
            // (necesitamos la instancia para que la Policy funcione correctamente)
            $item = JournalType::onlyTrashed()->findOrFail($id);

            // Paso 2: Validar seguridad pasando la INSTANCIA a la Policy
            $this->authorize('restore', $item);

            // Paso 3: Ejecutar la restauración
            $item->restore();

            // Paso 4: Limpiar selección y notificar
            $this->resetSelection();

            $this->dispatch('show-swalindex', [
                'icon'  => 'success',
                'title' => '¡Registro Restaurado!',
                'text'  => "El tipo de diario '{$name}' ha vuelto a la lista activa.",
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->dispatch('show-swalindex', [
                'icon'  => 'error',
                'title' => 'Acceso denegado',
                'text'  => 'No tienes permiso para restaurar registros.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('show-swalindex', [
                'icon'  => 'error',
                'title' => 'Error al restaurar',
                'text'  => 'No se pudo recuperar el registro: ' . $e->getMessage(),
            ]);
        }
    }





    #[On('confirmRestoreSelected')]
    public function restoreSelected()
    {
        $this->authorize('JournalType Restore');

        $ids = array_keys(array_filter($this->selectedItems, fn($val) => $val === true));

        if (empty($ids)) return;

        JournalType::onlyTrashed()->whereIn('id', $ids)->restore();

        $this->resetSelection();
        $this->dispatch('show-swalindex', [
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

            $this->dispatch('show-swalindex', [
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




    #[On('forceDeleteSingle')]
    public function forceDeleteSingle($id, $name): void
    {
        try {
            // 🔍 1. Buscamos el registro exclusivamente en la papelera
            $item = JournalType::onlyTrashed()->findOrFail($id);

            // 🔒 2. Seguridad: Validamos mediante la Policy
            $this->authorize('forceDelete', $item);

            // 🚀 3. Ejecutamos la eliminación FÍSICA de la base de datos
            $item->forceDelete();

            // 📢 4. Notificamos el éxito
            $this->dispatch('show-swalindex', [
                'title' => '¡Borrado Permanente!',
                'text' => "El registro '{$name}' ha sido eliminado definitivamente del sistema.",
                'icon' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('show-swalindex', ['icon' => 'error', 'text' => $e->getMessage()]);
        }
    }

    #[On('confirmForceDeleteSelected')]
    public function forceDeleteSelected(): void
    {
        try {
            // 1. Obtenemos los IDs seleccionados
            $ids = array_keys(array_filter($this->selectedItems, fn($value) => $value == true));

            if (empty($ids)) return;

            // 2. Transacción para asegurar integridad
            DB::transaction(function () use ($ids) {
                $items = JournalType::onlyTrashed()->whereIn('id', $ids)->get();

                foreach ($items as $item) {
                    $this->authorize('forceDelete', $item);
                    $item->forceDelete();
                }
            });

            // 3. Limpiar y refrescar
            $this->resetSelection();
            $this->dispatch('show-swalindex', [
                'title' => '¡Limpieza Exitosa!',
                'text'  => count($ids) . ' registro(s) borrados permanentemente.',
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
}
