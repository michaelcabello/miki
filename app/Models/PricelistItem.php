<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricelistItem extends Model
{
    protected $fillable = [
        'pricelist_id',
        'applied_on',
        'product_template_id',
        'product_variant_id',
        'sequence',
        'min_qty',
        'compute_method',
        'fixed_price',
        'percent_discount',
        'base',
        'base_pricelist_id',
        'price_multiplier',
        'price_surcharge',
        'rounding',
        'min_margin',
        'max_margin',
        'date_start',
        'date_end',
        'active',
        'category_id'
    ];

    protected $casts = [
        'active' => 'boolean',
        'min_qty' => 'decimal:2',
        'fixed_price' => 'decimal:2',
        'percent_discount' => 'decimal:2',
        'price_multiplier' => 'decimal:6',
        'price_surcharge' => 'decimal:2',
        'rounding' => 'decimal:6',
        'min_margin' => 'decimal:2',
        'max_margin' => 'decimal:2',
        'date_start' => 'date',
        'date_end' => 'date',
    ];

    public function pricelist()
    {
        return $this->belongsTo(Pricelist::class);
    }
    public function productTemplate()
    {
        return $this->belongsTo(ProductTemplate::class);
    }
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
    public function basePricelist()
    {
        return $this->belongsTo(Pricelist::class, 'base_pricelist_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // --- LÓGICA DE EDICIÓN ---

    public function startEdit($id)
    {
        $item = PricelistItem::findOrFail($id);

        // Cargamos los datos en el array de edición
        $this->editing[$id] = $item->toArray();
    }

    public function cancelEdit($id)
    {
        unset($this->editing[$id]);
    }

    public function saveEdit($id)
    {
        // 1. Validar
        $this->validate([
            "editing.$id.applied_on" => 'required',
            "editing.$id.compute_method" => 'required',
            "editing.$id.min_qty" => 'required|numeric|min:0',
        ]);

        // 2. Buscar y Actualizar
        $item = PricelistItem::findOrFail($id);

        // Aplicamos limpieza similar a la de addLine
        $data = $this->editing[$id];

        if ($data['compute_method'] == 'fixed') {
            $data['percent_discount'] = 0;
            $data['price_multiplier'] = 1;
        } elseif ($data['compute_method'] == 'discount') {
            $data['fixed_price'] = 0;
            $data['price_multiplier'] = 1;
        }

        $item->update($data);

        // 3. Limpiar estado de edición
        unset($this->editing[$id]);

        $this->dispatch('swal', ['title' => 'Regla actualizada', 'icon' => 'success']);
    }
}
