<?php

namespace App\Http\Controllers;

use App\Models\EstimateAddon;
use App\Models\EstimateRateLine;
use App\Models\RoofComplexity;
use App\Models\RoofPitch;
use App\Models\RoofType;
use Illuminate\Http\Request;

class PricingAdminController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->can('manage pricing'), 403);

        return view('admin.pricing', [
            'roofTypes' => RoofType::orderBy('sort_order')->get(),
            'pitches' => RoofPitch::orderBy('sort_order')->get(),
            'complexities' => RoofComplexity::orderBy('sort_order')->get(),
            'addons' => EstimateAddon::orderBy('sort_order')->get(),
            'rateLines' => EstimateRateLine::orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request)
    {
        abort_unless($request->user()?->can('manage pricing'), 403);

        $validated = $request->validate([
            'roof_types' => ['array'],
            'roof_types.*.base_price_per_square' => ['required', 'numeric', 'min:0'],
            'roof_types.*.margin_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'roof_types.*.active' => ['nullable', 'boolean'],
            'pitches' => ['array'],
            'pitches.*.multiplier' => ['required', 'numeric', 'min:0'],
            'pitches.*.active' => ['nullable', 'boolean'],
            'complexities' => ['array'],
            'complexities.*.multiplier' => ['required', 'numeric', 'min:0'],
            'complexities.*.active' => ['nullable', 'boolean'],
            'addons' => ['array'],
            'addons.*.unit_price' => ['required', 'numeric', 'min:0'],
            'addons.*.active' => ['nullable', 'boolean'],
            'rate_lines' => ['array'],
            'rate_lines.*.material_rate' => ['required', 'numeric', 'min:0'],
            'rate_lines.*.labor_rate' => ['required', 'numeric', 'min:0'],
            'rate_lines.*.active' => ['nullable', 'boolean'],
        ]);

        foreach ($validated['roof_types'] ?? [] as $id => $values) {
            RoofType::whereKey($id)->update([
                'base_price_per_square' => $values['base_price_per_square'],
                'margin_percent' => $values['margin_percent'],
                'active' => (bool) ($values['active'] ?? false),
            ]);
        }

        foreach ($validated['pitches'] ?? [] as $id => $values) {
            RoofPitch::whereKey($id)->update([
                'multiplier' => $values['multiplier'],
                'active' => (bool) ($values['active'] ?? false),
            ]);
        }

        foreach ($validated['complexities'] ?? [] as $id => $values) {
            RoofComplexity::whereKey($id)->update([
                'multiplier' => $values['multiplier'],
                'active' => (bool) ($values['active'] ?? false),
            ]);
        }

        foreach ($validated['addons'] ?? [] as $id => $values) {
            EstimateAddon::whereKey($id)->update([
                'unit_price' => $values['unit_price'],
                'active' => (bool) ($values['active'] ?? false),
            ]);
        }

        foreach ($validated['rate_lines'] ?? [] as $id => $values) {
            EstimateRateLine::whereKey($id)->update([
                'material_rate' => $values['material_rate'],
                'labor_rate' => $values['labor_rate'],
                'active' => (bool) ($values['active'] ?? false),
            ]);
        }

        return redirect()->route('admin.pricing')->with('status', 'Pricing variables updated.');
    }
}
