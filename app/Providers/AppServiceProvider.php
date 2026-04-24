<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\ContactObserver;
use App\Models\Contact;
use Illuminate\Support\Facades\Gate;
use App\Models\Warehouse;
use App\Observers\WarehouseObserver;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Contact::observe(ContactObserver::class);
        Warehouse::observe(WarehouseObserver::class);

        // 🚀 Este es el "Poder Absoluto" del Admin para todo el ERP
        // Se ejecuta ANTES de cualquier otra validación (Gates o Policies)
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Admin') ? true : null;
        });
    }
}
