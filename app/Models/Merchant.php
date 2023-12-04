<?php

namespace App\Models;

use App\Models\Relations\MerchantRelationsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Merchant extends Model
{
    use HasFactory, MerchantRelationsTrait, Notifiable;

    protected $fillable = [
        'name',
        'email',
    ];
}
