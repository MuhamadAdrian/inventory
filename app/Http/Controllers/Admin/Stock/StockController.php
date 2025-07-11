<?php

namespace App\Http\Controllers\Admin\Stock;

use App\DataTables\WarehouseStockDataTable;
use App\Http\Controllers\Admin\AppController;
use App\Http\Controllers\Controller;
use App\Http\Requests\StockTransferRequest;
use App\Models\Product;
use App\Models\StockTransferRequest as ModelsStockTransferRequest;
use App\Models\Warehouse;
use App\Services\Product\ProductService;
use App\Services\Product\StockService;
use App\Services\Stock\StockTransferService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class StockController extends AppController
{
    protected ProductService $productService;
    protected StockTransferService $stockTransferService;
    
    public function __construct(Request $request, ProductService $productService, StockTransferService $stockTransferService)
    {
        parent::__construct($request);

        $this->middleware('can:stock request view')->only(['index', 'show', 'data']);
        $this->middleware('can:stock request process')->only(['destory', 'processTransfer']);

        $this->productService = $productService;
        $this->stockTransferService = $stockTransferService;

        config([
            'site.header' => 'Stock Management',
            'site.breadcrumbs' => [
                ['name' => 'Stock', 'url' => route('stock-transfers.index')],
            ]
        ]);
    }

    /**
     * Display a list of stock transfer requests.
     */
    public function index()
    {
        return view('stocks.index');
    }

    /**
     * Return data for DataTables (list of transfer requests).
     */
    public function data()
    {
        $requests = $this->stockTransferService->getTransferRequestsForDataTables();

        return DataTables::of($requests)
            ->addColumn('sender_warehouse_name', function (ModelsStockTransferRequest $request) {
                return $request->senderWarehouse->name ?? 'N/A';
            })
            ->addColumn('receiver_warehouse_name', function (ModelsStockTransferRequest $request) {
                return $request->receiverWarehouse->name ?? 'N/A';
            })
            ->addColumn('created_by_name', function (ModelsStockTransferRequest $request) {
                return $request->createdBy->name ?? 'System';
            })
            ->addColumn('action', function (ModelsStockTransferRequest $request) {
                return view('stocks.template.action', compact('request'));
            })
            ->editColumn('status', function (ModelsStockTransferRequest $request) {
                switch ($request->status) {
                    case 'pending':
                        return '<span class="badge bg-warning">'.ucfirst($request->status).'</span>';
                    case 'processing':
                        return '<span class="badge bg-info">'.ucfirst($request->status).'</span>';
                    case 'completed':
                        return '<span class="badge bg-success">'.ucfirst($request->status).'</span>';
                    case 'cancelled':
                        return '<span class="badge bg-danger">'.ucfirst($request->status).'</span>';
                    default:
                        return '<span class="badge bg-secondary">'.ucfirst($request->status).'</span>';
                }
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->can('stock request') && !auth()->user()->can('stock input to warehouse'))
        {
            return abort(403, 'User does not have the right permissions.');
        }
        $userWarehouse = Auth::user()->warehouse; // Gudang pengguna yang sedang login
        $warehouseSenders = Warehouse::whereNot('id', $userWarehouse->id)->get();
        $warehouseReceivers = Warehouse::all();

        return view('stocks.create', compact('warehouseSenders', 'warehouseReceivers', 'userWarehouse'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StockTransferRequest $request)
    {
        if (!auth()->user()->can('stock request') && !auth()->user()->can('stock input to warehouse'))
        {
            return abort(403, 'User does not have the right permissions.');
        }
        try {
            $this->stockTransferService->createTransferRequest(
                $request->only(['request_date', 'desired_arrival_date', 'sender_warehouse_id', 'receiver_warehouse_id', 'notes']),
                $request->input('products')
            );

            return redirect()->route('stock-transfers.index')->with('success', 'Permintaan transfer stok berhasil dibuat!');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat permintaan transfer stok: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $stockTransferRequestModel = $this->stockTransferService->stockTransferRequestQuery()->find($id);
        $stockTransferRequestModel->load(['senderWarehouse', 'receiverWarehouse', 'createdBy', 'items.product']);
        return view('stocks.show', compact('stockTransferRequestModel'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function cancelTransfer(int $id)
    {
        $stockTransferRequest = $this->stockTransferService->stockTransferRequestQuery()->find($id);

        if ($stockTransferRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Permintaan transfer stok yang sudah diproses atau dibatalkan tidak dapat dihapus.');
        }

        try {
            $stockTransferRequest->update([
                'status' => 'cancelled'
            ]);

            $stockTransferRequest->items()->update([
                'status' => 'rejected'
            ]);
            return redirect()->route('stocks_transfers.index')->with('success', 'Permintaan transfer stok berhasil dihapus.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus permintaan transfer stok: ' . $e->getMessage());
        }
    }

    /**
     * Process the stock transfer (reduce from sender, add to receiver).
     */
    public function processTransfer($id)
    {
        try {
            $this->stockTransferService->processTransfer($id);
            return redirect()->route('stocks_transfers.index')->with('success', 'Transfer stok berhasil diproses dan diselesaikan!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses transfer stok: ' . $e->getMessage());
        }
    }

    /**
     * Return product data for DataTables (product selection in the form).
     */
    public function getProductsForSelectionData()
    {
        $products = $this->stockTransferService->getProductsForSelectionDataTables();

        return DataTables::of($products)
            ->addColumn('quantity_input', function (Product $product) {
                return "
                    <div class='input-group input-group-sm' style='width: 120px;'>
                        <button type='button' class='btn btn-outline-secondary btn-minus' data-product-id='{$product->id}'>-</button>
                        <input type='number' class='form-control form-control-sm text-center product-quantity'
                               data-product-id='{$product->id}' value='0' min='0' step='1'>
                        <button type='button' class='btn btn-outline-secondary btn-plus' data-product-id='{$product->id}'>+</button>
                    </div>
                ";
            })
            ->rawColumns(['quantity_input'])
            ->make(true);
    }
}
