<?php

namespace App\Livewire\Admin\Warehouse;

use Livewire\Component;
use App\Models\Warehouse;
use App\Traits\WithStandardTable;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

#[Title('Almacenes')]
class WarehouseList extends Component
{
    use WithStandardTable;

    public function mount(): void
    {
        $this->authorize('viewAny', Warehouse::class);
        $this->sortField = 'order';

        $this->columns = [
            'order'   => false,
            'code'    => true,
            'name'    => true,
            'is_main' => true,
            'state'   => true,
        ];
    }

    // ─── QUERY BASE ──────────────────────────────────────────────

    public function baseQuery()
    {
        $query = $this->showTrashed
            ? Warehouse::onlyTrashed()
            : Warehouse::query();

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

    // ─── CRUD INDIVIDUAL ─────────────────────────────────────────

    #[On('deleteSingle')]
    public function deleteSingle(int $id, string $name): void
    {
        $item = Warehouse::findOrFail($id);
        $this->authorize('delete', $item);

        // Validación de integridad: no borrar si tiene ubicaciones activas
        if ($item->locations()->exists()) {
            $this->dispatch('show-swalindex', [
                'title' => 'Acción bloqueada',
                'text'  => "El almacén '{$name}' tiene ubicaciones asociadas. Elimínalas primero.",
                'icon'  => 'warning',
            ]);
            return;
        }

        $item->delete();

        $this->dispatch('show-swalindex', [
            'title' => '¡Eliminado!',
            'text'  => "El almacén '{$name}' se movió a la papelera.",
            'icon'  => 'success',
        ]);
    }

    #[On('restoreSingle')]
    public function restoreSingle(int $id, string $name): void
    {
        try {
            $item = Warehouse::onlyTrashed()->findOrFail($id);
            $this->authorize('restore', $item);
            $item->restore();
            $this->resetSelection();

            $this->dispatch('show-swalindex', [
                'icon'  => 'success',
                'title' => '¡Restaurado!',
                'text'  => "El almacén '{$name}' ha vuelto a la lista activa.",
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->dispatch('show-swalindex', [
                'icon'  => 'error',
                'title' => 'Acceso denegado',
                'text'  => 'No tienes permiso para restaurar almacenes.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('show-swalindex', [
                'icon'  => 'error',
                'title' => 'Error al restaurar',
                'text'  => $e->getMessage(),
            ]);
        }
    }

    #[On('forceDeleteSingle')]
    public function forceDeleteSingle(int $id, string $name): void
    {
        try {
            $item = Warehouse::onlyTrashed()->findOrFail($id);
            $this->authorize('forceDelete', $item);
            $item->forceDelete();

            $this->dispatch('show-swalindex', [
                'icon'  => 'success',
                'title' => '¡Borrado Permanente!',
                'text'  => "El almacén '{$name}' fue eliminado definitivamente.",
            ]);
        } catch (\Exception $e) {
            $this->dispatch('show-swalindex', [
                'icon'  => 'error',
                'title' => 'Error',
                'text'  => $e->getMessage(),
            ]);
        }
    }

    // ─── ACCIONES MASIVAS ────────────────────────────────────────

    #[On('confirmDeleteSelected')]
    public function deleteSelected(): void
    {
        try {
            $ids = array_keys(array_filter($this->selectedItems, fn($v) => $v == true));

            if (empty($ids)) {
                $this->dispatch('show-swalindex', [
                    'title' => 'Atención',
                    'text'  => 'No hay registros seleccionados válidos.',
                    'icon'  => 'warning',
                ]);
                return;
            }

            DB::transaction(function () use ($ids) {
                foreach (Warehouse::whereIn('id', $ids)->get() as $item) {
                    $this->authorize('delete', $item);

                    if ($item->locations()->exists()) {
                        throw new \Exception(
                            "El almacén '{$item->name}' tiene ubicaciones asociadas y no puede eliminarse."
                        );
                    }

                    $item->delete();
                }
            });

            $this->resetSelection();
            $this->dispatch('show-swalindex', [
                'title' => '¡Éxito!',
                'text'  => count($ids) . ' almacén(es) eliminado(s) correctamente.',
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

    #[On('confirmRestoreSelected')]
    public function restoreSelected(): void
    {
        $this->authorize('restore', new Warehouse());
        $ids = array_keys(array_filter($this->selectedItems, fn($v) => $v === true));
        if (empty($ids)) return;

        Warehouse::onlyTrashed()->whereIn('id', $ids)->restore();
        $this->resetSelection();

        $this->dispatch('show-swalindex', [
            'title' => '¡Recuperados!',
            'text'  => count($ids) . ' almacén(es) restaurado(s).',
            'icon'  => 'success',
        ]);
    }

    #[On('confirmForceDeleteSelected')]
    public function forceDeleteSelected(): void
    {
        try {
            $ids = array_keys(array_filter($this->selectedItems, fn($v) => $v == true));
            if (empty($ids)) return;

            DB::transaction(function () use ($ids) {
                foreach (Warehouse::onlyTrashed()->whereIn('id', $ids)->get() as $item) {
                    $this->authorize('forceDelete', $item);
                    $item->forceDelete();
                }
            });

            $this->resetSelection();
            $this->dispatch('show-swalindex', [
                'title' => '¡Limpieza Exitosa!',
                'text'  => count($ids) . ' almacén(es) borrado(s) permanentemente.',
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

    // ─── TOGGLE STATE ────────────────────────────────────────────

    public function toggleState(int $id): void
    {
        $item = Warehouse::findOrFail($id);
        $this->authorize('update', $item);
        $item->state = !$item->state;
        $item->save();
    }

    // ─── RENDER ──────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.admin.warehouse.warehouse-list', [
            'warehouses'    => $this->baseQuery()
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage),
            'selectedCount' => $this->selectedCount,
        ]);
    }
}
