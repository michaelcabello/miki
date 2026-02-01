<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTemplateAttribute extends Model
{
     protected $fillable = [
        'product_template_id',
        'attribute_id',
    ];

    public function productTemplate()
    {
        return $this->belongsTo(ProductTemplate::class);
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function values()
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'attribute_value_product_template_attribute',
            'product_template_attribute_id',
            'attribute_value_id'
        )->withTimestamps();
    }
}
