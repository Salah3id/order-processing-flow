<?php

namespace App\Models\Relations;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait ProductRelationsTrait
{
    /** 
     * Retrieves the ingredients associated with the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class)
            ->withPivot(['amount'])
            ->withTimestamps();
    }
}
