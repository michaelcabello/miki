<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Mail\MarketingMailable;
use App\Models\Contact;
use App\Models\Marketing;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class SendMarketingEmailJob implements ShouldQueue
{

     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Contact $contact, public Marketing $marketing) {}

    public function handle(): void
    {
        $contact = $this->contact->fresh(['marketings']);
        $marketing = $this->marketing->fresh();

        if (!$contact || !$marketing) return;
        if (!$contact->send) return;

        Mail::to($contact->email)->send(new MarketingMailable($contact, $marketing));

        // contador global y pivot.number atómicos
        DB::transaction(function () use ($contact, $marketing) {
            $contact->increment('contador');
            $exists = $contact->marketings()->where('marketing_id', $marketing->id)->exists();
            if ($exists) {
                $contact->marketings()->updateExistingPivot($marketing->id, [
                    'number' => DB::raw('number + 1'),
                    'updated_at' => now(),
                ]);
            } else {
                $contact->marketings()->attach($marketing->id, ['number' => 1]);
            }
        });
    }
}
