<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstimateRateLine extends Model
{
    protected $fillable = [
        'code',
        'category',
        'line_item',
        'unit',
        'material_rate',
        'labor_rate',
        'notes',
        'active',
        'sort_order',
    ];
}
