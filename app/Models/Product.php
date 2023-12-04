<?php

namespace App\Models;

use App\Models\Relations\ProductRelationsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, ProductRelationsTrait;
}
