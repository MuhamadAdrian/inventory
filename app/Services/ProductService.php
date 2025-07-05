<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductBrand;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\DNS1D; // For 1D barcodes like CODE128
use Milon\Barcode\DNS2D; // For 2D barcodes like QR Code

class ProductService
{
    /**
     * Get all products.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllProducts()
    {
        return Product::all();
    }

    /**
     * Find a product by its ID.
     *
     * @param int $id
     * @return \App\Models\Product|null
     */
    public function findProductById(int $id)
    {
        return Product::with('images')->find($id); // Eager load images
    }

    /**
     * Create a new product.
     *
     * @param array $data
     * @param array $images (UploadedFile instances)
     * @return \App\Models\Product
     */
    public function createProduct(array $data, array $images = []): Product
    {
        return DB::transaction(function () use ($data, $images) {
            // Ensure lookup values exist or create them
            $this->ensureLookupValueExists(ProductCategory::class, $data['category'] ?? null);
            $this->ensureLookupValueExists(ProductColor::class, $data['color'] ?? null);
            $this->ensureLookupValueExists(ProductSize::class, $data['size'] ?? null);
            $this->ensureLookupValueExists(ProductBrand::class, $data['brand'] ?? null);

            $product = Product::create($data); // TODO: filter input

            // Handle image uploads
            $this->uploadAndAssociateImages($product, $images);

            return $product;
        });
    }

    /**
     * Update an existing product.
     *
     * @param \App\Models\Product $product
     * @param array $data
     * @param array $images (UploadedFile instances)
     * @param array $imagesToDelete (IDs of images to delete)
     * @return \App\Models\Product
     */
    public function updateProduct(Product $product, array $data, array $images = [], array $imagesToDelete = []): Product
    {
        return DB::transaction(function () use ($product, $data, $images, $imagesToDelete) {
            // Ensure lookup values exist or create them
            $this->ensureLookupValueExists(ProductCategory::class, $data['category'] ?? null);
            $this->ensureLookupValueExists(ProductColor::class, $data['color'] ?? null);
            $this->ensureLookupValueExists(ProductSize::class, $data['size'] ?? null);
            $this->ensureLookupValueExists(ProductBrand::class, $data['brand'] ?? null);

            $product->update($data); // TODO: filter input

            // Handle image deletions
            $this->deleteImages($imagesToDelete);

            // Handle new image uploads
            $this->uploadAndAssociateImages($product, $images);

            return $product;
        });
    }

    /**
     * Delete a product.
     *
     * @param \App\Models\Product $product
     * @return bool|null
     */
    public function deleteProduct(Product $product): ?bool
    {
        return DB::transaction(function () use ($product) {
            // Images are deleted via model observer in ProductImage model
            return $product->delete();
        });
    }

    /**
     * Prepare data for DataTables.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getProductsForDataTables()
    {
        return Product::select([
            'id',
            'name',
            'item_code',
            'description',
            'price',
            'stock',
            'color',
            'series',
            'category',
            'material',
            'size',
            'weight',
            'brand',
            'created_at'
        ]);
    }

    /**
     * Get all product categories for datalist.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCategories()
    {
        return ProductCategory::all();
    }

    /**
     * Get all product colors for datalist.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getColors()
    {
        return ProductColor::all();
    }

    /**
     * Get all product sizes for datalist.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSizes()
    {
        return ProductSize::all();
    }

    /**
     * Get all product brands for datalist.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBrands()
    {
        return ProductBrand::all();
    }

    /**
     * Ensures a lookup value exists in the database, creating it if not.
     *
     * @param string $modelClass
     * @param string|null $name
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    protected function ensureLookupValueExists(string $modelClass, ?string $name)
    {
        if (empty($name)) {
            return null;
        }
        return $modelClass::firstOrCreate(['name' => $name]);
    }

    /**
     * Generates a barcode string (SVG) from an item code.
     *
     * @param string $itemCode
     * @return string
     */
    public function generateBarcode(string $itemCode): string
    {
        $barcodeGenerator = new DNS1D();
        $barcodeGenerator->setStorPath(__DIR__.'/cache/');
        return $barcodeGenerator->getBarcodeSVG($itemCode, 'UPCA', 2, 50, 'black', true, false); // UPC-A for Univervasl Product Code, width 2, height 39
    }

    /**
     * Uploads and associates images with a product.
     *
     * @param \App\Models\Product $product
     * @param array $images (UploadedFile instances)
     */
    protected function uploadAndAssociateImages(Product $product, array $images = [])
    {
        $mainImageSet = $product->mainImage()->exists(); // Check if a main image already exists

        foreach ($images as $index => $imageFile) {
            if ($imageFile && $imageFile->isValid()) {
                $path = 'public/product_images/' . $product->id;
                $filename = uniqid('img_') . '.' . $imageFile->getClientOriginalExtension();

                // Store the file
                $imageFile->storeAs($path, $filename);

                // Create database entry
                $product->images()->create([
                    'filename' => $filename,
                    'path' => $path, // Store path relative to storage/app
                    'is_main' => !$mainImageSet && $index === 0, // Set first uploaded image as main if no main image exists
                ]);

                if (!$mainImageSet && $index === 0) {
                    $mainImageSet = true; // Mark that a main image has been set
                }
            }
        }
    }

    /**
     * Deletes product images by ID.
     *
     * @param array $imageIds
     */
    protected function deleteImages(array $imageIds = [])
    {
        if (empty($imageIds)) {
            return;
        }
        // ProductImage model's booted method handles file deletion
        ProductImage::whereIn('id', $imageIds)->delete();
    }
}
