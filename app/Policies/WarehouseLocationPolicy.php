<?php

namespace App\Policies;

use App\Models\WarehouseLocation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class WarehouseLocationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('WarehouseLocation List');
    }

    public function view(User $user, WarehouseLocation $record): bool
    {
        return $user->hasPermissionTo('WarehouseLocation View');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('WarehouseLocation Create');
    }

    public function update(User $user, WarehouseLocation $record): bool
    {
        return $user->hasPermissionTo('WarehouseLocation Update');
    }

    public function delete(User $user, WarehouseLocation $record): bool
    {
        return $user->hasPermissionTo('WarehouseLocation Delete');
    }

    public function restore(User $user, WarehouseLocation $record): bool
    {
        return $user->hasPermissionTo('WarehouseLocation Restore');
    }

    public function forceDelete(User $user, WarehouseLocation $record): bool
    {
        return $user->hasPermissionTo('WarehouseLocation ForceDelete');
    }
}
