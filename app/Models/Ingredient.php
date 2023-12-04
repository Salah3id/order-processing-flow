<?php

namespace App\Models;

use App\Models\Relations\IngredientRelationsTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory, IngredientRelationsTrait;

    protected $fillable = [
        'name',
        'merchant_id',
        'initial_amount',
        'amount_in_stock',
        'notified_amount',
        'low_amount_notified_at',
    ];

    /**
     * Scope a query to only include ingredients used for specified products.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $productIds
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsedForProducts($query, array $productIds) : Builder
    {
        return $query->whereHas('products', function ($query) use ($productIds) {
            $query->whereIn('products.id', $productIds);
        });
    }


    /**
     * Filters the query to include only ingredients that have a stock level below their notified amount
     * and have not yet been notified about their low stock level
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereLowAmountDetected($query) : Builder
    {
        return $query->whereColumn('amount_in_stock', '<', 'notified_amount')
        ->whereNull('low_amount_notified_at');
    }
}
