<?php

namespace App\Repositories;

use App\Events\LowIngredientInStock;
use App\Exceptions\DataRaceException;
use App\Interfaces\OrderRepositoryInterface;
use App\Models\Ingredient;
use App\Models\Order;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface 
{
    /**
     * Creates a new order and associates it with the specified products and quantities
     *
     * @param array $products An associative array of product IDs and their corresponding quantities
     * @return Order The newly created order
     */
    public function createWithProducts(array $products) : Order
    {
        $order = Order::create();
        $productsIds = array_column($products, 'product_id'); 
        $quantities = array_column($products, 'quantity');
        $quantities = collect($quantities)->map(function ($value) {
            return ['quantity' => $value];
        })->toArray();

        $order->products()->sync(array_combine($productsIds, $quantities));

        return $order->load('products');
    }

    /**
     * Get the ingredients used by the given products.
     *
     * @param array $products An array containing product data with 'product_id' as key or index.
     * @return \Illuminate\Database\Eloquent\Builder ingredients used by the products.
     */
    public function getIngredients(array $products) : Builder
    {
        $products = array_column($products, 'product_id'); 
        return Ingredient::UsedForProducts($products);
    }

    /**
     * Updates the stock levels of ingredients associated with the specified order
     *
     * @param Order $order The order to update ingredients for
     * @param Builder $ingredientsVersion A reference to the ingredient versions at the beginning of the request
     * @throws DataRaceException if ingredient stock levels have changed since the request was initiated.
     */
    public function updateIngredientsSafely(Order $order, Builder $ingredientsVersion): void
    {
        
        // Fetch ingredients that are used in the ordered products
        $lockedIngredient = Ingredient::UsedForProducts($order->products->pluck('id')->toArray());

        // Verify that ingredient stock levels have not changed since the request started
        $IsIngredientsSafe = $lockedIngredient->orderBy('id')->get()->toArray() === $ingredientsVersion->orderBy('id')->get()->toArray();
        
        // If changed, throw a DataRaceException to prevent inconsistencies.
        throw_unless($IsIngredientsSafe,new DataRaceException('Ingredient stock levels have changed since the request started.'));

        // Pessimistic locking .. Lock the ingredients for update
        $lockedIngredient->lockForUpdate();

        // Update ingredients
        $this->updateIngredients($order);
        
    }

    /**
     * Updates the ingredient stock levels based on the ordered products.
     *
     * @param Order $order The order to process
     *
     * @return void
     */
    public function updateIngredients(Order $order): void {
        $products = $order->products;
        foreach ($products as $product) {
            foreach ($product->ingredients as $ingredient) {

                $ingredient->amount_in_stock -= $ingredient->pivot->amount * $product->pivot->quantity;
                $ingredient->save();

            }
        }
    }
}