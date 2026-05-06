<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Estimate extends Model
{
    protected $fillable = [
        'customer_name',
        'job_name',
        'customer_email',
        'customer_phone',
        'project_address',
        'roof_type_id',
        'roof_pitch_id',
        'roof_complexity_id',
        'roof_area_squares',
        'waste_percent',
        'tear_off_layers',
        'stories',
        'addon_quantities',
        'input_snapshot',
        'calculation_snapshot',
        'line_items',
        'category_totals',
        'review_flags',
        'direct_job_cost',
        'recommended_sell',
        'gross_profit',
        'material_cost',
        'addon_cost',
        'subtotal_cost',
        'margin_percent',
        'margin_amount',
        'total_price',
    ];

    protected $casts = [
        'addon_quantities' => 'array',
        'input_snapshot' => 'array',
        'calculation_snapshot' => 'array',
        'line_items' => 'array',
        'category_totals' => 'array',
        'review_flags' => 'array',
    ];

    public function roofType(): BelongsTo
    {
        return $this->belongsTo(RoofType::class);
    }

    public function roofPitch(): BelongsTo
    {
        return $this->belongsTo(RoofPitch::class);
    }

    public function roofComplexity(): BelongsTo
    {
        return $this->belongsTo(RoofComplexity::class);
    }
}
