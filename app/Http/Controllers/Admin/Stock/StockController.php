<?php

namespace App\Http\Controllers\Admin\Stock;

use App\Http\Controllers\Admin\AppController;
use App\Http\Requests\StockInStoreRequest;
use App\Http\Requests\StockOutRequestRequest;
use App\Models\Product;
use App\Models\ProductBusinessLocation;
use App\Models\StockOutRequest;
use App\Models\StockOutRequestItem;
use App\Services\BusinessLocation\BusinessLocationService;
use App\Services\Product\ProductService;
use App\Services\Stock\StockOutRequestService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Milon\Barcode\DNS2D;
use Yajra\DataTables\Facades\DataTables;

class StockController extends AppController
{
    protected ProductService $productService;
    protected StockOutRequestService $stockOutRequestService;
    protected BusinessLocationService $businessLocationService;
    
    public function __construct(
        Request $request,
        ProductService $productService,
        StockOutRequestService $stockOutRequestService,
        BusinessLocationService $businessLocationService
    ) {
        parent::__construct($request);

        $this->middleware('can:view stock request')->only(['index', 'show', 'data']);
        $this->middleware('can:approval stock request')->only(['destory', 'processTransfer']);

        $this->productService = $productService;
        $this->stockOutRequestService = $stockOutRequestService;
        $this->businessLocationService = $businessLocationService;

        config([
            'site.header' => 'Stock Out Management',
            'site.breadcrumbs' => [
                ['name' => 'Stock Out Request', 'url' => route('stock-out-requests.index')],
            ]
        ]);
    }

    /**
     * Display a list of stock transfer requests.
     */
    public function index()
    {
        return view('admin.stock-out-requests.index');
    }

    /**
     * Return data for DataTables (list of transfer requests).
     */
    public function data()
    {
        $requests = $this->stockOutRequestService->getTransferRequestsForDataTables();

        return DataTables::of($requests)
            ->addColumn('sender_name', function (StockOutRequest $request) {
                return $request->sender->name ?? 'N/A';
            })
            ->addColumn('receiver_name', function (StockOutRequest $request) {
                return $request->receiver->name ?? 'N/A';
            })
            ->addColumn('created_by_name', function (StockOutRequest $request) {
                return $request->createdBy->name ?? 'System';
            })
            ->addColumn('action', function (StockOutRequest $request) {
                return view('admin.stock-out-requests.template.action', compact('request'));
            })
            ->editColumn('request_date', function (StockOutRequest $request) {
                return $request->request_date->format('d-m-Y');
            })
            ->editColumn('desired_arrival_date', function (StockOutRequest $request) {
                return $request->desired_arrival_date ? $request->desired_arrival_date->format('d-m-Y') : 'N/A';
            })
            ->editColumn('created_at', function (StockOutRequest $request) {
                return $request->created_at->format('d-m-Y');
            })
            ->editColumn('status', function (StockOutRequest $request) {
                switch ($request->status) {
                    case 'pending':  
                        return '<span class="badge bg-warning">'.ucfirst($request->status).'</span>';
                    case 'processing':
                        return '<span class="badge bg-info">'.ucfirst($request->status).'</span>';
                    case 'Perlu dikirim':
                        return '<span class="badge bg-warning">'.ucfirst($request->status).'</span>';
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
        if (!auth()->user()->can('create stock request') && !auth()->user()->can('direct stock out'))
        {
            return abort(403, 'User does not have the right permissions.');
        }

        $locations = $this->businessLocationService->businessLocationQuery()->get();

        return view('admin.stock-out-requests.create', compact('locations'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StockOutRequestRequest $request)
    {
        if (!auth()->user()->can('create stock request') && !auth()->user()->can('direct stock out'))
        {
            return abort(403, 'User does not have the right permissions.');
        }
        try {
            $this->stockOutRequestService->createTransferRequest(
                $request->only(['request_date', 'desired_arrival_date', 'sender_id', 'receiver_id', 'notes']),
                $request->input('products')
            );

            return redirect()->route('stock-out-requests.index')->with('success', 'Permintaan transfer stok berhasil dibuat!');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat permintaan transfer stok: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $stockOutRequest = $this->stockOutRequestService->stockOutRequestQuery()->find($id);
        $stockOutRequest->load(['sender', 'receiver', 'createdBy', 'items.product']);

        $dns2d = new DNS2D();
        $stockOutRequest->items->each(function ($item) use ($stockOutRequest, $dns2d) {
            $item->qr_scan_url = 'data:image/png;base64,' .
                $dns2d->getBarcodePNG(
                    route('stock-out-requests.stock_in_confirmation_page', [
                        'stock_out_request' => $stockOutRequest->id,
                        'item_id' => $item->id
                    ]),
                    'QRCODE',
                    2,
                    2
                );
        });
        return view('admin.stock-out-requests.show', compact('stockOutRequest'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function cancelTransfer(int $id)
    {
        try {
            $this->stockOutRequestService->cancelTransfer($id);
            return redirect()->route('stock_out_requests.index')->with('success', 'Permintaan transfer stok berhasil dibatalkan.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal membatalkan permintaan transfer stok: ' . $e->getMessage());
        }
    }

    /**
     * Process the stock transfer (reduce from sender, add to receiver).
     */
    public function processTransfer($id)
    {
        try {
            $this->stockOutRequestService->processTransfer($id);
            return redirect()->back()->with('success', 'Permintaan stok keluar berhasil diproses.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses permintaan stok keluar: ' . $e->getMessage());
        }
    }

    /**
     * Return product data for DataTables (product selection in the form).
     */
    public function getProductsForSelectionData(Request $request)
    {
        $productBusiness = $this->stockOutRequestService->getProductsForSelectionDataTables();

        if ($request->filled('sender_id_filter') && $request->input('sender_id_filter') !== '') {
            $locationFilter = $request->input('sender_id_filter');

            $productBusiness->where('business_location_id', $locationFilter);
        }

        
        $productBusiness = $productBusiness->get();

        return DataTables::of($productBusiness)
            ->addColumn('quantity_input', function (ProductBusinessLocation $productBusiness) {
                return "
                    <div class='input-group input-group-sm' style='width: 120px;'>
                        <button type='button' class='btn btn-outline-secondary btn-minus' data-product-id='{$productBusiness->product->id}'>-</button>
                        <input type='number' class='form-control form-control-sm text-center product-quantity'
                               data-product-id='{$productBusiness->product->id}' value='0' min='0' step='1'>
                        <button type='button' class='btn btn-outline-secondary btn-plus' data-product-id='{$productBusiness->product->id}'>+</button>
                    </div>
                ";
            })
            ->addColumn('formatted_price', function(ProductBusinessLocation $productBusiness) {
                return $productBusiness->product->formatted_price;
            })
            ->rawColumns(['quantity_input'])
            ->make(true);
    }

    /**
     * Scan a stock out item and update its status.
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function sendStockOutRequest(int $id)
    {
        try {
            $stockOutRequest = $this->stockOutRequestService
                ->stockOutRequestQuery()
                ->findOrFail($id);

            $this->stockOutRequestService->updateStatusRequestItems($stockOutRequest, 'transferred');

            return redirect()->route('stock-out-requests.index')->with('success', 'Permintaan stock keluar mulai dikirim ke penerima.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal merubah status ' . $e->getMessage());
        }
    }
    
    /**
     * Print the stock out request document.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function printStockOutRequest(int $id)
    {
        try{
            $stockOutRequest = $this->stockOutRequestService
                ->stockOutRequestQuery()
                ->findOrFail($id);

            $this->stockOutRequestService->updateStatusRequestItems($stockOutRequest);
            $stockOutRequest->load(['sender', 'receiver', 'createdBy', 'items.product', 'approver']);

            $dns2d = new DNS2D();

            $stockOutRequest->items->each(function ($item) use ($stockOutRequest, $dns2d) {
                $item->qr_scan_url = 'data:image/png;base64,' .
                    $dns2d->getBarcodePNG(
                        route('stock-out-requests.stock_in_confirmation_page', [
                            'stock_out_request' => $stockOutRequest->id,
                            'item_id' => $item->id
                        ]),
                        'QRCODE',
                        2,
                        2
                    );
            });

            return view('admin.stock-out-requests.template.print', compact('stockOutRequest'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat dokumen: ' . $e->getMessage());
        }
    }

    public function stockInConfirmationPage(int $stockOutRequestId, int $itemId)
    {
        config([
            'site.header' => 'Konfirmasi Stok Masuk',
            'site.breadcrumbs' => [
                ['name' => 'Stock Out Request', 'url' => route('stock-out-requests.index')],
                ['name' => 'Konfirmasi Stok Masuk', 'url' => route('stock-out-requests.show', $stockOutRequestId)],
            ]
        ]);

        try {

            $item = StockOutRequestItem::where('id', $itemId)
                ->where('stock_out_request_id', $stockOutRequestId)
                ->firstOrFail();

            if ($item->status !== 'transferred') {
                abort(404, 'Item tidak ditemukan atau tidak dalam status yang benar untuk konfirmasi stok masuk.');
            }

            return view('admin.stock-in.template.proceed', compact('item'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal mendapatkan informasi : ' . $e->getMessage());
        }
    }

    public function stockInStoreProceed(StockInStoreRequest $request, int $stockOutRequestItemId)
    {
        try {
            if ($request->input('quantity') <= 0) {
                return redirect()->back()->with('error', 'Jumlah stok yang dimasukkan harus lebih dari nol.');
            }

            $productBusinessLocation = $this->stockOutRequestService->inputProductToStore($stockOutRequestItemId, $request->input('quantity'));

            if ($productBusinessLocation < 10){
                // TODO: Send email
            }

            return redirect()->route('products.store.index')->with('success', 'Produk berhasil diperbarui');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal input produk : ' . $e->getMessage());
        }
    }

    public function forceStockOutRequest(int $stockOutRequestId)
    {
        DB::beginTransaction();

        $errors = [];

        try {
            $stockOutRequest = $this->stockOutRequestService->stockOutRequestQuery()
                ->findOrFail($stockOutRequestId);

            foreach ($stockOutRequest->items as $item) {
                try {
                    if ($item->quantity <= 0) {
                        throw new Exception("Quantity must be greater than 0 for item ID {$item->id}");
                    }

                    $productBusinessLocation = $this->stockOutRequestService->inputProductToStore($item->id, $item->quantity);

                    if (!$productBusinessLocation) {
                        throw new Exception("Failed to process item ID {$item->id}: ProductBusinessLocation not found.");
                    }

                    if ($productBusinessLocation->stock < 10) {
                        // TODO: Send low stock email
                    }

                } catch (Exception $itemException) {
                    $errors[] = $itemException->getMessage();
                    Log::error($itemException->getMessage());
                    Log::info($item);
                    continue;
                }
            }

            DB::commit();

            if (!empty($errors)) {
                return redirect()->back()->with('warning', 'Processed with warnings: ' . implode(', ', $errors));
            }

            return redirect()->back()->with('success', 'Stock out processed successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal force update permintaan stok produk: ' . $e->getMessage());
        }
    }
}
