<?php

namespace App\Services\Stock;

use App\Models\Product;
use App\Models\StockOutRequest;
use App\Services\Product\StockService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class StockOutRequestService
{
    protected $modelStockOutRequestRequest;

    public function __construct(StockOutRequest $modelStockOutRequestRequest)
    {
        $this->modelStockOutRequestRequest = $modelStockOutRequestRequest;
    }

    public function stockOutRequestQuery()
    {
      return $this->modelStockOutRequestRequest->newQuery();
    }

    /**
     * Create a new stock transfer request.
     *
     * @param array $requestData Data for StockOutRequest (request_date, desired_arrival_date, sender_id, receiver_id, notes).
     * @param array $itemsData Array of products and quantities [{product_id: 1, quantity: 10}, ...].
     * @return StockOutRequest
     * @throws Exception If the sender and receiver warehouses are the same, or if item quantities are invalid.
     */
    public function createTransferRequest(array $requestData, array $itemsData): StockOutRequest
    {
        return DB::transaction(function () use ($requestData, $itemsData) {
            $senderId = $requestData['sender_id'];
            $receiverId = $requestData['receiver_id'];

            if ($senderId === $receiverId) {
                throw new Exception("Lokasi pengirim dan penerima tidak boleh sama.");
            }

            // Add creator information
            $requestData['created_by_type'] = Auth::check() ? get_class(Auth::user()) : null;
            $requestData['created_by_id'] = Auth::id();

            $stockOutRequest = StockOutRequest::create($requestData);

            foreach ($itemsData as $item) {
                if (!isset($item['product_id']) || !isset($item['quantity']) || $item['quantity'] <= 0) {
                    throw new Exception("Kuantitas produk tidak valid untuk stok keluar.");
                }

                // Opsional: Periksa apakah produk memiliki stok yang cukup di gudang pengirim
                // $currentStock = $this->stockService->getProductStockInWarehouse($item['product_id'], $senderId);
                // if ($currentStock < $item['quantity']) {
                //     throw new Exception("Stok tidak cukup untuk produk ID {$item['product_id']} di gudang pengirim.");
                // }

                $stockOutRequest->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'status' => 'requested', // Status awal item
                ]);
            }

            return $stockOutRequest;
        });
    }

    /**
     * Get stock transfer requests for DataTables.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getTransferRequestsForDataTables()
    {
        $stockOutRequest = StockOutRequest::with(['sender', 'receiver', 'createdBy'])
          ->select('id', 'request_date', 'desired_arrival_date', 'sender_id', 'receiver_id', 'status', 'created_at', 'created_by_type', 'created_by_id');

        if (!in_array(auth()->user()->getRoleNames()[0], ['staff', 'owner'])) {
            $stockOutRequest->where('created_by_id', Auth::id());
        }

        if (auth()->user()->getRoleNames()[0] === 'staff') {
            $stockOutRequest->whereHas('sender', function ($query) {
                $query->where('area', Auth::user()->businessLocation->area);
            });
        }

        return $stockOutRequest;
    }

    /**
     * Get stock transfer items for DataTables from a specific request.
     *
     * @param StockOutRequest $stockOutRequest
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getTransferItemsForDataTables(StockOutRequest $stockOutRequest)
    {
        return $stockOutRequest->items()->with('product')->select('id', 'stock_out_request_id', 'product_id', 'quantity', 'status', 'created_at');
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
     * @param StockOutRequest $stockOutRequest
     * @throws Exception If stock is insufficient or the status is invalid.
     */
    public function processTransfer($StockOutRequestRequestId): void
    {
        DB::transaction(function () use ($StockOutRequestRequestId) {
            $stockOutRequest = $this->stockOutRequestQuery()->find($StockOutRequestRequestId);
            if ($stockOutRequest->status !== 'pending') {
                throw new Exception("Permintaan transfer stok harus dalam status 'pending' untuk diproses.");
            }

            $stockOutRequest->update(['status' => 'processing', 'approver_id' => Auth::id()]);

            return $stockOutRequest;
        });
    }

    /**
     * Cancel a stock transfer request.
     *
     * @param int $id
     * @throws Exception If the request is already processed or cancelled.
     */
    public function cancelTransfer(int $id): void
    {
        DB::transaction(function () use ($id) {
            $stockOutRequest = $this->stockOutRequestQuery()->find($id);
            if ($stockOutRequest->status !== 'pending') {
                throw new Exception("Permintaan stok keluar yang sudah diproses atau dibatalkan tidak dapat dihapus.");
            }

            $stockOutRequest->update(['status' => 'cancelled', 'approver_id' => Auth::id()]);
            $stockOutRequest->items()->update(['status' => 'rejected']);
        });
    }

    /**
     * Update the status of all items in a stock out request.
     *
     * @param StockOutRequest $stockOutRequest
     * @param string $status
     * @throws Exception If the status is invalid.
     */
    public function updateStatusRequestItems(StockOutRequest $stockOutRequest, ?string $status = null): void
    {
        DB::transaction(function () use ($stockOutRequest, $status) {
            if ($status) {
                $stockOutRequest->update(['status' => 'shipping']);
                $stockOutRequest->items()->update(['status' => $status]);
            } else {
                if (!$stockOutRequest->document_printed_at) {
                    $stockOutRequest->update(['document_printed_at' => now()]);
                }
            }
        });
    }
}
