<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoofComplexity extends Model
{
    protected $fillable = [
        'label',
        'multiplier',
        'active',
        'sort_order',
    ];
}
