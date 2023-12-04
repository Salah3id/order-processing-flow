<?php

namespace App\Listeners;

use App\Events\IngredientLowStock;
use App\Events\OrderCreated;
use App\Models\Ingredient;
use App\Models\Order;

class OrderCreatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        $this->dispatchIngredientLowStockEvents($event->order);
    }


    /**
     * Dispatches `IngredientLowStock` events for ingredients that have a low stock level and haven't been notified about it.
     *
     * @param Order $order The order to process
     * @return void
     */
    private function dispatchIngredientLowStockEvents(Order $order): void
    {
        $productIds = $order->products->pluck('id')->toArray();
        Ingredient::usedForProducts($productIds)
        ->whereLowAmountDetected()
        ->get()
        ->each(function (Ingredient $ingredient) {
            event(new IngredientLowStock($ingredient));
        });
    }
}
