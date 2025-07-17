<?php

namespace App\Observers;

use App\Models\ProductStock;
use App\Services\Stock\StockStreamService;

use Illuminate\Support\Facades\Log;

class ProductStockObserver
{
    public function created(ProductStock $stock): void
    {
        try {
            app(StockStreamService::class)->handleNewMovement($stock);
        } catch (\Throwable $e) {
            Log::error('StockStreamService failed: ' . $e->getMessage(), [
                'stock_id' => $stock->id ?? null,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}