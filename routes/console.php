<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\AutomationService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Programación de cumpleaños: todos los días 08:00
Schedule::call(function () {
    app(AutomationService::class)->dispatchBirthdaysForDate(now());
})->dailyAt('08:00')->name('birthdays:dispatch')->withoutOverlapping();
