<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Journal;
use App\Models\User;

class JournalPolicy
{

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
        return $user->hasPermissionTo('Journal ViewAny');
    }


     public function view(User $user, Journal $journal): bool
    {
        return $user->hasPermissionTo('Journal View');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Journal Create');
    }

     public function update(User $user, Journal $journal): bool
    {
        return $user->hasPermissionTo('Journal Update');
    }

    public function delete(User $user, Journal $journal): bool
    {
        return $user->hasPermissionTo('Journal Delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Journal $journal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Journal $journal): bool
    {
        return false;
    }
}
