<?php

namespace App\Policies;

use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization; //agregue esto

//php artisan make:policy SubscriptionPlanPolicy
class SubscriptionPlanPolicy
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
        return $user->hasPermissionTo('SubscriptionPlan List');
    }

    public function view(User $user, SubscriptionPlan $plan): bool
    {
        return $user->hasPermissionTo('SubscriptionPlan View');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('SubscriptionPlan Create');
    }

    public function update(User $user, SubscriptionPlan $plan): bool
    {
        return $user->hasPermissionTo('SubscriptionPlan Update');
    }

    public function delete(User $user, SubscriptionPlan $plan): bool
    {
        return $user->hasPermissionTo('SubscriptionPlan Delete');
    }
}
