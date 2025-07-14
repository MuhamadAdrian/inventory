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
    
    if (auth()->user()->getRoleNames()[0] === 'kasir') {
      $query->where('business_location_id', auth()->user()->business_location_id);
    }

    if (!empty($with)) {
      $query->with($with);
    }

    return $query;
  }
}