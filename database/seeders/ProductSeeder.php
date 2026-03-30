<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\ProductTemplate;
use App\Models\ProductVariant;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Planes de Suscripción (Odoo: Recurring Intervals)
        $planMensual = SubscriptionPlan::create([
            'name' => 'Mensual Estándar',
            'interval_count' => 1,
            'interval_unit' => 'month',
            'active' => true
        ]);

        $planAnual = SubscriptionPlan::create([
            'name' => 'Anual Premium',
            'interval_count' => 1,
            'interval_unit' => 'year',
            'active' => true
        ]);

        // 2. Crear Atributos y Valores (Odoo: Attributes & Values)
        $colorAttr = Attribute::create(['name' => 'Colorr', 'state' => true]);
        $colores = collect(['Rojoo', 'Azull', 'Verdee'])->map(fn($c) => AttributeValue::create(['attribute_id' => $colorAttr->id, 'name' => $c]));

        $tallaAttr = Attribute::create(['name' => 'Tallaa', 'state' => true]);
        $tallas = collect(['SS', 'MM', 'LL'])->map(fn($t) => AttributeValue::create(['attribute_id' => $tallaAttr->id, 'name' => $t]));

        // --- PROCESO DE CREACIÓN (50 PRODUCTOS TOTAL) ---

        // A. 20 PRODUCTOS SIN VARIANTES
        for ($i = 1; $i <= 20; $i++) {
            $name = "Producto Simple $i";
            $template = ProductTemplate::create([
                'name' => $name,
                'slug' => Str::slug($name) . "-" . uniqid(),
                'type' => 'goods',
                'sale_ok' => true,
                'purchase_ok' => true,
                'active' => true,
                'uom_id' => 1, // Asumiendo ID 1 para 'Unidades'
                'uom_po_id' => 1,
            ]);

            // Crear la variante única (Odoo Style)
            ProductVariant::create([
                'product_template_id' => $template->id,
                'sku' => "SKU-SIMPLE-$i",
                'price_sale' => rand(10, 100),
                'price_purchase' => rand(5, 50),
                'active' => true,
                'is_default' => true,
            ]);
        }

        // B. 30 PRODUCTOS CON VARIANTES
        for ($i = 1; $i <= 30; $i++) {
            $name = "Producto Configurable $i";
            $template = ProductTemplate::create([
                'name' => $name,
                'slug' => Str::slug($name) . "-" . uniqid(),
                'type' => 'goods',
                'sale_ok' => true,
                'purchase_ok' => true,
                'active' => true,
                'uom_id' => 1,
            ]);

            // Generar combinaciones (Color x Talla)
            foreach ($colores as $color) {
                foreach ($tallas as $talla) {
                    $variantName = "{$name} ({$color->name}, {$talla->name})";

                    // Odoo usa una llave de combinación para identificar la variante única
                    // Formato: "attr_id:val_id|attr_id:val_id"
                    $combinationKey = "{$colorAttr->id}:{$color->id}|{$tallaAttr->id}:{$talla->id}";

                    $variant = ProductVariant::create([
                        'product_template_id' => $template->id,
                        'sku' => "SKU-" . Str::upper(Str::random(8)),
                        'price_sale' => rand(150, 500),
                        'price_purchase' => rand(100, 140),
                        'active' => true,
                        'is_default' => ($color->name == 'Rojo' && $talla->name == 'M'),
                        'combination_key' => $combinationKey,
                        'variant_name' => $variantName
                    ]);

                    // Registrar en la tabla pivote de valores de atributo
                    DB::table('attribute_value_product_variant')->insert([
                        ['attribute_value_id' => $color->id, 'product_variant_id' => $variant->id],
                        ['attribute_value_id' => $talla->id, 'product_variant_id' => $variant->id],
                    ]);
                }
            }
        }

        // C. ACTUALIZAR 10 PRODUCTOS EXISTENTES PARA QUE SEAN SUSCRIPCIONES
        // Tomamos los primeros 10 templates y los convertimos en Servicios Recurrentes
        $templatesForSub = ProductTemplate::limit(10)->get();
        foreach ($templatesForSub as $index => $subTemplate) {
            $subTemplate->update([
                'is_recurring' => true,
                'type' => 'service',
                'subscription_plan_id' => ($index % 2 == 0) ? $planMensual->id : $planAnual->id,
                'recurring_price' => rand(50, 200)
            ]);
        }
    }
}
