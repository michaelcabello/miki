<?php
namespace App\Services;

use App\Models\Contact;
use App\Models\CategoryMarketing;
use App\Models\Marketing;
use App\Jobs\SendMarketingEmailJob;
use Illuminate\Support\Carbon;

class AutomationService
{
    /** Encola todas las piezas relativas al registro del contacto */
    public function scheduleRegistrationFlows(Contact $contact): void
    {
        if (!$contact->send) return;

        // Bienvenida (categoría: bienvenida)
        if ($m = $this->activeByCategory('bienvenida')) {
            SendMarketingEmailJob::dispatch($contact, $m);
        }

        // +1h (promoción diseño web)
        if ($m = $this->activeByCategory('promo-1h')) {
            SendMarketingEmailJob::dispatch($contact, $m)->delay(now()->addHour());
        }

        // +7d, +14d, +3m, +6m, +10m (todas categoría "promo-recurrencia" o separadas)
        $recurrences = [
            'promo-7d'  => now()->addDays(7),
            'promo-14d' => now()->addDays(14),
            'promo-3m'  => now()->addMonths(3),
            'promo-6m'  => now()->addMonths(6),
            'promo-10m' => now()->addMonths(10),
        ];
        foreach ($recurrences as $cat => $when) {
            if ($m = $this->activeByCategory($cat)) {
                SendMarketingEmailJob::dispatch($contact, $m)->delay($when);
            }
        }
    }

    /** Enviar correo de cumpleaños para contactos cuya fecha coincida hoy */
    public function dispatchBirthdaysForDate(Carbon $date): int
    {
        $m = $this->activeByCategory('cumpleanios'); // define esta categoría
        if (!$m) return 0;

        $contacts = Contact::query()
            ->where('send', true)
            ->whereMonth('birthdate', $date->month)
            ->whereDay('birthdate', $date->day)
            ->get();

        foreach ($contacts as $c) {
            SendMarketingEmailJob::dispatch($c, $m);
        }
        return $contacts->count();
    }

    protected function activeByCategory(string $slugOrName): ?Marketing
    {
        $cat = CategoryMarketing::query()
            ->where('name', $slugOrName)
            ->first();

        return $cat ? $cat->marketings()->where('state', true)->first() : null;
    }
}
