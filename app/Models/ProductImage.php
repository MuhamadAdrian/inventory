<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; // For deleting files

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'filename',
        'path',
        'is_main',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    /**
     * Get the product that owns the image.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the URL for the image.
     */
    public function getUrlAttribute()
    {
        return Storage::url($this->path . '/' . $this->filename);
    }

    /**
     * Delete the actual file from storage when the model is deleted.
     */
    protected static function booted()
    {
        static::deleting(function ($image) {
            Storage::delete($image->path . '/' . $image->filename);
        });
    }
}
