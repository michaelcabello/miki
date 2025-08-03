<?php

namespace App\Http\Controllers\admin;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Importar el trait

//php artisan make:controller admin/RoleController -r
class RoleController extends Controller
{
    use AuthorizesRequests; // Usa el trait aquí explícitamente

    public function index()
    {
        //$this->authorize('viewAny', Role::class);
        return view('admin.roles.index');
    }


    public function create()
    {
        //$this->authorize('create', $role = new Role);
        $role = new Role;

        //$permissions = Permission::orderBy('model_name', 'asc')->get();
        //$permissions = $permissions->groupBy('model_name');

        $permissions = Permission::orderBy('model_name')
            ->orderBy('display_name')
            ->get()
            ->groupBy('model_name');


        return view('admin.roles.create', [
            'permissions' => $permissions,
            'role' => $role
        ]);
    }

    public function store(Request $request)
    {
        //$this->authorize('create', new Role);
        $role = new Role;


        $request->validate([
            'name' => 'required|unique:roles,name',
            'display_name' => 'required',
        ]);


        $role = Role::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            //'company_id' => auth()->user()->employee->company->id, //encontramos la company actual osea la compania del usuario logueado
            //'guard_name' => auth()->user()->employee->company->id,
        ]);

        if ($request->has('permissions')) {
            $role->givePermissionTo($request->permissions);
        }
        //return redirect()->route('admin.role.index')->withFlash('El Rol fue creado correctamente');

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'TICOM',
            'text' => 'Rol Creado Correctamentexx',
        ]);


        return redirect()->route('admin.roles.index');
    }


    public function show(Role $role)
    {
        //$this->authorize('view', $role);

        // Cargar los permisos relacionados
        $permissions = $role->permissions->groupBy('model_name');

        return view('admin.roles.show', compact('role', 'permissions'));
    }

    public function edit(Role $role)
    {
        //$this->authorize('update', $role);

        $permissions = Permission::orderBy('model_name')
            ->orderBy('display_name')
            ->get()
            ->groupBy('model_name');

        return view('admin.roles.edit', [
            'role' => $role,
            'permissions' => $permissions
        ]);
    }





    public function update(Request $request, Role $role)
    {
        //$this->authorize('update', $role);
        $data = $request->validate([
            //'name'=>'required|unique:roles,name,' . $role->id,
            'display_name' => 'required',
            //'guard_name'=>'required'
        ]);

        $role->update($data);

        $role->permissions()->detach();
        if ($request->has('permissions')) {
            $role->givePermissionTo($request->permissions);
        }

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'TICOM',
            'text' => 'Rol Actualizado Correctamente',
        ]);


        //return redirect()->route('admin.roles.index');
        return redirect()->route('admin.roles.edit', $role);
    }


    public function destroy(Role $role)
    {
        //
    }
}
