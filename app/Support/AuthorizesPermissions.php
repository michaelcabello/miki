<?php

namespace App\Support;

use Illuminate\Auth\Access\AuthorizationException;

trait AuthorizesPermissions
{
    /**
     * Autoriza por permiso Spatie.
     * - Si no tiene permiso lanza 403.
     */
    protected function authorizePermission(string $permission): void
    {
        if (!auth()->check() || !auth()->user()->can($permission)) {
            throw new AuthorizationException("No autorizado: {$permission}");
        }
    }

    /**
     * Autoriza si tiene AL MENOS UNO de los permisos.
     */
    protected function authorizeAnyPermission(array $permissions): void
    {
        if (!auth()->check() || !auth()->user()->canAny($permissions)) {
            throw new AuthorizationException('No autorizado.');
        }
    }
}
