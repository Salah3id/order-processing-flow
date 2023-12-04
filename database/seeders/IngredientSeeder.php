<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Merchant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredients = ['Beef'=>20000,'Cheese'=>5000,'Onion'=>1000];

        $merchant = Merchant::create([
            'name' => 'Robert',
            'email' => 'robert@example.com'
        ]);

        foreach($ingredients as $name=>$amount) {
            $ingredient = new Ingredient();
            $ingredient->name = $name;
            $ingredient->merchant_id = $merchant->id;
            $ingredient->initial_amount = $amount;
            $ingredient->amount_in_stock = $amount;
            $ingredient->notified_amount = $amount/2;
            $ingredient->saveQuietly();
        }
    }
}
