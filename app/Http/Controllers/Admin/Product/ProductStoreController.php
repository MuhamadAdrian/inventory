<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Admin\AppController;
use App\Models\ProductBusinessLocation;
use App\Services\Product\ProductBusinessLocationService;
use App\Services\Product\ProductService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductStoreController extends AppController
{
    protected $productBusinessLocationService;
    protected $productService;

    public function __construct(Request $productStore, ProductBusinessLocationService $productBusinessLocationService, ProductService $productService)
    {
        parent::__construct($productStore);

        $this->middleware('can:view store product')->only(['index', 'data', 'show']);

        $this->productBusinessLocationService = $productBusinessLocationService;
        $this->productService = $productService;

        config([
            'site.header' => 'Produk Toko',
            'site.breadcrumbs' => [
                ['name' => 'Produk Toko', 'url' => route('products.store.index')],
            ]
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.product-store.index');
    }

    public function data()
    {
        $productStores = $this->productBusinessLocationService->getListProducts(['product']);

        return DataTables::of($productStores)
            ->addColumn('action', function (ProductBusinessLocation $productBusinessLocation) {
                return view('admin.product-store.template.action', ['product' => $productBusinessLocation->product]);
            })
            ->editColumn('product.price', function (ProductBusinessLocation $productBusinessLocation) {
                return $productBusinessLocation->product->formatted_price;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $productStore)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $productId)
    {
        $product = $this->productService->findProductById($productId);

        return view('admin.product-store.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $productStore, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
