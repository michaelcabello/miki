<?php

namespace App\Livewire\Admin;

use Livewire\Component;

use Livewire\WithPagination;
use Livewire\Attributes\On;
use Spatie\Permission\Models\Permission;

//php artisan make:livewire Admin\PermissionList
class PermissionList extends Component
{

    use WithPagination;

    public $search = '';
    public $status = 'all'; // all, active, inactive
    public $perPage = 10;
    public $selectedUsers = [];
    public $selectAll = false;
    public $sortField = 'id';
    public $sortDirection = 'asc';

    public $currentPageIds = [];
    protected $paginationTheme = 'tailwind';

    //para el modal con tailwind sin flux
    public $showEditModal = false;
    public $permissionId;
    //public $display_name;

    public $permission;
    public $display_name;


    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }


    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }


    public function render()
    {
        $permissions = Permission::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.permission-list', compact('permissions'));
    }

    /*  public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        $this->permissionId = $permission->id;
        $this->display_name = $permission->display_name;
        $this->showEditModal = true;
    } */


    public function edit($id)
    {
        $this->permission = Permission::findOrFail($id);
        $this->display_name = $this->permission->display_name; // Solo el valor editable
        // Limpia errores de validación anteriores
        //$this->resetErrorBag();
        //$this->resetValidation(); // Limpia errores + mensajes personalizados
        //dd($this->permission);
    }

    public function editt($id)
    {
        $permission = Permission::findOrFail($id);
        $this->permissionId = $permission->id;
        $this->display_name = $permission->display_name;
        $this->showEditModal = true;
    }



    public function updateDisplayName()
    {
        $this->validate([
            'display_name' => 'required|string|max:255',
        ]);

        $permission = Permission::findOrFail($this->permissionId);
        $permission->display_name = $this->display_name;
        $permission->save();

        $this->showEditModal = false;
        //$this->dispatch('permission-updateddos');
        //$this->dispatch('show-swalindex', 'El permiso se actualizó correctamente.');
        //title, text, icon
        //$this->dispatch('show-swalindex', 'TICOM', 'El permiso se actualizó.', 'success');

        /*  $this->dispatch('show-swalindex', [
            'title' => '¡Actualizado!',
            'text' => 'El permiso se actualizó correctamente.',
            'icon' => 'success',
        ]); */

         $this->dispatch('show-swalindex', title: 'TICOM', text: 'El permiso se actualizó', icon: 'success');

        //$this->dispatch('show-toast', icon: 'success', title: 'Permiso actualizado correctamente');
    }



    public function update()
    {
        $this->validate([
            'display_name' => 'required|string|max:255',
        ]);

        if ($this->permission) {
            $this->permission->update([
                'display_name' => $this->display_name,
            ]);

            // Opcional: resetear modal, notificación, cerrar
            $this->reset(['display_name', 'permission']);
            $this->resetValidation();
            //$this->dispatch('permission-updated');

            $this->dispatch('close-modal', name: 'edit-profile');
            $this->dispatch('notify', type: 'success', message: 'xPermiso actualizado.');
        }
    }

     #[On('permission-updateddos')]
    public function showSuccessMessage()
    {
        $this->dispatch('show-swalindex', [
            'title' => '¡Actualizado!',
            'text' => 'El permiso fue actualizado correctamente.',
            'icon' => 'success',
        ]);
    }

   /*  #[On('permission-updateddos')]
    public function handlePermissionUpdated()
    {
        $this->dispatch('show-toast', icon: 'success', title: 'Permiso actualizado correctamente');
    } */

    public function clearErrors()
    {
        $this->resetErrorBag();
    }

    public function clearValidationErrors()
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->reset(['display_name']); // Si quieres también resetear el campo
    }

    public function closeModal()
    {
        $this->reset(['display_name', 'permission', 'showEditModal']);
        $this->resetValidation(); // Limpiar errores de validación
    }
}
