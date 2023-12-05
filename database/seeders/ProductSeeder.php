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

        $product2 = new Product();
        $product2->name = 'Cheese Burger';
        $product2->saveQuietly();

        $product->ingredients()->sync([
            1 => ['amount'=> 150], // Beef
            2 => ['amount'=> 30], // Cheese
            3 => ['amount'=> 20], // Onion
        ]);

        $product2->ingredients()->sync([
            1 => ['amount'=> 100], // Beef
            2 => ['amount'=> 60], // Cheese
            3 => ['amount'=> 10], // Onion
        ]);
    }
}
