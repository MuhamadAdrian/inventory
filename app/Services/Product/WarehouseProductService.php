<?php

namespace App\Services\Product;

use App\Http\Requests\UpdateStockRequest;
use App\Models\ProductStock;
use App\Models\User;
use App\Models\WarehouseProductStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WarehouseProductService
{
  protected $warehouseProductModel;

  public function __construct(WarehouseProductStock $warehouseProductModel) {
    $this->warehouseProductModel = $warehouseProductModel;
  }

  public function warehouseProductStockQuery() {
    return $this->warehouseProductModel->newQuery();
  }
}