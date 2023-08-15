<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a product
        Product::create([
            'name' => 'Product 1',
            'desc' => 'Product 1 description',
            'price' => 1000,
            'stock' => 10,
        ]);
    }
}
