<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Position;
use App\Models\Local;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Storage;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Importar el trait


use Barryvdh\DomPDF\Facade\Pdf;

//php artisan make:controller admin/UserController --model=User --resource
class UserController extends Controller
{
    use AuthorizesRequests, ValidatesRequests; // Usa el trait aquí explícitamente

    public function index()
    {
        //$this->authorize('viewAny', User::class);
        return view('admin.users.index');
    }

    public function create()
    {
        $user = new User(); //instanvciamos el modelo user pero vacia
        //$this->authorize('create', $user);
        //$roles = Role::with('permissions')->get();
        $roles = Role::all();
        //$permissions = Permission::orderBy('model_name', 'asc')->get();
        $permissions = Permission::orderBy('model_name')->get()->groupBy('model_name');
        $positions = Position::where('state', 1)->get(); //positions de la emresa
        $locales = Local::where('state', 1)->get(); //locales de la empresa
        return view('admin.users.create', compact('user', 'roles', 'permissions', 'positions', 'locales'));
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:5',
            'email' => 'required|unique:users|email|max:100',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Employee::create([
            'address' => $request->address,
            'movil' => $request->movil,
            'dni' => $request->dni,
            'gender' => $request->gender,
            'birthdate' => $request->birthdate,
            'state' => $request->has('state') ? 1 : 0,
            'user_id' => $user->id,
            'position_id' => $request->position_id,
            'local_id' => $request->local_id

        ]);

        // ✅ Convertir IDs a names antes de asignar
        if ($request->filled('roles')) {
            $roleNames = Role::whereIn('id', $request->roles)->pluck('name')->toArray();
            $user->assignRole($roleNames);
        }

        if ($request->filled('permissions')) {
            $permissionNames = Permission::whereIn('name', $request->permissions)->pluck('name')->toArray();
            $user->givePermissionTo($permissionNames);
        }

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Bien Hecho',
            'text' => 'Usuario Creado Correctamente',
        ]);

        return redirect()->route('admin.users.index');
    }


    public function show(User $user)
    {
        // Cargar relaciones necesarias
        $user->load([
            'roles',            // Roles asignados al usuario
            'permissions',      // Permisos directos asignados al usuario
            'employee.position', // Cargo del empleado
            'employee.local'    // Local asociado al empleado
        ]);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->load('roles', 'permissions', 'employee');

        $roles = Role::all();
        $permissions = Permission::orderBy('model_name')->get()->groupBy('model_name');
        $positions = Position::where('state', 1)->get();
        $locales = Local::where('state', 1)->get();

        return view('admin.users.edit', compact('user', 'roles', 'permissions', 'positions', 'locales'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'name' => 'required|min:5',
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
            'password' => 'required|confirmed|min:6',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
        ]);

        $user->employee()->update([
            'address' => $request->address,
            'movil' => $request->movil,
            'dni' => $request->dni,
            'gender' => $request->gender,
            'birthdate' => $request->birthdate,
            'state' => $request->has('state') ? 1 : 0,
            'position_id' => $request->position_id,
            'local_id' => $request->local_id,
        ]);

        $roleNames = Role::whereIn('id', $request->roles ?? [])->pluck('name')->toArray();
        $permissionNames = Permission::whereIn('name', $request->permissions ?? [])->pluck('name')->toArray();

        $user->syncRoles($roleNames);
        $user->syncPermissions($permissionNames);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Actualizado',
            'text' => 'Usuario actualizado correctamente.',
        ]);

        return redirect()->route('admin.users.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
