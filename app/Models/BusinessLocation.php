<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessLocation extends Model
{
    protected $fillable = [
        'name',
        'code',
        'city',
        'area',
        'phone',
        'type', // warehouse, store, office
    ];

    /**
     * Get the products associated with the business location.
     */
    public function productBusinessLocations()
    {
        return $this->hasMany(ProductBusinessLocation::class);
    }

    /**
     * Get the products associated with the business location.
     */
    public function products()
    {
        return $this->hasMany(ProductBusinessLocation::class);
    }

    /**
     * Get the products associated with the business location.
     */
    public function productStocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    /**
     * Get the users associated with the business location.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
