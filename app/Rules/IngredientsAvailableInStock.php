<?php

namespace App\Rules;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IngredientsAvailableInStock implements ValidationRule
{

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        // Initialize an empty collection to store the required amount of ingredients for all order's products
        $requiredAmountOfIngredients = collect();

        foreach($value as $Orderproduct) {

            $product = isset($Orderproduct['product_id']) 
            ? Product::find($Orderproduct['product_id'])?->load('ingredients') 
            : null;

            if(!$product) {
                $fail("There is an specified product in order does not exist..");
                return;
            }

            foreach ($product->ingredients as $ingredient) {

                $ingredientAmountsUsed = $ingredient->pivot->amount * $Orderproduct['quantity'];

                if(!isset($requiredAmountOfIngredients[$ingredient->name])) {
                    $requiredAmountOfIngredients->put($ingredient->name, $ingredientAmountsUsed);
                } else {
                    $requiredAmountOfIngredients[$ingredient->name] += $ingredientAmountsUsed;
                }

                // Check if the required amount of the ingredient exceeds the available stock
                if($requiredAmountOfIngredients[$ingredient->name] > $ingredient->amount_in_stock) {
                    $fail("Insufficient stock for ingredient: $ingredient->name");
                }
            }
        }

    }
}
