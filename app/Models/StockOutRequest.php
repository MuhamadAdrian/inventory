<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockOutRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_date',
        'desired_arrival_date',
        'sender_id',
        'receiver_id',
        'status',
        'notes',
        'created_by_type',
        'created_by_id',
        'document_printed_at',
        'approver_id'
    ];

    protected $casts = [
        'request_date' => 'date',
        'desired_arrival_date' => 'date',
        'document_printed_at' => 'datetime'
    ];

    // ======================== Accessores and Mutators ========================

    protected function status(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $user = auth()->user();

                if ($user && $user->getRoleNames()[0] === 'gudang' && $value === 'processing') {
                    return 'Perlu dikirim';
                }

                return $value;
            },
        );
    }

    /**
     * Dapatkan gudang pengirim.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(BusinessLocation::class, 'sender_id');
    }

    /**
     * Dapatkan gudang penerima.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(BusinessLocation::class, 'receiver_id');
    }

    /**
     * Dapatkan item transfer stok.
     */
    public function items(): HasMany
    {
        return $this->hasMany(StockOutRequestItem::class);
    }

    /**
     * Dapatkan entitas yang membuat permintaan transfer stok.
     */
    public function createdBy(): MorphTo
    {
        return $this->morphTo();
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
