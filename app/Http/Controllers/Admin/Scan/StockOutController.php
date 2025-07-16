<?php

namespace App\Http\Controllers\Admin\Scan;

use App\Http\Controllers\Admin\AppController;
use App\Http\Controllers\Controller;
use App\Http\Requests\ScanStockOutRequest;
use App\Models\BusinessLocation;
use App\Models\ProductStock;
use App\Services\BusinessLocation\BusinessLocationService;
use App\Services\Product\ProductBusinessLocationService;
use App\Services\Product\ProductService;
use App\Services\Product\StockService;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockOutController extends AppController
{
    protected $productService;
    protected $businessLocationService;
    protected $stockService;
    protected $productBusinessLocationService;

    public function __construct(Request $request, ProductService $productService, BusinessLocationService $businessLocationService, StockService $stockService, ProductBusinessLocationService $productBusinessLocationService)
    {
        parent::__construct($request);

        $this->productService = $productService;
        $this->businessLocationService = $businessLocationService;
        $this->stockService = $stockService;
        $this->productBusinessLocationService = $productBusinessLocationService;

        $this->middleware('can:scan barcode out');
    }

    public function index()
    {
        return view('admin.scan-product.stock-out');
    }

    public function itemCodeConfirmation(string $itemCode)
    {
        // check produk tersedia di pihak scanner
        $productStock = $this->stockService->getOwnedProductFromStockBy($itemCode, 'item_code');

        if (!$productStock) {
            return redirect('/')
                ->with('error', "Produk tidak tersedia di tempat Anda");
        }

        // $businessLocations = $this->businessLocationService->getEligibleReceiverByItemCode($itemCode);
        $businessLocations = BusinessLocation::whereNot('id', auth()->user()->businessLocation->id)->get();

        if (empty($businessLocations)) {
            return redirect('/')
                ->with('error', "Produk dengan kode tersebut tidak ada di lokasi manapun: {$itemCode}");
        }

        $product = $this->productService->getProductByItemCode($itemCode);

        return view('admin.scan-product.confirmation', compact('businessLocations', 'product', 'itemCode'));
    }

    public function proceedStockOut(ScanStockOutRequest $request)
    {
        try {
            $latestStock = $this->stockService->getOwnedProductFromStockBy($request->product_id, 'id');

            if (!$latestStock) {
                throw new Exception('Stok produk tidak tersedia di tempat Anda', 404);
            }

            $productBusinessLocation = $this->productBusinessLocationService->getProductLocation($request->product_id, auth()->user()->businessLocation->id);

            // check sync stock
            if ($productBusinessLocation->stock !== $latestStock->stock)
            {
                Log::warning('Missmatch final stock for product ID: ' . $productBusinessLocation->product->id . ' In ' . $productBusinessLocation->businessLocation->name);
                // TODO: Send email notification
            }
            
            if ($request->quantity > $latestStock->stock)
            {
                throw new Exception('Stok tidak memadai', 400);
            }
            
            DB::transaction(function () use ($productBusinessLocation, $request) {
                // create stock out from sender
                $senderHistory = $this->stockService->createStockMovementHistory(
                    $productBusinessLocation->id,
                    auth()->user()->businessLocation->id,
                    $request->quantity * -1,
                    Auth::id()
                );
    
                $senderHistory->productBusinessLocation->update([
                    'stock' => $senderHistory->stock
                ]);
    
                // create stock in from receiver
                $productBusinessLocation = $this->productBusinessLocationService->getProductLocation($request->product_id, $request->business_location_id);

                if (!$productBusinessLocation) {
                    $productBusinessLocation = $this->productBusinessLocationService->productBusinessLocationQuery()
                        ->create([
                            'product_id' => $request->product_id,
                            'business_location_id' => $request->business_location_id,
                            'stock' => 0
                        ]);
                }

                $receiverHistory = $this->stockService->createStockMovementHistory(
                    $productBusinessLocation->id,
                    $request->business_location_id,
                    $request->quantity,
                    Auth::id()
                );
    
                $receiverHistory->productBusinessLocation->update([
                    'stock' => $receiverHistory->stock
                ]);
            });

            return redirect('/')->with('success', 'Proses stock out barang berhasil!');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat permintaan transfer stok: ' . $e->getMessage());
        }
    }
}
