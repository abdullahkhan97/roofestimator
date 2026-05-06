<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoofType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'base_price_per_square',
        'margin_percent',
        'active',
        'sort_order',
    ];
}
