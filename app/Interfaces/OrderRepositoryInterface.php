<?php

namespace App\Interfaces;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;

interface OrderRepositoryInterface 
{
    /**
     * Creates a new order and associates it with the specified products and quantities
     *
     * @param array $products An associative array of product IDs and their corresponding quantities
     * @return Order The newly created order
     */
    public function createWithProducts(array $products);

    /**
     * Get the updated_at timestamps of ingredients used by the given products.
     *
     * @param array $products An array containing product data with 'product_id' as key or index.
     * @return \Illuminate\Database\Eloquent\Builder ingredients used by the products.
     */
    public function getIngredients(array $products);

    public function updateIngredientsSafely(Order $order, Builder $ingredientsVersion);


}