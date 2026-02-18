<?php

namespace App\Livewire\Admin\Partner;

use App\Models\Partner;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Storage;

class PartnerList extends Component
{
    use WithPagination;

    public $search = '';
    public $status = 'all'; // all|active|inactive
    public $scope  = 'roots'; // roots|contacts|all

    public $perPage = 10;

    public $selectedPartners = [];
    public $selectAll = false;

    public $sortField = 'id';      // id|name
    public $sortDirection = 'asc'; // asc|desc

    public ?int $expandedId = null;

    protected $paginationTheme = 'tailwind';

    // Columnas opcionales para mostrar/ocultar
    public $columns = [
        'slug' => false,
        'seo'  => false,
    ];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatus() { $this->resetPage(); $this->resetSelection(); }
    public function updatingScope()  { $this->resetPage(); $this->resetSelection(); }
    public function updatingPerPage(){ $this->resetPage(); $this->resetSelection(); }

    public function toggleExpanded($id)
    {
        $this->expandedId = $this->expandedId === (int)$id ? null : (int)$id;
    }

    public function sortBy($field)
    {
        if (!in_array($field, ['id', 'name'])) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
        $this->resetSelection();
    }

    public function toggleStatus($partnerId)
    {
        try {
            $partner = Partner::findOrFail($partnerId);
            $partner->status = !$partner->status;
            $partner->save();

            $this->dispatch('show-swalindex', [
                'title' => 'Actualizado',
                'text'  => 'Estado del partner actualizado',
                'icon'  => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('show-swalindex', [
                'title' => 'Error',
                'text'  => 'No se pudo actualizar el estado: ' . $e->getMessage(),
                'icon'  => 'error'
            ]);
        }
    }

    public function getSelectedCountProperty()
    {
        return count(array_keys(array_filter($this->selectedPartners)));
    }

    private function resetSelection()
    {
        $this->selectAll = false;
        $this->selectedPartners = [];
    }

    /**
     * Seleccionar todos (respeta scope/filtros actuales)
     */
    public function updatedSelectAll($value)
    {
        if (!$value) {
            $this->selectedPartners = [];
            return;
        }

        $query = $this->baseQuery(cloneQuery: true)->select('id');

        $this->selectedPartners = $query->pluck('id')->mapWithKeys(fn ($id) => [$id => true])->toArray();
    }

    #[On('confirmDeleteSelected')]
    public function deleteSelected()
    {
        try {
            $selectedIds = array_keys(array_filter($this->selectedPartners));
            if (empty($selectedIds)) return;

            $partners = Partner::whereIn('id', $selectedIds)->get();

            foreach ($partners as $partner) {
                if ($partner->image) {
                    // Si usas local, cambia disk('s3') por disk('public')
                    Storage::disk('s3')->delete($partner->image);
                }
                $partner->delete();
            }

            $this->resetSelection();

            $this->dispatch('partnersDeleted', [
                'title' => 'Eliminado',
                'text'  => count($selectedIds) . ' partner(s) eliminado(s) correctamente',
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

    #[On('deleteSingle')]
    public function deleteSingle($id, $name)
    {
        try {
            $partner = Partner::findOrFail($id);

            if ($partner->image) {
                Storage::disk('s3')->delete($partner->image);
            }

            $partner->delete();

            unset($this->selectedPartners[$id]);

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

    /**
     * Query base con eager loading (N+1 prevenido con ->with())
     */
    private function baseQuery(bool $cloneQuery = false)
    {
        $query = Partner::query()
            ->with([
                'children:id,parent_id,name,email,phone,whatsapp,mobile,status,is_customer,is_supplier,image',
                'companyType:id,name',
                'documentType:id,name',
                'pricelist:id,name',
                'currency:id,name',
                'department:id,name',
                'province:id,name',
                'district:id,name',
            ]);

        if ($this->scope === 'roots') {
            $query->whereNull('parent_id');
        } elseif ($this->scope === 'contacts') {
            $query->whereNotNull('parent_id');
        }

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        if ($this->status === 'active') {
            $query->where('status', true);
        } elseif ($this->status === 'inactive') {
            $query->where('status', false);
        }

        return $query;
    }

    public function render()
    {
        $partners = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.partner.partner-list', compact('partners'));
    }
}
