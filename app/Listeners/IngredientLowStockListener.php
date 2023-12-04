<?php

namespace App\Listeners;

use App\Events\IngredientLowStock;
use App\Notifications\IngredientLowAmountDetected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Throwable;

class IngredientLowStockListener implements ShouldQueue
{

    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param IngredientLowStock $event
     * @return void
     */
    public function handle(IngredientLowStock $event)
    {
        $ingredient = $event->ingredient;
        $ingredient->merchant->notify(new IngredientLowAmountDetected($ingredient));
        $ingredient->low_amount_notified_at = now();
        $ingredient->save();
    }


    /**
     * Handle a job failure.
     */
    public function failed(IngredientLowStock $event, Throwable $exception): void
    {
        // ...To do
    }

    public function shouldQueue(IngredientLowStock $event): bool
    {
        $ingredient = $event->ingredient->refresh();

        return $ingredient->low_amount_notified_at === null;
    }
}
