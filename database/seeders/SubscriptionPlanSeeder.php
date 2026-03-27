<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Mensual estándar',
                'interval_count' => 1,
                'interval_unit' => 'month',
                'active' => true,
            ],
            [
                'name' => 'Trimestral (Ahorro 5%)',
                'interval_count' => 3,
                'interval_unit' => 'month',
                'active' => true,
            ],
            [
                'name' => 'Semestral (Ahorro 10%)',
                'interval_count' => 6,
                'interval_unit' => 'month',
                'active' => true,
            ],
            [
                'name' => 'Anual Premium',
                'interval_count' => 1,
                'interval_unit' => 'year',
                'active' => true,
            ],
            [
                'name' => 'Mantenimiento Semanal',
                'interval_count' => 1,
                'interval_unit' => 'week',
                'active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }
    }
}
