<?php

namespace Database\Seeders;

use App\Http\Requests\UpdateStockRequest;
use Illuminate\Database\Seeder;
use App\Models\Product;
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
        $user = User::where('email', 'admin@example.com')->first();

        // Create 50 fake products
        Product::factory(50)->create()->each(function (Product $product) use ($user) {
            $request = new UpdateStockRequest();
            $request->merge([
                'product_id' => $product->id,
                'stock' => 100
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
