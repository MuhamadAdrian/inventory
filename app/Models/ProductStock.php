<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductStock extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_business_location_id',
        'quantity',
        'causer_type',
        'causer_id',
        'stock',
        'business_location_id'
    ];

    /**
     * Get the product.
     */
    public function productBusinessLocation(): BelongsTo
    {
        return $this->belongsTo(ProductBusinessLocation::class);
    }

    /**
     * Get the user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the causer of the stock movement.
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the business location associated with the product stock.
     */
    public function businessLocation(): BelongsTo
    {
        return $this->belongsTo(BusinessLocation::class);
    }
}
