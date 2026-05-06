<?php

namespace App\Services;

use App\Models\EstimateRateLine;
use App\Models\RoofComplexity;
use App\Models\RoofPitch;
use App\Models\RoofType;
use Illuminate\Support\Collection;

class RoofEstimateCalculator
{
    public function calculate(array $input, RoofType $roofType, RoofPitch $pitch, RoofComplexity $complexity): array
    {
        $rates = EstimateRateLine::where('active', true)
            ->orderBy('sort_order')
            ->get()
            ->keyBy('code');

        $numbers = $this->normalizeInput($input);
        $laborFactor = (float) $pitch->multiplier * (float) $complexity->multiplier;
        $margin = (float) $roofType->margin_percent / 100;
        $salesTax = $numbers['sales_tax_percent'] / 100;
        $contingency = $numbers['contingency_percent'] / 100;
        $system = $roofType->name;
        $adjustedArea = $numbers['roof_area_squares'] * (1 + ($numbers['waste_percent'] / 100));

        $quantityByCode = [
            'DEMO_TO' => $numbers['tear_off_squares'] > 0 ? $numbers['tear_off_squares'] * (1 + ($numbers['waste_percent'] / 100)) : 0,
            'DECK_OSB' => $numbers['decking_sheets'],
            'UNDER_SYN' => $numbers['roof_area_squares'] > 0 ? $adjustedArea : 0,
            'ICE_WATER' => round($numbers['eaves_lf'] * 3 / 100, 2),
            'SHINGLE_ARCH' => $this->isShingle($system) ? $adjustedArea : 0,
            'METAL_SCREW' => $this->isScrewDownMetal($system) ? $adjustedArea : 0,
            'METAL_STANDING' => $this->isStandingSeam($system) ? $adjustedArea : 0,
            'STARTER' => $this->isShingle($system) ? ceil(($numbers['eaves_lf'] + $numbers['rakes_lf']) / 141) : 0,
            'RIDGE_CAP_SH' => $this->isShingle($system) ? ceil($numbers['hips_ridges_lf'] / 31) : 0,
            'RIDGE_CAP_MT' => $this->isMetal($system) ? ceil($numbers['hips_ridges_lf'] / 10) : 0,
            'RIDGE_VENT' => ceil($numbers['ridge_vent_replace_lf'] / 4),
            'RIDGE_VENT_CUT' => ceil($numbers['ridge_vent_cut_in_lf'] / 4),
            'DRIP_EDGE' => ceil($numbers['drip_edge_lf'] / 10),
            'VALLEY_METAL' => $numbers['valleys_lf'],
            'STEP_FLASH' => $this->isShingle($system) ? $numbers['step_flashing_lf'] : 0,
            'SIDEWALL' => $numbers['sidewall_flashing_lf'],
            'ENDWALL' => $numbers['endwall_flashing_lf'],
            'TRANSITION_METAL' => ceil($numbers['transition_metal_lf'] / 10),
            'CLOSURE_VENTED' => $this->isMetal($system) ? ceil($numbers['vented_closures_lf'] / 3) : 0,
            'CLOSURE_NONVENT' => $this->isMetal($system) ? ceil($numbers['non_vented_closures_lf'] / 3) : 0,
            'RAKE_TRIM_MT' => $this->isMetal($system) ? ceil($numbers['rakes_lf'] / 10) : 0,
            'SKYLIGHT_FLASH' => $numbers['skylight_flash_kits'],
            'SNOW_GUARD' => $this->isMetal($system) ? $numbers['snow_guards_qty'] : 0,
            'GUTTER_5K' => $numbers['gutters_lf'],
            'DOWNSPOUT' => $numbers['downspouts_lf'],
            'MISC_ALLOW' => $numbers['permit_misc_allowance'],
            'COIL_NAILS' => $this->isShingle($system) ? ceil($adjustedArea / 18) : 0,
            'WASHER_SCREWS' => $this->isScrewDownMetal($system) ? ceil($adjustedArea / 3) : 0,
            'BATTEN_1X4' => $this->isScrewDownMetal($system) ? ceil($adjustedArea * 7) : 0,
            'PIPE_BOOT_SH' => $this->isShingle($system) ? $numbers['pipe_boots_shingle_qty'] : 0,
            'PIPE_BOOT_MT' => $this->isMetal($system) ? $numbers['pipe_boots_metal_qty'] : 0,
            'BUTYL_TAPE' => $this->isMetal($system) ? $numbers['butyl_tape_rolls'] : 0,
            'SEALANT_CAULK' => $numbers['sealant_tubes'],
            'MISC_SHINGLE' => $this->isShingle($system) ? $numbers['misc_shingle_accessories'] : 0,
            'MISC_METAL' => $this->isMetal($system) ? $numbers['misc_metal_accessories'] : 0,
        ];

        $lineItems = [];
        $categoryTotals = [];
        $directTotal = 0;
        $sellTotal = 0;
        $materialTotal = 0;
        $laborTotal = 0;

        foreach ($rates as $rate) {
            $quantity = (float) ($quantityByCode[$rate->code] ?? 0);
            $material = (float) $rate->material_rate;
            $labor = (float) $rate->labor_rate;
            $isAllowance = in_array($rate->code, ['MISC_ALLOW', 'MISC_SHINGLE', 'MISC_METAL'], true);

            $materialCost = $isAllowance ? $quantity : $quantity * $material * (1 + $salesTax);
            $laborCost = $isAllowance ? 0 : $quantity * $labor * $laborFactor;
            $direct = $isAllowance ? $quantity : $materialCost + $laborCost;
            $sell = $direct <= 0 ? 0 : $direct * (1 + $contingency) / max(0.01, 1 - $margin);

            $line = [
                'code' => $rate->code,
                'category' => $rate->category,
                'line_item' => $rate->line_item,
                'unit' => $rate->unit,
                'quantity' => round($quantity, 2),
                'material_rate' => $material,
                'labor_rate' => $labor,
                'material_cost' => round($materialCost, 2),
                'labor_cost' => round($laborCost, 2),
                'direct_cost' => round($direct, 2),
                'recommended_sell' => round($sell, 2),
                'notes' => $rate->notes,
            ];

            $lineItems[] = $line;
            $directTotal += $direct;
            $sellTotal += $sell;
            $materialTotal += $materialCost;
            $laborTotal += $laborCost;

            $categoryTotals[$rate->category] ??= ['direct_cost' => 0, 'recommended_sell' => 0];
            $categoryTotals[$rate->category]['direct_cost'] += $direct;
            $categoryTotals[$rate->category]['recommended_sell'] += $sell;
        }

        foreach ($categoryTotals as $category => $totals) {
            $categoryTotals[$category] = [
                'direct_cost' => round($totals['direct_cost'], 2),
                'recommended_sell' => round($totals['recommended_sell'], 2),
            ];
        }

        $reviewFlags = $this->reviewFlags($system, $quantityByCode);
        $grossProfit = $sellTotal - $directTotal;

        return [
            'input_snapshot' => $numbers,
            'line_items' => $lineItems,
            'category_totals' => $categoryTotals,
            'review_flags' => $reviewFlags,
            'direct_job_cost' => round($directTotal, 2),
            'recommended_sell' => round($sellTotal, 2),
            'gross_profit' => round($grossProfit, 2),
            'material_cost' => round($materialTotal, 2),
            'addon_cost' => 0,
            'subtotal_cost' => round($directTotal, 2),
            'margin_percent' => round($margin * 100, 2),
            'margin_amount' => round($grossProfit, 2),
            'total_price' => round($sellTotal, 2),
            'calculation_snapshot' => [
                'roof_system' => $system,
                'roof_type' => $roofType->only(['id', 'name', 'margin_percent']),
                'pitch' => $pitch->only(['id', 'label', 'multiplier']),
                'complexity' => $complexity->only(['id', 'label', 'multiplier']),
                'labor_difficulty_factor' => round($laborFactor, 4),
                'sales_tax_percent' => $numbers['sales_tax_percent'],
                'contingency_percent' => $numbers['contingency_percent'],
                'adjusted_area_squares' => round($adjustedArea, 2),
            ],
        ];
    }

    private function normalizeInput(array $input): array
    {
        $defaults = [
            'roof_area_squares' => 0,
            'tear_off_squares' => 0,
            'decking_sheets' => 0,
            'eaves_lf' => 0,
            'rakes_lf' => 0,
            'valleys_lf' => 0,
            'hips_ridges_lf' => 0,
            'ridge_vent_replace_lf' => 0,
            'ridge_vent_cut_in_lf' => 0,
            'drip_edge_lf' => 0,
            'step_flashing_lf' => 0,
            'sidewall_flashing_lf' => 0,
            'endwall_flashing_lf' => 0,
            'transition_metal_lf' => 0,
            'vented_closures_lf' => 0,
            'non_vented_closures_lf' => 0,
            'pipe_boots_shingle_qty' => 0,
            'pipe_boots_metal_qty' => 0,
            'gutters_lf' => 0,
            'downspouts_lf' => 0,
            'snow_guards_qty' => 0,
            'permit_misc_allowance' => 0,
            'waste_percent' => 10,
            'sales_tax_percent' => 7,
            'contingency_percent' => 0,
            'butyl_tape_rolls' => 0,
            'sealant_tubes' => 0,
            'misc_shingle_accessories' => 0,
            'misc_metal_accessories' => 0,
            'skylight_flash_kits' => 0,
        ];

        $numbers = [];
        foreach ($defaults as $key => $default) {
            $numbers[$key] = (float) ($input[$key] ?? $default);
        }

        return $numbers;
    }

    private function reviewFlags(string $system, array $quantities): array
    {
        $flags = [];

        if ($this->isMetal($system) && (($quantities['CLOSURE_VENTED'] ?? 0) + ($quantities['CLOSURE_NONVENT'] ?? 0)) <= 0) {
            $flags[] = 'Metal roof selected but closures are zero.';
        }

        if ($this->isMetal($system) && ($quantities['TRANSITION_METAL'] ?? 0) <= 0) {
            $flags[] = 'Metal roof selected but transition metal is zero.';
        }

        if ($this->isScrewDownMetal($system) && ($quantities['BATTEN_1X4'] ?? 0) <= 0) {
            $flags[] = 'Screw-down metal selected but battens are zero.';
        }

        return $flags ?: ['OK'];
    }

    private function isShingle(string $system): bool
    {
        return $system === 'Architectural Shingle' || str_contains($system, 'Shingle');
    }

    private function isScrewDownMetal(string $system): bool
    {
        return $system === 'Metal Screw-Down' || str_contains($system, 'Screw');
    }

    private function isStandingSeam(string $system): bool
    {
        return $system === 'Standing Seam' || str_contains($system, 'Standing');
    }

    private function isMetal(string $system): bool
    {
        return $this->isScrewDownMetal($system) || $this->isStandingSeam($system);
    }
}
