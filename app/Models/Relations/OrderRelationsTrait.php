<?php

namespace App\Models\Relations;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

trait OrderRelationsTrait
{
    /** 
     * Retrieves the products associated with the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot(['quantity'])
            ->withTimestamps();
    }

}
