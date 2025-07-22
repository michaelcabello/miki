<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization; //agregue esto
//Si decides no usar la clase HandlesAuthorization,
//las políticas seguirán funcionando, pero perderás la facilidad
//de usar métodos como allow(), deny(), y before().

//php artisan make:policy UserPolicy -m User
class UserPolicy
{
    //agregue esto, chat gpt recomienda ponerlo
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
        return $user->hasPermissionTo('User List');
    }


    //solo puede ver un modelo, solo un registro valido para show
    public function view(User $authUser, User $user): bool
    {
        //return false;
        return $authUser->id === $user->id
            || $user->hasPermissionTo('User View');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('User Create');
    }


    public function update(User $user, User $model): bool
    {
        return $user->hasPermissionTo('User Update');
    }


    public function delete(User $user, User $model): bool
    {
        return $user->hasPermissionTo('User Delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
