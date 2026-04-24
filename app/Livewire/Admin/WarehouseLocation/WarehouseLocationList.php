<?php

namespace App\Livewire\Admin\WarehouseLocation;

use Livewire\Component;
use App\Models\WarehouseLocation;
use App\Traits\WithStandardTable;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

#[Title('Ubicaciones de Almacén')]
class WarehouseLocationList extends Component
{
    use WithStandardTable;

    // Filtro adicional por tipo de ubicación
    public string $filterUsage     = 'all';
    public string $filterWarehouse = 'all';

    public function mount(): void
    {
        $this->authorize('viewAny', WarehouseLocation::class);
        $this->sortField = 'order';

        $this->columns = [
            'order'    => false,
            'code'     => true,
            'name'     => true,
            'usage'    => true,
            'warehouse'=> true,
            'state'    => true,
        ];
    }

    // Resetear página al cambiar filtros adicionales
    public function updatingFilterUsage(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingFilterWarehouse(): void
    {
        $this->resetPage();
        $this->resetSelection();
    }

    // ─── QUERY BASE ─────────────────────────────────────────────

    public function baseQuery()
    {
        $query = $this->showTrashed
            ? WarehouseLocation::onlyTrashed()
            : WarehouseLocation::query();

        // Prevenir N+1: cargar relaciones que se muestran en tabla
        $query->with([
            'warehouse:id,code,name',
            'parent:id,name,complete_name',
        ]);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%")
                  ->orWhere('complete_name', 'like', "%{$this->search}%");
            });
        }

        if ($this->status !== 'all' && !$this->showTrashed) {
            $query->where('state', $this->status === 'active');
        }

        if ($this->filterUsage !== 'all') {
            $query->where('usage', $this->filterUsage);
        }

        if ($this->filterWarehouse !== 'all') {
            $query->where('warehouse_id', $this->filterWarehouse);
        }

        return $query;
    }

    // ─── CRUD INDIVIDUAL ────────────────────────────────────────

    #[On('deleteSingle')]
    public function deleteSingle(int $id, string $name): void
    {
        $item = WarehouseLocation::findOrFail($id);
        $this->authorize('delete', $item);

        // Validación de integridad: no eliminar si tiene ubicaciones hijas
        if ($item->children()->exists()) {
            $this->dispatch('show-swalindex', [
                'title' => 'Acción bloqueada',
                'text'  => "La ubicación '{$name}' tiene sub-ubicaciones asociadas. Elimínalas primero.",
                'icon'  => 'warning',
            ]);
            return;
        }

        $item->delete();

        $this->dispatch('show-swalindex', [
            'title' => '¡Eliminado!',
            'text'  => "La ubicación '{$name}' se movió a la papelera.",
            'icon'  => 'success',
        ]);
    }

    #[On('restoreSingle')]
    public function restoreSingle(int $id, string $name): void
    {
        try {
            $item = WarehouseLocation::onlyTrashed()->findOrFail($id);
            $this->authorize('restore', $item);
            $item->restore();
            $this->resetSelection();

            $this->dispatch('show-swalindex', [
                'icon'  => 'success',
                'title' => '¡Restaurado!',
                'text'  => "La ubicación '{$name}' ha vuelto a la lista activa.",
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->dispatch('show-swalindex', [
                'icon'  => 'error',
                'title' => 'Acceso denegado',
                'text'  => 'No tienes permiso para restaurar ubicaciones.',
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
            $item = WarehouseLocation::onlyTrashed()->findOrFail($id);
            $this->authorize('forceDelete', $item);
            $item->forceDelete();

            $this->dispatch('show-swalindex', [
                'icon'  => 'success',
                'title' => '¡Borrado Permanente!',
                'text'  => "La ubicación '{$name}' fue eliminada definitivamente.",
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
                    'text'  => 'No hay registros seleccionados.',
                    'icon'  => 'warning',
                ]);
                return;
            }

            DB::transaction(function () use ($ids) {
                foreach (WarehouseLocation::whereIn('id', $ids)->get() as $item) {
                    $this->authorize('delete', $item);

                    if ($item->children()->exists()) {
                        throw new \Exception(
                            "La ubicación '{$item->name}' tiene sub-ubicaciones y no puede eliminarse."
                        );
                    }

                    $item->delete();
                }
            });

            $this->resetSelection();

            $this->dispatch('show-swalindex', [
                'title' => '¡Éxito!',
                'text'  => count($ids) . ' ubicación(es) eliminada(s) correctamente.',
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
        $this->authorize('restore', new WarehouseLocation());

        $ids = array_keys(array_filter($this->selectedItems, fn($v) => $v === true));
        if (empty($ids)) return;

        WarehouseLocation::onlyTrashed()->whereIn('id', $ids)->restore();
        $this->resetSelection();

        $this->dispatch('show-swalindex', [
            'title' => '¡Recuperados!',
            'text'  => count($ids) . ' ubicación(es) restaurada(s).',
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
                foreach (WarehouseLocation::onlyTrashed()->whereIn('id', $ids)->get() as $item) {
                    $this->authorize('forceDelete', $item);
                    $item->forceDelete();
                }
            });

            $this->resetSelection();

            $this->dispatch('show-swalindex', [
                'title' => '¡Limpieza Exitosa!',
                'text'  => count($ids) . ' ubicación(es) borrada(s) permanentemente.',
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
        $item = WarehouseLocation::findOrFail($id);
        $this->authorize('update', $item);
        $item->state = !$item->state;
        $item->save();
    }

    // ─── RENDER ──────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.admin.warehouse-location.warehouse-location-list', [
            'locations'     => $this->baseQuery()
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage),
            'selectedCount' => $this->selectedCount,
            'warehouses'    => \App\Models\Warehouse::where('state', true)
                ->orderBy('name')
                ->get(['id', 'code', 'name']),
        ]);
    }
}
