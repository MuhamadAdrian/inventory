<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\ProductsDataTable;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Milon\Barcode\DNS1D;

class ProductController extends AppController
{
    protected ProductService $productService;
    

    public function __construct(Request $request, ProductService $productService)
    {
        parent::__construct($request);

        $this->middleware('can:view product')->only(['index']);
        $this->middleware('can:create product')->only(['create', 'store']);
        $this->middleware('can:edit product')->only(['edit', 'update']);
        $this->middleware('can:delete product')->only(['destroy']);
        $this->middleware('can:print barcode')->only(['printBarcode']);

        $this->productService = $productService;

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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        $barcodeBase64 = 'data:image/png;base64,' . $dns1d->getBarcodePNG($product->item_code, 'UPCA', 4, 100); // Larger barcode for print

        return view('products.template.print_barcode', compact('product', 'barcodeBase64'));
    }
}
