<?php

namespace App\Services\Product;

use App\Http\Requests\UpdateStockRequest;
use App\Models\BusinessLocation;
use App\Models\ProductStock;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockService
{
  protected $productStockModel;

  public function __construct(ProductStock $productStockModel) {
    $this->productStockModel = $productStockModel;
  }

  public function productStockQuery() {
    return $this->productStockModel->newQuery();
  }

  public function updateProductStock(UpdateStockRequest $request, $userId = null, $userType = null)
  {
    DB::transaction(function() use ($request, $userId, $userType) {
      $previousProductStock = $this->productStockQuery()
        ->where('product_id', $request->product_id)
        ->latest('created_at')
        ->first();

      $previousStockTotal = 0;
      if ($previousProductStock) {
          $previousStockTotal = $previousProductStock->stock;
      }

      $newStockTotal = $previousStockTotal + $request->stock;

      $businessLocation = BusinessLocation::where('type', 'warehouse')
        ->where('city', auth()->user()->businessLocation->city)
        ->where('area', auth()->user()->businessLocation->area)
        ->first();

      $productStock = $this->productStockQuery()
        ->create([
          'product_id'  => $request->product_id,
          'quantity'    => $request->stock,
          'causer_type' => $userType ?? User::class,
          'causer_id'   => $userId ?? Auth::id(),
          'stock'       => $newStockTotal,
          'business_location_id' => $businessLocation ? $businessLocation->id : null,
        ]);

      return $productStock->product()->update([
        'stock' => $productStock->stock
      ]);
    });
  }
}