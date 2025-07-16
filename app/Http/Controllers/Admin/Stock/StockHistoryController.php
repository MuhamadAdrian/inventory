<?php

namespace App\Http\Controllers\Admin\Stock;

use App\Http\Controllers\Admin\AppController;
use App\Http\Controllers\Controller;
use App\Models\ProductStock;
use App\Services\Stock\StockHistoryService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StockHistoryController extends AppController
{
    protected $productStockHistoryService;

    public function __construct(Request $request, StockHistoryService $productStockHistoryService) {
        parent::__construct($request);

        $this->middleware('can:view product stock history')->only(['index', 'show', 'data']);

        $this->productStockHistoryService = $productStockHistoryService;

        config([
            'site.header' => 'Histori Perpindahan Stok',
            'site.breadcrumbs' => [
                ['name' => 'Histori Perpindahan Stok', 'url' => route('stock-history.index')],
            ]
        ]);
    }

    public function index()
    {
        return view('admin.stock-history.index');
    }

    public function data()
    {
        $productStockHistories = $this->productStockHistoryService->productStockHistoryQuery()
            ->with(['productBusinessLocation.product', 'businessLocation', 'causer']);
        
        return DataTables::of($productStockHistories)
            ->addColumn('action', function (ProductStock $productStockHistory) {
                return view('admin.stock-history.template.action', compact('productStockHistory'));
            })
            ->editColumn('quantity', function (ProductStock $productStockHistory) {
                return abs($productStockHistory->quantity);
            })
            ->editColumn('created_at', function (ProductStock $productStockHistory) {
                return $productStockHistory->created_at->format('Y-m-d H:i:s');
            })
            ->editColumn('type', function (ProductStock $productStockHistory) {
                return $productStockHistory->quantity < 0 ? '<span class="badge bg-danger">Keluar</span>' : '<span class="badge bg-success">Masuk</span>';
            })
            ->rawColumns(['action', 'type'])
            ->make(true);
    }

    public function show(int $id)
    {

    }
}
