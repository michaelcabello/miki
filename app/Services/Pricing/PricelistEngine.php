<?php

namespace App\Services\Pricing;

use App\Models\Pricelist;
use App\Models\PricelistItem;
use App\Models\ProductVariant;
use Carbon\Carbon;

class PricelistEngine
{
    /**
     * Calcula el precio final de una variante según una lista de precios (Odoo-like).
     */
    public function priceForVariant(
        Pricelist $pricelist,
        ProductVariant $variant,
        float $qty = 1,
        ?Carbon $date = null
    ): ?float {
        $date ??= now();

        // 1) Buscar la mejor regla aplicable
        $rule = $this->bestRule($pricelist, $variant, $qty, $date);

        // 2) Si no hay regla: retornar precio base (price_sale)
        $basePrice = $this->getBasePrice($variant, 'price_sale');
        if (!$rule) {
            return $basePrice;
        }

        // 3) Calcular según compute_method
        return $this->computeByRule($pricelist, $variant, $qty, $date, $rule);
    }

    /**
     * Devuelve la regla ganadora (prioridad + min_qty + sequence) estilo Odoo.
     */


    public function bestRule(
        Pricelist $pricelist,
        ProductVariant $variant,
        float $qty,
        Carbon $date
    ): ?PricelistItem {

        $templateId = $variant->product_template_id;
        $category = $variant->productTemplate?->category;

        // Obtener árbol completo de categorías (desde la actual hasta la raíz)
        $categoryIds = [];
        while ($category) {
            $categoryIds[] = $category->id;
            $category = $category->parent;
        }

        $query = PricelistItem::query()
            ->where('pricelist_id', $pricelist->id)
            ->where('active', true)
            ->where('min_qty', '<=', $qty)
            ->where(function ($q) use ($date) {
                $q->whereNull('date_start')->orWhere('date_start', '<=', $date);
            })
            ->where(function ($q) use ($date) {
                $q->whereNull('date_end')->orWhere('date_end', '>=', $date);
            })
            ->where(function ($q) use ($variant, $templateId, $categoryIds) {

                $q->where('applied_on', 'all')

                    ->orWhere(function ($qq) use ($variant) {
                        $qq->where('applied_on', 'variant')
                            ->where('product_variant_id', $variant->id);
                    })

                    ->orWhere(function ($qq) use ($templateId) {
                        $qq->where('applied_on', 'template')
                            ->where('product_template_id', $templateId);
                    })

                    ->orWhere(function ($qq) use ($categoryIds) {
                        $qq->where('applied_on', 'category')
                            ->whereIn('category_id', $categoryIds);
                    });
            });

        $items = $query->get();

        return $items->sortByDesc(function ($it) use ($categoryIds) {

            // PRIORIDAD ODOO
            if ($it->applied_on === 'variant') return 1000;
            if ($it->applied_on === 'template') return 900;

            if ($it->applied_on === 'category') {
                // Mientras más profunda la categoría, mayor prioridad
                $depthScore = array_search($it->category_id, $categoryIds);
                return 800 - $depthScore;
            }

            return 100; // all
        })
            ->sortByDesc('min_qty')
            ->sortBy('sequence')
            ->first();
    }



    private function computeByRule(
        Pricelist $pricelist,
        ProductVariant $variant,
        float $qty,
        Carbon $date,
        PricelistItem $rule
    ): ?float {
        $basePrice = $this->resolveRuleBasePrice($pricelist, $variant, $qty, $date, $rule);

        // Si basePrice es null, no podemos calcular
        if ($basePrice === null && $rule->compute_method !== 'fixed') {
            return null;
        }

        $price = null;

        if ($rule->compute_method === 'fixed') {
            $price = $rule->fixed_price !== null ? (float)$rule->fixed_price : null;
        }

        if ($rule->compute_method === 'discount') {
            $discount = (float)($rule->percent_discount ?? 0);
            $price = $basePrice * (1 - ($discount / 100));
        }

        if ($rule->compute_method === 'formula') {
            $mult = $rule->price_multiplier !== null ? (float)$rule->price_multiplier : 1.0;
            $surcharge = (float)($rule->price_surcharge ?? 0);

            $price = ($basePrice * $mult) + $surcharge;

            // Redondeo (ej: 0.05, 1.00)
            if ($rule->rounding !== null && (float)$rule->rounding > 0) {
                $round = (float)$rule->rounding;
                $price = round($price / $round) * $round;
            }

            // Márgenes (estilo Odoo: margen absoluto sobre base)
            if ($rule->min_margin !== null) {
                $price = max($price, $basePrice + (float)$rule->min_margin);
            }
            if ($rule->max_margin !== null) {
                $price = min($price, $basePrice + (float)$rule->max_margin);
            }
        }

        // Nunca negativo
        if ($price !== null) {
            $price = max(0, $price);
        }

        return $price;
    }

    /**
     * Base price para fixed/discount/formula:
     * - price_sale: variant.price_sale (fallback template si tienes)
     * - cost: variant.price_purchase
     * - other_pricelist: recursivo
     */
    private function resolveRuleBasePrice(
        Pricelist $pricelist,
        ProductVariant $variant,
        float $qty,
        Carbon $date,
        PricelistItem $rule
    ): ?float {
        if ($rule->compute_method !== 'formula') {
            // para discount usamos price_sale como base por defecto
            return $this->getBasePrice($variant, 'price_sale');
        }

        return match ($rule->base) {
            'cost' => $this->getBasePrice($variant, 'cost'),
            'other_pricelist' => $this->priceFromOtherPricelist($rule, $variant, $qty, $date),
            default => $this->getBasePrice($variant, 'price_sale'),
        };
    }

    private function priceFromOtherPricelist(
        PricelistItem $rule,
        ProductVariant $variant,
        float $qty,
        Carbon $date
    ): ?float {
        if (!$rule->base_pricelist_id) return null;

        $basePL = Pricelist::find($rule->base_pricelist_id);
        if (!$basePL) return null;

        // Evita loops simples (si alguien pone la misma lista)
        if ((int)$basePL->id === (int)$rule->pricelist_id) {
            return null;
        }

        return $this->priceForVariant($basePL, $variant, $qty, $date);
    }

    /**
     * Obtiene precio base desde tu migración de variantes.
     */
    private function getBasePrice(ProductVariant $variant, string $type): ?float
    {
        // Si tu template también tiene price_sale, aquí puedes fallback
        $template = $variant->productTemplate;

        return match ($type) {
            'price_sale' => $variant->price_sale ?? ($template->price_sale ?? null),
            'cost' => $variant->price_purchase ?? ($template->price_purchase ?? null),
            default => null
        };
    }

    //para demo de punto de venta y probar price list
    public function priceAndRuleForVariant(
        Pricelist $pricelist,
        ProductVariant $variant,
        float $qty = 1,
        ?\Carbon\Carbon $date = null
    ): array {
        $date ??= now();

        $rule = $this->bestRule($pricelist, $variant, $qty, $date);
        $price = $this->priceForVariant($pricelist, $variant, $qty, $date);

        return [$price, $rule];
    }
}
