<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstimateAddon extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'unit',
        'unit_price',
        'active',
        'sort_order',
    ];
}
