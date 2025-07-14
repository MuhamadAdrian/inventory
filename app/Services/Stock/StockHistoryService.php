<?php
namespace App\Services\Stock;

use App\Models\ProductStock;

class StockHistoryService {
  protected $productStockHistoryModel;

  public function __construct(ProductStock $productStockHistoryModel) {
    $this->productStockHistoryModel = $productStockHistoryModel;
  }

  public function productStockHistoryQuery() {
    return $this->productStockHistoryModel->newQuery();
  }
}