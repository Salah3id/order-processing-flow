<?php

namespace App\Models;

use App\Events\OrderCreated;
use App\Models\Relations\OrderRelationsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, OrderRelationsTrait;
}
