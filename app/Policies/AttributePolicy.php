<?php

namespace App\Policies;

use App\Models\Attribute;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization; //agregue esto

//php artisan make:policy AttributePolicy
class AttributePolicy
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
        return $user->hasPermissionTo('Attribute List');
    }

    /**
     * Ver un atributo específico.
     */
    public function view(User $user, Attribute $attribute): bool
    {
        return $user->hasPermissionTo('Attribute View');
    }

    /**
     * Crear un nuevo atributo.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Attribute Create');
    }

    /**
     * Actualizar un atributo existente.
     */
    public function update(User $user, Attribute $attribute): bool
    {
        return $user->hasPermissionTo('Attribute Update');
    }

    /**
     * Eliminar un atributo.
     */
    public function delete(User $user, Attribute $attribute): bool
    {
        return $user->hasPermissionTo('Attribute Delete');
    }
}
