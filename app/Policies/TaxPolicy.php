<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization; //agregue esto

class TaxPolicy
{

  use HandlesAuthorization;

    //puede hacer de todo
    public function before(User $user): ?bool
    {
        if ($user->hasRole('Admin')) {
            return true; // Admin: todo permitido
        }

        return null; // continúa con la policy normal
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Tax ViewAny');
    }

    public function view(User $user, Tax $tax): bool
    {
        return $user->hasPermissionTo('Tax View');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Tax Create');
    }

    public function update(User $user, Tax $tax): bool
    {
        return $user->hasPermissionTo('Tax Update');
    }

    public function delete(User $user, Tax $tax): bool
    {
        return $user->hasPermissionTo('Tax Delete');
    }
}
