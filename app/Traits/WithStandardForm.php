<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Auth\Access\AuthorizationException;

trait WithStandardForm
{
    use AuthorizesRequests;

    /**
     * Gestión de pestañas (Odoo Style)
     */
    public string $tab = 'general';

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    /**
     * 🚀 Ejecutor Universal de Guardado
     * Maneja transacciones, errores y redirección con SweetAlert.
     */

    protected function executeSave(callable $action, string $redirectRoute, string $successMessage)
    {
        DB::beginTransaction();

        try {
            $result = $action();

            if ($result === false) {
                throw new \Exception("La operación devolvió un resultado fallido.");
            }

            DB::commit();

            session()->flash('swal', [
                'icon'  => 'success',
                'title' => '¡Hecho!',
                'text'  => $successMessage,
            ]);

            return redirect()->route($redirectRoute);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // 🚀 IMPORTANTE: Si es un error de validación, cancelamos la transacción
            // pero NO atrapamos el error, lo dejamos pasar para que Livewire lo muestre en la vista.
            DB::rollBack();
            throw $e;
        } catch (AuthorizationException $e) {
            // El usuario no tiene permisos para ejecutar esta acción
            DB::rollBack();

            $this->dispatch('show-swalindex', [
                'icon'  => 'error',
                'title' => 'Acceso denegado',
                'text'  => 'No tienes permisos para realizar esta acción.',
            ]);

            return null;

        }catch (\Exception $e) {
            DB::rollBack();

            Log::error("Error en " . class_basename($this) . ": " . $e->getMessage());

            $this->dispatch('show-swalindex', [
                'icon'  => 'error',
                'title' => 'Error de sistema',
                'text'  => config('app.debug') ? $e->getMessage() : 'No se pudo procesar la solicitud.',
            ]);

            return null;
        }
    }
}
