<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Currency;
use App\Models\CurrencyRate;
use Carbon\Carbon;

class SyncExchangeRates extends Command
{
    protected $signature = 'erp:sync-rates';
    protected $description = 'Sincroniza el tipo de cambio desde Decolecta';

    public function handle()
    {
        $this->info('Iniciando sincronización con Decolecta...');

        // 🚀 SOLUCIÓN SQL: Buscamos SOLO por 'name' porque 'code' no existe
        $usd = Currency::where('name', 'USD')->first();

        if (!$usd) {
            $this->error('❌ No se encontró la moneda "USD" en la tabla currencies.');
            return Command::FAILURE;
        }

        $token = config('services.consulta_ruc.token');
        $baseUrl = "https://api.decolecta.com/v1/tipo-cambio/sunat";

        // 🚀 LÓGICA DE REINTENTO: Intentamos hoy, si falla (404), intentamos ayer.
        $fechasAProbar = [
            'hoy' => Carbon::now()->format('Y-m-d'),
            'ayer' => Carbon::now()->subDay()->format('Y-m-d'),
        ];

        foreach ($fechasAProbar as $label => $fecha) {
            $this->line("Probando fecha ({$label}): {$fecha}...");

            $response = Http::withToken($token)
                ->withHeaders(['Accept' => 'application/json'])
                ->get($baseUrl, ['date' => $fecha]);

            if ($response->successful()) {
                $data = $response->json();

                // 🚀 MAPEO SEGÚN DECOLECTA: buy_price y sell_price
                $buyRate = floatval($data['buy_price'] ?? 0);
                $sellRate = floatval($data['sell_price'] ?? 0);
                $resDate = $data['date'] ?? $fecha;

                if ($buyRate > 0) {
                    CurrencyRate::updateOrCreate(
                        ['currency_id' => $usd->id, 'date' => $resDate],
                        ['buy_rate' => $buyRate, 'sell_rate' => $sellRate, 'official_rate' => $sellRate]
                    );

                    $this->info("✅ ¡Éxito! Sincronizado para el día {$resDate}: C $buyRate | V $sellRate");
                    return Command::SUCCESS;
                }
            } else {
                $this->error("❌ Falló {$label} (Código: " . $response->status() . ")");
            }
        }

        $this->error('No se pudo obtener el tipo de cambio de hoy ni de ayer. Verifica tu token en Decolecta.');
        return Command::FAILURE;
    }
}
