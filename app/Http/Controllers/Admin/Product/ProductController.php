<?php

namespace App\Http\Controllers\Admin\Product;

use App\DataTables\ProductsDataTable;
use App\Http\Controllers\Admin\AppController;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\UpdateStockRequest;
use App\Models\Product;
use App\Services\Product\ProductService;
use App\Services\Product\StockService;
use Illuminate\Http\Request;
use Milon\Barcode\DNS1D;

class ProductController extends AppController
{
    protected ProductService $productService;
    protected StockService $stockService;
    

    public function __construct(Request $request, ProductService $productService, StockService $stockService)
    {
        parent::__construct($request);

        $this->middleware('can:view product')->only(['index']);
        $this->middleware('can:create product')->only(['create', 'store']);
        $this->middleware('can:edit product')->only(['edit', 'update']);
        $this->middleware('can:delete product')->only(['destroy']);
        $this->middleware('can:print barcode')->only(['printBarcode']);
        $this->middleware('can:scan barcode')->only(['processScanStock', 'showScanStockForm']);

        $this->productService = $productService;
        $this->stockService = $stockService;

        config([
            'site.header' => 'Product List',
            'site.breadcrumbs' => [
                ['name' => 'Products', 'url' => route('products.index')],
            ]
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(ProductsDataTable $dataTable)
    {
        return $dataTable->render('products.index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->productService->getCategories();
        $colors = $this->productService->getColors();
        $sizes = $this->productService->getSizes();
        $brands = $this->productService->getBrands();

        return view('products.create', compact('categories', 'colors', 'sizes', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
         $images = $request->file('images') ?? [];

        $this->productService->createProduct($request->except('images'), $images);

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product = $this->productService->findProductById($product->id);
        $categories = $this->productService->getCategories();
        $colors = $this->productService->getColors();
        $sizes = $this->productService->getSizes();
        $brands = $this->productService->getBrands();

        return view('products.edit', compact('product', 'categories', 'colors', 'sizes', 'brands'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $images = $request->file('images') ?? [];

        $imagesToDelete = $request->input('existing_images_to_delete', []);

        $this->productService->updateProduct($product, $request->except(['images', 'existing_images_to_delete']), $images, $imagesToDelete);

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $this->productService->deleteProduct($product);

        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }

        /**
     * Display a print-friendly view of the product barcode.
     */
    public function printBarcode(Product $product)
    {
        // Ensure the product has an item code to generate a barcode
        if (!$product->item_code) {
            return redirect()->back()->with('error', 'Cannot print barcode: Product does not have an Item Code.');
        }

        // Generate the barcode image (base64 PNG)
        $dns1d = new DNS1D();
        $barcodeBase64 = 'data:image/png;base64,' . $dns1d->getBarcodePNG($product->item_code, 'C128', 4, 100); // Larger barcode for print
        $barcodeCount = request()->input('count', 1);

        return view('products.template.print_barcode', compact('product', 'barcodeBase64', 'barcodeCount'));
    }

    /**
     * Show the form for scanning barcode and adjusting stock.
     */
    public function showScanStockForm()
    {
        return view('products.scan_stock');
    }

    /**
     * Process barcode scan and adjust product stock.
     */
    public function processScanStock(Request $request)
    {
        $request->validate([
            'item_code_or_barcode' => ['required', 'string', 'max:255'],
            'quantity_change' => ['required', 'integer', 'min:-999999999', 'max:999999999'], // Allow large changes
        ]);

        $identifier = $request->input('item_code_or_barcode');
        $quantityChange = $request->input('quantity_change');

        try {
            $product = $this->productService->adjustProductStock($identifier, $quantityChange);

            if (!$product) {
                return response()->json(['success' => false, 'message' => 'Product not found with the provided identifier.'], 404);
            }

            // Generate barcode for the response
            $barcodeBase64 = $product->item_code ? $this->productService->generateBarcode($product->item_code) : null;

            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully!',
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'item_code' => $product->item_code,
                    'current_stock' => $product->stock,
                    'barcode_image' => $barcodeBase64,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function updateStock(UpdateStockRequest $request)
    {
        $this->stockService->updateProductStock($request);

        return redirect()->route('products.index')->with('success', 'Product stock has been updated !');
    }
}
