<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'location'];

    /**
     * Get the product stocks for the warehouse.
     */
    public function productStocks()
    {
        return $this->hasMany(WarehouseProductStock::class);
    }

    /**
     * Get the users assigned to this warehouse.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
