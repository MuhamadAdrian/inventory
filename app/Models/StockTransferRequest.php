<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockTransferRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_date',
        'desired_arrival_date',
        'sender_warehouse_id',
        'receiver_warehouse_id',
        'status',
        'notes',
        'created_by_type',
        'created_by_id',
    ];

    protected $casts = [
        'request_date' => 'date',
        'desired_arrival_date' => 'date',
    ];

    /**
     * Dapatkan gudang pengirim.
     */
    public function senderWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'sender_warehouse_id');
    }

    /**
     * Dapatkan gudang penerima.
     */
    public function receiverWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'receiver_warehouse_id');
    }

    /**
     * Dapatkan item transfer stok.
     */
    public function items(): HasMany
    {
        return $this->hasMany(StockTransferItem::class);
    }

    /**
     * Dapatkan entitas yang membuat permintaan transfer stok.
     */
    public function createdBy(): MorphTo
    {
        return $this->morphTo();
    }
}
