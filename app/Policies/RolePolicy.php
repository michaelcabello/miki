<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

//php artisan make:policy RolePolicy -m Role
class RolePolicy
{
    use HandlesAuthorization;

    //puede hacer de todo
    public function before($user)
    {
        if ($user->hasRole('Admin')) {
            return true;
        }
    }


    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Role List');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->hasRole('Admin') || $user->hasPermissionTo('Role View');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasPermissionTo('Role Create');
    }


    public function update(User $user, Role $role): bool
    {
        return $user->hasRole('Admin') || $user->hasPermissionTo('Role Update');
    }

    public function delete(User $user, Role $role): bool
    {
        if ($role->id === 1) { // ID del rol protegido
            return false; // Bloquear eliminación
        }
        return $user->hasRole('Admin') || $user->hasPermissionTo('Role Delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Role $role): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Role $role): bool
    {
        return false;
    }
}
