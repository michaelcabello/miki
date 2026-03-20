<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{

    protected $fillable = [
        'product_variant_id',
        'disk',
        'path',
        'original_path',
        'large_path',
        'medium_path',
        'thumb_path',
        'original_name',
        'mime_type',
        'size',
        'width',
        'height',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    protected function buildUrl(?string $path): string
    {
        if (!$path) {
            return '';
        }

        return Storage::disk($this->disk)->url($path);
    }

    /*  public function getImageUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    } */

    public function getImageUrlAttribute(): string
    {
        if ($this->medium_path) {
            return $this->buildUrl($this->medium_path);
        }

        if ($this->path) {
            return $this->buildUrl($this->path);
        }

        return $this->buildUrl($this->original_path);
    }


    public function getOriginalUrlAttribute(): string
    {
        return $this->buildUrl($this->original_path ?: $this->path);
    }

    public function getLargeUrlAttribute(): string
    {
        return $this->buildUrl($this->large_path ?: $this->original_path ?: $this->path);
    }

    public function getMediumUrlAttribute(): string
    {
        return $this->buildUrl($this->medium_path ?: $this->large_path ?: $this->original_path ?: $this->path);
    }

    public function getThumbUrlAttribute(): string
    {
        return $this->buildUrl($this->thumb_path ?: $this->medium_path ?: $this->large_path ?: $this->original_path ?: $this->path);
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = (int) $this->size;

        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        if ($bytes < 1024 * 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }

        return number_format($bytes / 1024 / 1024, 2) . ' MB';
    }

}
