<?php

namespace Database\Seeders;

use App\Http\Requests\UpdateStockRequest;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductBusinessLocation;
use App\Models\User;
use App\Services\Product\StockService as ProductStockService;

class ProductSeeder extends Seeder
{
    protected $stockService;

    public function __construct(ProductStockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'staff1@gmail.com')->first();

        // Create 50 fake products
        Product::factory(50)->create()->each(function (Product $product) use ($user) {
            $productBusinessLocation = ProductBusinessLocation::create([
                'product_id' => $product->id,
                'business_location_id' => 1,
                'stock' => 0
            ]);

            $request = new UpdateStockRequest();
            $request->merge([
                'product_business_location_id' => $productBusinessLocation->id,
                'stock' => 100,
                'business_location_id' => 1
            ]);

            try {
                $this->stockService->updateProductStock($request, $user->id, User::class);
            } catch (\Exception $e) {
                $this->command->warn("Gagal menambahkan stok awal untuk produk {$product->name}: " . $e->getMessage());
            }
        });

        $this->command->info('50 produk dummy berhasil dibuat dan stok awalnya ditambahkan ke Gudang Utama.');
    }
}
