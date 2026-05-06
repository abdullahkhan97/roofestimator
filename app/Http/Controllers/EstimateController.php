<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use App\Models\EstimateAddon;
use App\Models\RoofComplexity;
use App\Models\RoofPitch;
use App\Models\RoofType;
use App\Services\EstimatePdfRenderer;
use App\Services\RoofEstimateCalculator;
use Illuminate\Http\Request;

class EstimateController extends Controller
{
    public function index(Request $request)
    {
        // ── Allowed sort columns ──────────────────────────────────────────
        $sortable = [
            'id', 'job_name', 'customer_name', 'roof_area_squares',
            'direct_job_cost', 'recommended_sell', 'margin_percent', 'created_at',
        ];

        $sortBy  = in_array($request->input('sort'), $sortable, true)
            ? $request->input('sort')
            : 'created_at';

        $sortDir = $request->input('dir') === 'asc' ? 'asc' : 'desc';

        // ── Base query ────────────────────────────────────────────────────
        $query = Estimate::with(['roofType', 'roofPitch', 'roofComplexity']);

        // ── Search (job name, customer name, address, email) ──────────────
        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('job_name',        'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email','like', "%{$search}%")
                  ->orWhere('project_address','like', "%{$search}%");
            });
        }

        // ── Filter: roof type ─────────────────────────────────────────────
        if ($roofTypeId = $request->input('roof_type')) {
            $query->where('roof_type_id', $roofTypeId);
        }

        // ── Filter: date range ────────────────────────────────────────────
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // ── Filter: minimum margin ────────────────────────────────────────
        if ($request->filled('min_margin')) {
            $query->where('margin_percent', '>=', (float) $request->input('min_margin'));
        }

        // ── Sort & paginate (preserve all query params) ───────────────────
        $estimates = $query
            ->orderBy($sortBy, $sortDir)
            ->paginate(20)
            ->withQueryString();

        // ── Stats (always over full dataset, not filtered) ────────────────
        $stats = [
            'total'      => Estimate::count(),
            'total_sell' => Estimate::sum('recommended_sell'),
            'avg_margin' => Estimate::avg('margin_percent'),
            'this_month' => Estimate::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->count(),
        ];

        // ── Roof types for filter dropdown ────────────────────────────────
        $roofTypes = RoofType::where('active', true)->orderBy('sort_order')->get();

        return view('estimates.dashboard', compact(
            'estimates', 'stats', 'roofTypes', 'sortBy', 'sortDir'
        ));
    }

    public function create()
    {
        return view('estimates.create', $this->formData());
    }

    public function store(Request $request, RoofEstimateCalculator $calculator)
    {
        $validated = $request->validate([
            'customer_name'            => ['nullable', 'string', 'max:255'],
            'job_name'                 => ['nullable', 'string', 'max:255'],
            'customer_email'           => ['nullable', 'email', 'max:255'],
            'customer_phone'           => ['nullable', 'string', 'max:50'],
            'project_address'          => ['nullable', 'string', 'max:255'],
            'roof_type_id'             => ['required', 'exists:roof_types,id'],
            'roof_pitch_id'            => ['required', 'exists:roof_pitches,id'],
            'roof_complexity_id'       => ['required', 'exists:roof_complexities,id'],
            'roof_area_squares'        => ['required', 'numeric', 'min:1', 'max:100000'],
            'tear_off_squares'         => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'decking_sheets'           => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'eaves_lf'                 => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'rakes_lf'                 => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'valleys_lf'               => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'hips_ridges_lf'           => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'ridge_vent_replace_lf'    => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'ridge_vent_cut_in_lf'     => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'drip_edge_lf'             => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'step_flashing_lf'         => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'sidewall_flashing_lf'     => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'endwall_flashing_lf'      => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'transition_metal_lf'      => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'vented_closures_lf'       => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'non_vented_closures_lf'   => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'pipe_boots_shingle_qty'   => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'pipe_boots_metal_qty'     => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'gutters_lf'               => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'downspouts_lf'            => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'snow_guards_qty'          => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'permit_misc_allowance'    => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'waste_percent'            => ['required', 'numeric', 'min:0', 'max:100'],
            'sales_tax_percent'        => ['required', 'numeric', 'min:0', 'max:100'],
            'contingency_percent'      => ['required', 'numeric', 'min:0', 'max:100'],
            'butyl_tape_rolls'         => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'sealant_tubes'            => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'misc_shingle_accessories' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'misc_metal_accessories'   => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'skylight_flash_kits'      => ['nullable', 'numeric', 'min:0', 'max:100000'],
        ]);

        $roofType   = RoofType::where('active', true)->findOrFail($validated['roof_type_id']);
        $pitch      = RoofPitch::where('active', true)->findOrFail($validated['roof_pitch_id']);
        $complexity = RoofComplexity::where('active', true)->findOrFail($validated['roof_complexity_id']);
        $totals     = $calculator->calculate($validated, $roofType, $pitch, $complexity);

        $estimate = Estimate::create(array_merge($validated, $totals, [
            'roof_area_squares' => $validated['roof_area_squares'],
            'tear_off_layers'   => 0,
            'stories'           => 1,
            'addon_quantities'  => [],
        ]));

        return redirect()->route('estimates.show', $estimate);
    }

    public function show(Estimate $estimate)
    {
        $estimate->load(['roofType', 'roofPitch', 'roofComplexity']);

        return view('estimates.show', compact('estimate'));
    }

    public function edit(Estimate $estimate)
    {
        $estimate->load(['roofType', 'roofPitch', 'roofComplexity']);

        return view('estimates.edit', array_merge(
            $this->formData(),
            compact('estimate')
        ));
    }

    public function update(Request $request, Estimate $estimate, RoofEstimateCalculator $calculator)
    {
        $validated = $request->validate([
            'customer_name'            => ['nullable', 'string', 'max:255'],
            'job_name'                 => ['nullable', 'string', 'max:255'],
            'customer_email'           => ['nullable', 'email', 'max:255'],
            'customer_phone'           => ['nullable', 'string', 'max:50'],
            'project_address'          => ['nullable', 'string', 'max:255'],
            'roof_type_id'             => ['required', 'exists:roof_types,id'],
            'roof_pitch_id'            => ['required', 'exists:roof_pitches,id'],
            'roof_complexity_id'       => ['required', 'exists:roof_complexities,id'],
            'roof_area_squares'        => ['required', 'numeric', 'min:1', 'max:100000'],
            'tear_off_squares'         => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'decking_sheets'           => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'eaves_lf'                 => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'rakes_lf'                 => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'valleys_lf'               => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'hips_ridges_lf'           => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'ridge_vent_replace_lf'    => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'ridge_vent_cut_in_lf'     => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'drip_edge_lf'             => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'step_flashing_lf'         => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'sidewall_flashing_lf'     => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'endwall_flashing_lf'      => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'transition_metal_lf'      => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'vented_closures_lf'       => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'non_vented_closures_lf'   => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'pipe_boots_shingle_qty'   => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'pipe_boots_metal_qty'     => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'gutters_lf'               => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'downspouts_lf'            => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'snow_guards_qty'          => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'permit_misc_allowance'    => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'waste_percent'            => ['required', 'numeric', 'min:0', 'max:100'],
            'sales_tax_percent'        => ['required', 'numeric', 'min:0', 'max:100'],
            'contingency_percent'      => ['required', 'numeric', 'min:0', 'max:100'],
            'butyl_tape_rolls'         => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'sealant_tubes'            => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'misc_shingle_accessories' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'misc_metal_accessories'   => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'skylight_flash_kits'      => ['nullable', 'numeric', 'min:0', 'max:100000'],
        ]);

        $roofType   = RoofType::where('active', true)->findOrFail($validated['roof_type_id']);
        $pitch      = RoofPitch::where('active', true)->findOrFail($validated['roof_pitch_id']);
        $complexity = RoofComplexity::where('active', true)->findOrFail($validated['roof_complexity_id']);
        $totals     = $calculator->calculate($validated, $roofType, $pitch, $complexity);

        $estimate->update(array_merge($validated, $totals));

        return redirect()->route('estimates.show', $estimate)
            ->with('status', 'Estimate #' . $estimate->id . ' updated successfully.');
    }

    public function destroy(Estimate $estimate)
    {
        $id = $estimate->id;
        $estimate->delete();

        return redirect()->route('dashboard')
            ->with('status', 'Estimate #' . $id . ' has been deleted.');
    }

    public function pdf(Estimate $estimate, EstimatePdfRenderer $pdfRenderer)
    {
        $estimate->load(['roofType', 'roofPitch', 'roofComplexity']);

        return response($pdfRenderer->render($estimate))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="estimate-' . $estimate->id . '.pdf"');
    }

    private function formData(): array
    {
        return [
            'roofTypes'    => RoofType::where('active', true)->orderBy('sort_order')->get(),
            'pitches'      => RoofPitch::where('active', true)->orderBy('sort_order')->get(),
            'complexities' => RoofComplexity::where('active', true)->orderBy('sort_order')->get(),
            'addons'       => EstimateAddon::where('active', true)->orderBy('sort_order')->get(),
        ];
    }
}
