<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Milon\Barcode\DNS1D; // Import the barcode generator

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $itemCode = $this->faker->unique()->ean13();

        return [
            'name' => $this->faker->words(rand(2, 4), true) . ' ' . $this->faker->suffix(),
            'description' => $this->faker->paragraph(1),
            'price' => $this->faker->randomFloat(2, 80000, 500000), // Price between 10,000 and 500,000
            'stock' => $this->faker->numberBetween(0, 100), // Initial stock when product is created
            'item_code' => $itemCode,
            'color' => $this->faker->safeColorName(),
            'series' => $this->faker->word() . ' Series',
            'category' => $this->faker->randomElement(['Kerudung', 'Sejadah', 'Mukena', 'Sarung', 'Koko', 'Gamis']),
            'material' => $this->faker->randomElement(['Katun', 'Rayon', 'Polyester', 'Sifon', 'Satin']),
            'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL', 'One Size']),
            'weight' => $this->faker->randomFloat(2, 0.1, 10), // Weight in kg
            'brand' => $this->faker->company(),
        ];
    }
}
