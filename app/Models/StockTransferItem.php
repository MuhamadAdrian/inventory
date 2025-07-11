<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransferItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_transfer_request_id',
        'product_id',
        'quantity',
        'status',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * Dapatkan permintaan transfer stok yang memiliki item ini.
     */
    public function stockTransferRequest(): BelongsTo
    {
        return $this->belongsTo(StockTransferRequest::class);
    }

    /**
     * Dapatkan produk yang terkait dengan item ini.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
