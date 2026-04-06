<?php

namespace App\Traits;

use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

trait WithStandardTable
{
    use WithPagination, AuthorizesRequests;
    public $showTrashed = false; // 🚀 Controla si vemos la papelera

    // Propiedades comunes que usas en JournalTypeList
    public $search = '';
    public $status = 'all';
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $selectedItems = [];
    public $selectAll = false;

    // 🚀 Recuperamos el control de columnas
    public $columns = [
        'order' => false,
    ];

    public function updatingShowTrashed()
    {
        $this->resetPage();
        $this->resetSelection();
    }


    // Resetear paginación al interactuar
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
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetSelection();
    }

    public function resetSelection()
    {
        $this->selectAll = false;
        $this->selectedItems = [];
    }


    public function updatedSelectAllBack($value)
    {
        if ($value) {
            // Obtenemos solo los IDs de la consulta filtrada actual
            $this->selectedItems = $this->baseQuery()
                ->pluck('id')
                ->mapWithKeys(fn($id) => [$id => $id])
                ->toArray();
        } else {
            $this->selectedItems = [];
        }
    }


    public function updatedSelectAll($value)
    {
        if ($value) {
            // 🚀 TIP SENIOR: Debemos asignar 'true' como valor para que el checkbox se marque
            $this->selectedItems = $this->baseQuery()
                ->pluck('id')
                ->mapWithKeys(fn($id) => [(string)$id => true]) // ID como llave, TRUE como valor
                ->toArray();
        } else {
            $this->selectedItems = [];
        }
    }


    // Propiedad computada para el botón rojo
    public function getSelectedCountProperty()
    {
        return count(array_filter($this->selectedItems));
    }
}
