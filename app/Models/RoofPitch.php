<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoofPitch extends Model
{
    protected $fillable = [
        'label',
        'multiplier',
        'active',
        'sort_order',
    ];
}
