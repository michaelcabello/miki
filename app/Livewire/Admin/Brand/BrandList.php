<?php

namespace App\Livewire\Admin\Brand;

use Livewire\Component;
use App\Models\Brand;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Storage;

class BrandList extends Component
{

    use WithPagination;

    public $search = '';
    public $status = 'all'; // all, active, inactive
    public $perPage = 5;
    public $selectedBrands = [];
    public $selectAll = false;
    public $sortField = 'order';
    public $sortDirection = 'asc';

    protected $paginationTheme = 'tailwind';

    // Columnas opcionales para mostrar/ocultar
    public $columns = [
        'slug' => false,
        'seo' => false,
    ];


    /**
     * Actualizar búsqueda - reinicia paginación
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Actualizar filtro de estado - reinicia paginación
     */
    public function updatingStatus()
    {
        $this->resetPage();
    }

    /**
     * Seleccionar todos los registros
     */
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedBrands = Brand::pluck('id')->mapWithKeys(function ($id) {
                return [$id => true];
            })->toArray();
        } else {
            $this->selectedBrands = [];
        }
    }

    /**
     * Resetear selección
     */
    private function resetSelection()
    {
        $this->selectAll = false;
        $this->selectedBrands = [];
    }

    /**
     * Ordenar por columna
     */
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    /**
     * Alternar estado activo/inactivo
     */
    public function toggleStatus($brandId)
    {
        try {
            $brand = Brand::findOrFail($brandId);
            $brand->state = !$brand->state;
            $brand->save();

            $this->dispatch('show-swalindex', [
                'title' => 'Actualizado',
                'text' => 'Estado de la marca actualizado',
                'icon' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('show-swalindex', [
                'title' => 'Error',
                'text' => 'No se pudo actualizar el estado',
                'icon' => 'error'
            ]);
        }
    }

    /**
     * Propiedad computada para contar seleccionados
     */
    public function getSelectedCountProperty()
    {
        return count(array_keys(array_filter($this->selectedBrands)));
    }

    /**
     * Eliminar marcas seleccionadas
     */
    #[On('confirmDeleteSelected')]
    public function deleteSelected()
    {
        try {
            // Obtener IDs seleccionados
            $selectedIds = array_keys(array_filter($this->selectedBrands));

            if (!empty($selectedIds)) {
                // Eliminar imágenes de S3 y marcas
                $brands = Brand::whereIn('id', $selectedIds)->get();

                foreach ($brands as $brand) {
                    if ($brand->image) {
                        Storage::disk('s3')->delete($brand->image);
                    }
                    $brand->delete();
                }

                $this->resetSelection();

                $this->dispatch('brandsDeleted', [
                    'title' => 'Eliminado',
                    'text' => count($selectedIds) . ' marca(s) eliminada(s) correctamente',
                    'icon' => 'success'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('show-swalindex', [
                'title' => 'Error',
                'text' => 'Error al eliminar las marcas: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    /**
     * Eliminar una marca individual
     */
    #[On('deleteSingle')]
    public function deleteSingle($id, $name)
    {
        try {
            $brand = Brand::findOrFail($id);

            // Eliminar imagen de S3
            if ($brand->image) {
                Storage::disk('s3')->delete($brand->image);
            }

            $brand->delete();

            unset($this->selectedBrands[$id]);

            $this->dispatch('itemDeleted', [
                'title' => 'Eliminado',
                'text' => 'La marca "' . $name . '" fue eliminada correctamente',
                'icon' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('show-swalindex', [
                'title' => 'Error',
                'text' => 'Error al eliminar la marca: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }


/**
     * Renderizar componente
     */
    public function render()
    {
        // Construir query con eager loading para prevenir N+1
        $query = Brand::query();

        // Aplicar búsqueda
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('slug', 'like', "%{$this->search}%")
                  ->orWhere('title', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        // Filtrar por estado
        if ($this->status === 'active') {
            $query->where('state', true);
        } elseif ($this->status === 'inactive') {
            $query->where('state', false);
        }

        // Aplicar ordenamiento y paginación
        $brands = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.brand.brand-list', compact('brands'));
    }



}
