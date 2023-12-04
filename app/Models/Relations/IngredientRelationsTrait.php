<?php

namespace App\Models\Relations;

use App\Models\Merchant;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait IngredientRelationsTrait
{
    /** 
     * Retrieves the products associated.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot(['quantity'])
            ->withTimestamps();
    }

    /**
     * Retrieves the merchant associated with the ingredient
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
