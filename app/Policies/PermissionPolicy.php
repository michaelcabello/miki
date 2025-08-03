<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

//php artisan make:policy PermissionPolicy -m Permission
class PermissionPolicy
{
    use HandlesAuthorization;

    //puede hacer de todo
    public function before($user)
    {
        if ($user->hasRole('Admin')) {
            return true;
        }
    }

    //puede ver varios modelos, osea la lista de usuarios
    public function viewAny(User $user): bool
    {
         return $user->hasPermissionTo('Permission List');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Permission $permission): bool
    {
        return $user->hasRole('Admin')||$user->hasPermissionTo('Permission View');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Permission $permission): bool
    {
        return $user->hasRole('Admin')||$user->hasPermissionTo('Permission Update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Permission $permission): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Permission $permission): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Permission $permission): bool
    {
        return false;
    }
}
