<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductBusinessLocation extends Model
{
    protected $fillable = [
        'product_id',
        'business_location_id',
        'stock'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->whereNull('deleted_at');
    }

    public function businessLocation(): BelongsTo
    {
        return $this->belongsTo(BusinessLocation::class);
    }

    public function productStocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function getFinalStockAttribute(): int
    {
        return $this->productStocks()->sum('quantity');
    }

}
