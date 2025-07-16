<?php
namespace App\Services\Product;

use App\Models\ProductBusinessLocation;

class ProductBusinessLocationService
{
  protected $productBusinessLocationModel;

  public function __construct(ProductBusinessLocation $productBusinessLocationModel) {
    $this->productBusinessLocationModel = $productBusinessLocationModel;
  }

  public function productBusinessLocationQuery()
  {
    return $this->productBusinessLocationModel->newQuery();
  }

  public function getListProducts($with = [])
  {
    $query = $this->productBusinessLocationQuery();

    $query->whereHas('product', function($product) {
      $product->where('deleted_at', null);
    });
    
    if (auth()->user()->getRoleNames()[0] === 'kasir') {
      $query->where('business_location_id', auth()->user()->business_location_id);
    }

    if (!empty($with)) {
      $query->with($with);
    }

    return $query;
  }

  public function getProductLocation($productId, $businessLocationId)
  {
    return $this->productBusinessLocationQuery()
      ->where('product_id', $productId)
      ->where('business_location_id', $businessLocationId)
      ->first();
  }
}