<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\JournalType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization; //agregue esto

class JournalTypePolicy
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


    /* public function before($user)
    {
        if ($user->hasRole('Admin')) {
            return true;
        }
    } */


    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('JournalType List');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, JournalType $journalType): bool
    {
        return $user->hasPermissionTo('JournalType View');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('JournalType Create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JournalType $journalType): bool
    {
        return $user->hasPermissionTo('JournalType Update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JournalType $journalType): bool
    {
        return $user->hasPermissionTo('JournalType Delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, JournalType $journalType): bool
    {
        return $user->hasPermissionTo('JournalType Restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, JournalType $journalType): bool
    {
        return $user->hasPermissionTo('JournalType ForceDelete');
    }
}
