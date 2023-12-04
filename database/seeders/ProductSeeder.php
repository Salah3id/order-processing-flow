<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product = new Product();
        $product->name = 'Burger';
        $product->saveQuietly();

        $product->ingredients()->sync([
            1 => ['amount'=> 150], // Beef
            2 => ['amount'=> 30], // Cheese
            3 => ['amount'=> 20], // Onion
        ]);
    }
}
