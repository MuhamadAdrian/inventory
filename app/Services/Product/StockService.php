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

  public function getOwnedProductFromStockBy($value, string $column, ?int $businessLocationId = null)
  {
      return ProductStock::whereHas('productBusinessLocation', function($productBusinessLocation) use($value, $column) {
          $productBusinessLocation->whereHas('product', function ($product) use($value, $column) {
            $product->where($column, $value);
          });
      })->where('business_location_id', $businessLocationId ?? auth()->user()->businessLocation->id)
      ->latest()
      ->first();
  }

  public function updateProductStock(UpdateStockRequest $request, $userId = null, $userType = null)
  {
    DB::transaction(function() use ($request, $userId, $userType) {
      $previousProductStock = $this->productStockQuery()
        ->where('product_business_location_id', $request->product_business_location_id)
        ->latest('created_at')
        ->first();

      $previousStockTotal = 0;
      if ($previousProductStock) {
          $previousStockTotal = $previousProductStock->stock;
      }

      $newStockTotal = $previousStockTotal + $request->stock;

      $businessLocation = null;
      if (auth()->user()) {
        $businessLocation = BusinessLocation::where('type', 'warehouse')
          ->where('city', auth()->user()->businessLocation->city)
          ->where('area', auth()->user()->businessLocation->area)
          ->first();
      }

      $productStock = $this->productStockQuery()
        ->create([
          'product_business_location_id'  => $request->product_business_location_id,
          'quantity'    => $request->stock,
          'causer_type' => $userType ?? User::class,
          'causer_id'   => $userId ?? Auth::id(),
          'stock'       => $newStockTotal,
          'business_location_id' => $businessLocation ? $businessLocation->id : $request->business_location_id,
        ]);

      return $productStock->productBusinessLocation()->update([
        'stock' => $productStock->stock
      ]);
    });
  }

  public function createStockMovementHistory (int $productBusinessLocationId, int $businessLocationId, int $quantity, int $causerId)
  {
      $previousProductStockSender = $this->productStockQuery()
        ->where('product_business_location_id', $productBusinessLocationId)
        ->where('business_location_id', $businessLocationId)
        ->latest('created_at')
        ->first();

      $previousStockTotalSender = 0;
      if ($previousProductStockSender) {
          $previousStockTotalSender = $previousProductStockSender->stock;
      }

      $newStockTotalSender = $previousStockTotalSender + $quantity;

      $productStock = $this->productStockQuery()
          ->create([
              'product_business_location_id' => $productBusinessLocationId,
              'business_location_id' => $businessLocationId,
              'quantity' => $quantity,
              'causer_type' => get_class(Auth::user()),
              'causer_id' => $causerId,
              'stock' => $newStockTotalSender
          ]);

      if ($productStock->stock < 10) {
        // TODO: Send email notification
      }
 

      return $productStock;
  }

  public function updateActualStock(int $producId, int $businessLocationId)
  {}
}
