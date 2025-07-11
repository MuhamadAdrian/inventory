<?php

namespace App\Services\Stock;

use App\Models\Product;
use App\Models\StockTransferRequest;
use App\Services\Product\StockService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class StockTransferService
{
    protected $modelStockTransferRequest;

    public function __construct(StockTransferRequest $modelStockTransferRequest)
    {
        $this->modelStockTransferRequest = $modelStockTransferRequest;
    }

    public function stockTransferRequestQuery()
    {
      return $this->modelStockTransferRequest->newQuery();
    }

    /**
     * Create a new stock transfer request.
     *
     * @param array $requestData Data for StockTransferRequest (request_date, desired_arrival_date, sender_warehouse_id, receiver_warehouse_id, notes).
     * @param array $itemsData Array of products and quantities [{product_id: 1, quantity: 10}, ...].
     * @return StockTransferRequest
     * @throws Exception If the sender and receiver warehouses are the same, or if item quantities are invalid.
     */
    public function createTransferRequest(array $requestData, array $itemsData): StockTransferRequest
    {
        return DB::transaction(function () use ($requestData, $itemsData) {
            $senderWarehouseId = $requestData['sender_warehouse_id'];
            $receiverWarehouseId = $requestData['receiver_warehouse_id'];

            if ($senderWarehouseId === $receiverWarehouseId) {
                throw new Exception("Gudang pengirim dan gudang penerima tidak boleh sama.");
            }

            // Add creator information
            $requestData['created_by_type'] = Auth::check() ? get_class(Auth::user()) : null;
            $requestData['created_by_id'] = Auth::id();

            $transferRequest = StockTransferRequest::create($requestData);

            foreach ($itemsData as $item) {
                if (!isset($item['product_id']) || !isset($item['quantity']) || $item['quantity'] <= 0) {
                    throw new Exception("Kuantitas produk tidak valid untuk transfer.");
                }

                // Opsional: Periksa apakah produk memiliki stok yang cukup di gudang pengirim
                // $currentStock = $this->stockService->getProductStockInWarehouse($item['product_id'], $senderWarehouseId);
                // if ($currentStock < $item['quantity']) {
                //     throw new Exception("Stok tidak cukup untuk produk ID {$item['product_id']} di gudang pengirim.");
                // }

                $transferRequest->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'status' => 'requested', // Status awal item
                ]);
            }

            return $transferRequest;
        });
    }

    /**
     * Get stock transfer requests for DataTables.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getTransferRequestsForDataTables()
    {
        $stockTransferRequest = StockTransferRequest::with(['senderWarehouse', 'receiverWarehouse', 'createdBy'])
          ->select('id', 'request_date', 'desired_arrival_date', 'sender_warehouse_id', 'receiver_warehouse_id', 'status', 'created_at', 'created_by_type', 'created_by_id');

        if (!in_array(auth()->user()->getRoleNames()[0], ['admin', 'owner'])) {
            $stockTransferRequest->where('created_by_id', Auth::id());
        }

        if (auth()->user()->getRoleNames()[0] === 'admin') {
          $stockTransferRequest->where('sender_warehouse_id', auth()->user()->warehouse->id);
        }

        return $stockTransferRequest;
    }

    /**
     * Get stock transfer items for DataTables from a specific request.
     *
     * @param StockTransferRequest $transferRequest
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getTransferItemsForDataTables(StockTransferRequest $transferRequest)
    {
        return $transferRequest->items()->with('product')->select('id', 'stock_transfer_request_id', 'product_id', 'quantity', 'status', 'created_at');
    }

    /**
     * Get products for stock selection in DataTables.
     * Only products with an item_code will be displayed.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getProductsForSelectionDataTables()
    {
        return Product::whereNotNull('item_code')->select('id', 'name', 'item_code', 'price');
    }

    /**
     * Process a stock transfer request by reducing stock from the sender warehouse
     * and adding it to the receiver warehouse.
     *
     * @param StockTransferRequest $transferRequest
     * @throws Exception If stock is insufficient or the status is invalid.
     */
    public function processTransfer($stockTransferRequestId): void
    {
        DB::transaction(function () use ($stockTransferRequestId) {
            $transferRequest = $this->stockTransferRequestQuery()->find($stockTransferRequestId);
            if ($transferRequest->status !== 'pending') {
                throw new Exception("Permintaan transfer stok harus dalam status 'pending' untuk diproses.");
            }
            dd($transferRequest);

            foreach ($transferRequest->items as $item) {
                // Kurangi stok dari gudang pengirim
                $this->stockService->adjustStock(
                    $item->product_id,
                    $transferRequest->sender_warehouse_id,
                    -$item->quantity,
                    "Transfer keluar untuk permintaan #{$transferRequest->id}"
                );

                // Tambahkan stok ke gudang penerima
                $this->stockService->adjustStock(
                    $item->product_id,
                    $transferRequest->receiver_warehouse_id,
                    $item->quantity,
                    "Transfer masuk dari permintaan #{$transferRequest->id}"
                );

                // Perbarui status item
                $item->update(['status' => 'transferred']);
            }

            // Perbarui status permintaan transfer
            $transferRequest->update(['status' => 'completed']);
        });
    }
}
