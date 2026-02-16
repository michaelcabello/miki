<?php

namespace App\Livewire\Admin\Pos;

use Livewire\Component;
use App\Models\Pricelist;
use App\Models\ProductVariant;
use App\Services\Pricing\PricelistEngine;

class PosDemo extends Component
{
    public ?int $pricelist_id = null;
    public ?int $product_variant_id = null;
    public float $qty = 1;

    public ?float $basePrice = null;
    public ?float $finalPrice = null;

    public ?array $appliedRule = null;

    public function updated($prop)
    {
        if (in_array($prop, ['pricelist_id', 'product_variant_id', 'qty'])) {
            $this->recalculate();
        }
    }

    public function recalculate()
    {
        $this->resetComputed();

        if (!$this->pricelist_id || !$this->product_variant_id || $this->qty < 1) {
            return;
        }

        $pricelist = Pricelist::find($this->pricelist_id);
        $variant = ProductVariant::with('productTemplate.category')->find($this->product_variant_id);

        if (!$pricelist || !$variant) return;

        $this->basePrice = (float)($variant->price_sale ?? 0);

        /** @var PricelistEngine $engine */
        $engine = app(PricelistEngine::class);

        [$price, $rule] = $engine->priceAndRuleForVariant($pricelist, $variant, $this->qty);

        $this->finalPrice = $price !== null ? (float)$price : null;

        if ($rule) {
            $this->appliedRule = [
                'id' => $rule->id,
                'applied_on' => $rule->applied_on,
                'sequence' => $rule->sequence,
                'min_qty' => (float)$rule->min_qty,
                'compute_method' => $rule->compute_method,
                'fixed_price' => $rule->fixed_price,
                'percent_discount' => $rule->percent_discount,
                'category_id' => $rule->category_id,
                'product_template_id' => $rule->product_template_id,
                'product_variant_id' => $rule->product_variant_id,
                'date_start' => $rule->date_start,
                'date_end' => $rule->date_end,
            ];
        }
    }

    private function resetComputed(): void
    {
        $this->basePrice = null;
        $this->finalPrice = null;
        $this->appliedRule = null;
    }

    public function render()
    {
        $pricelists = Pricelist::select('id', 'name')->orderBy('name')->get();

        // Para demo: variants activas
        $variants = ProductVariant::query()
            ->select('id', 'sku', 'variant_name', 'price_sale', 'product_template_id')
            ->where('active', true)
            ->orderBy('sku')
            ->limit(200)
            ->get();

        return view('livewire.admin.pos.pos-demo', compact('pricelists', 'variants'));
    }
}
