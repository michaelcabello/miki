<?php

namespace App\Policies;

use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class WarehousePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Warehouse List');
    }

    public function view(User $user, Warehouse $warehouse): bool
    {
        return $user->hasPermissionTo('Warehouse View');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Warehouse Create');
    }

    public function update(User $user, Warehouse $warehouse): bool
    {
        return $user->hasPermissionTo('Warehouse Update');
    }

    public function delete(User $user, Warehouse $warehouse): bool
    {
        return $user->hasPermissionTo('Warehouse Delete');
    }

    public function restore(User $user, Warehouse $warehouse): bool
    {
        return $user->hasPermissionTo('Warehouse Restore');
    }

    public function forceDelete(User $user, Warehouse $warehouse): bool
    {
        return $user->hasPermissionTo('Warehouse ForceDelete');
    }
}
