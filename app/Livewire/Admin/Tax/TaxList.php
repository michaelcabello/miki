<?php

namespace App\Livewire\Admin\Tax;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use App\Models\Tax;
use App\Imports\TaxesImport;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

#[Title('Impuestos')]
class TaxList extends Component
{
    use WithPagination, WithFileUploads, AuthorizesRequests;

    public $search         = '';
    public $status         = 'all';
    public $typeFilter     = 'all';
    public $perPage        = 10;
    public $selectedItems  = [];
    public $selectAll      = false;
    public $sortField      = 'sequence';
    public $sortDirection  = 'asc';
    public string $title   = 'Impuestos';
    public $importFile     = null;

    protected $paginationTheme = 'tailwind';

    public $columns = [
        'sequence'    => false,
        'description' => false,
        'tax_scope'   => false,
    ];

    public function mount(): void
    {
        $this->authorize('viewAny', Tax::class);
    }

    public function updatingSearch()    { $this->resetPage(); $this->resetSelection(); }
    public function updatingStatus()    { $this->resetPage(); $this->resetSelection(); }
    public function updatingTypeFilter(){ $this->resetPage(); $this->resetSelection(); }
    public function updatingPerPage()   { $this->resetPage(); $this->resetSelection(); }

    /**
     * Hook de Livewire — se dispara en cuanto el archivo termina de subirse.
     */
    public function updatedImportFile(): void
    {
        if (!$this->importFile) return;
        $this->importTaxes();
    }

    /**
     * Importa el archivo Excel/CSV.
     *
     * Sin DB::beginTransaction() manual: Maatwebsite/Excel gestiona
     * sus propias transacciones internamente por chunk. Envolver el import
     * en una transacción externa puede interferir con ese mecanismo y
     * provocar que ningún registro se persista aunque no haya errores.
     *
     * Fix de extensión: Livewire guarda el archivo temporal SIN extensión.
     * storeAs() con la extensión original permite que PhpSpreadsheet
     * detecte el formato correcto (xlsx/csv/xls) y lea las filas.
     */
    public function importTaxes(): mixed
    {
        // 1. Verifica permiso
        if (!auth()->user()->hasPermissionTo('Tax ImportExcel')) {
            $this->dispatch('show-swalindex', [
                'title' => 'Sin permiso',
                'text'  => 'No tienes permiso para importar impuestos.',
                'icon'  => 'error',
            ]);
            $this->importFile = null;
            return null;
        }

        // 2. Valida el archivo
        $this->validate(
            ['importFile' => 'required|file|mimes:xlsx,xls,csv|max:2048'],
            [
                'importFile.required' => 'Selecciona un archivo para importar.',
                'importFile.mimes'    => 'Solo se permiten archivos .xlsx, .xls o .csv.',
                'importFile.max'      => 'El archivo no debe superar 2 MB.',
            ]
        );

        // 3. Guarda con extensión original para que PhpSpreadsheet detecte el formato
        $extension  = $this->importFile->getClientOriginalExtension();
        $storedPath = $this->importFile->storeAs(
            'imports/taxes',
            'import_' . now()->format('YmdHis') . '_' . auth()->id() . '.' . $extension,
            'local'
        );

        if (!$storedPath) {
            $this->dispatch('show-swalindex', [
                'title' => 'Error',
                'text'  => 'No se pudo guardar el archivo temporalmente.',
                'icon'  => 'error',
            ]);
            $this->importFile = null;
            return null;
        }

        try {
            // 4. Importa directamente — sin transacción manual externa
            Excel::import(new TaxesImport, $storedPath, 'local');

        } catch (\Throwable $e) {
            Storage::disk('local')->delete($storedPath);
            $this->importFile = null;

            Log::error('Error al importar Taxes', [
                'error'   => $e->getMessage(),
                'usuario' => auth()->id(),
            ]);

            $this->dispatch('show-swalindex', [
                'title' => 'Error al importar',
                'text'  => 'Detalle: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);

            return null;
        }

        // 5. Limpia temporal y redirige
        Storage::disk('local')->delete($storedPath);
        $this->importFile = null;

        session()->flash('swal', [
            'icon'  => 'success',
            'title' => 'Importado',
            'text'  => 'Impuestos importados correctamente.',
        ]);

        return redirect()->route('admin.taxes.index');
    }

    public function sortBy($field): void
    {
        $allowed = ['id', 'name', 'amount', 'amount_type', 'type_tax_use', 'sequence', 'active'];
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

    private function baseQuery()
    {
        $query = Tax::query();

        if ($this->search) {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        if ($this->status === 'active')        $query->where('active', true);
        elseif ($this->status === 'inactive')  $query->where('active', false);

        if ($this->typeFilter !== 'all') {
            $query->where('type_tax_use', $this->typeFilter);
        }

        return $query;
    }

    public function updatedSelectAll($value): void
    {
        if (!$value) { $this->selectedItems = []; return; }

        $ids = $this->baseQuery()->select('id')->pluck('id');
        $this->selectedItems = $ids->mapWithKeys(fn($id) => [$id => true])->toArray();
    }

    public function toggleActive(int $id): void
    {
        try {
            $tax = Tax::findOrFail($id);
            $this->authorize('update', $tax);
            $tax->active = !$tax->active;
            $tax->save();

            $this->dispatch('show-swalindex', [
                'title' => 'Actualizado',
                'text'  => 'Estado del impuesto actualizado',
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

    #[On('confirmDeleteSelected')]
    public function deleteSelected(): void
    {
        try {
            $ids = array_keys(array_filter($this->selectedItems));
            if (empty($ids)) return;

            $taxes = Tax::whereIn('id', $ids)->get();
            foreach ($taxes as $tax) { $this->authorize('delete', $tax); }

            $inUse = Tax::whereIn('id', $ids)
                ->where(function ($q) {
                    $q->whereHas('productTemplateSaleTaxes')
                      ->orWhereHas('productTemplatePurchaseTaxes');
                })->exists();

            if ($inUse) {
                $this->dispatch('show-swalindex', [
                    'title' => 'No se puede eliminar',
                    'text'  => 'Uno o más impuestos están asignados a productos. Desasígnalos primero.',
                    'icon'  => 'warning',
                ]);
                return;
            }

            Tax::whereIn('id', $ids)->delete();
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

    #[On('deleteSingle')]
    public function deleteSingle(int $id, string $name): void
    {
        try {
            $tax = Tax::findOrFail($id);
            $this->authorize('delete', $tax);

            if ($tax->productTemplateSaleTaxes()->exists() || $tax->productTemplatePurchaseTaxes()->exists()) {
                $this->dispatch('show-swalindex', [
                    'title' => 'No se puede eliminar',
                    'text'  => 'El impuesto "' . $name . '" está asignado a uno o más productos.',
                    'icon'  => 'warning',
                ]);
                return;
            }

            $tax->delete();
            unset($this->selectedItems[$id]);

            $this->dispatch('itemDeleted', [
                'title' => 'Eliminado',
                'text'  => 'El impuesto "' . $name . '" fue eliminado correctamente',
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
        $taxes = $this->baseQuery()
            ->with('account:id,name')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.tax.tax-list', compact('taxes'));
    }
}
