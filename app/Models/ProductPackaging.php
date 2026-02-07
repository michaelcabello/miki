<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPackaging extends Model
{
    protected $table = 'product_packagings';

    protected $fillable = [
        'product_template_id',
        'name',
        'qty',
        'price_sale',
        'barcode',
        'active',
        'sort_order',
    ];

    public function productTemplate(): BelongsTo
    {
        return $this->belongsTo(ProductTemplate::class, 'product_template_id');
    }
}
