<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'item_code', // Kode Barang (Item Code)
        'color',     // Warna (Color)
        'series',    // Seri (Series)
        'category',  // Kategori Barang / Produk (Category)
        'material',  // Bahan (Material)
        'size',      // Ukuran (Size)
        'weight',    // Berat (Weight)
        'brand',     // Merek
        'barcode_print_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    // ================== Accessors =====================
    protected function getFormattedPriceAttribute($value)
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    // ================== RELATION =====================

    /**
     * Get the images for the product.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Get the main image for the product.
     */
    public function mainImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_main', true);
    }

    public function productStocks(): HasManyThrough
    {
        return $this->hasManyThrough(
            ProductStock::class,
            ProductBusinessLocation::class,
            'product_id', // Foreign key on ProductBusinessLocation
            'product_business_location_id', // Foreign key on ProductStock
            'id', // Local key on Product
            'id'  // Local key on ProductBusinessLocation
        );
    }

}
