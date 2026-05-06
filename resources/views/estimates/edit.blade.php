@extends('layouts.app', ['title' => 'Edit Estimate #' . $estimate->id, 'pageTitle' => 'Edit estimate #' . $estimate->id])

@php
    /*
     * Pre-fill priority: old() → input_snapshot → model field → 0
     * input_snapshot stores the original validated form data from store/update.
     */
    $snap = $estimate->input_snapshot ?? [];

    $val = function ($name, $default = 0) use ($snap, $estimate) {
        if (old($name) !== null) return old($name);
        if (isset($snap[$name]))  return $snap[$name];
        if (isset($estimate->$name)) return $estimate->$name;
        return $default;
    };

    $field = function ($name, $label, $unit = null, $default = 0) use ($val) {
        $html  = '<div class="span-3 field">';
        $html .= '<label for="' . $name . '">' . $label;
        $html .= $unit ? ' <span class="muted">(' . $unit . ')</span>' : '';
        $html .= '</label>';
        $html .= '<input id="' . $name . '" name="' . $name . '" type="number" min="0" step="0.01" value="' . e($val($name, $default)) . '">';
        $html .= '</div>';
        return $html;
    };
@endphp

@section('content')
    <form class="grid" method="POST" action="{{ route('estimates.update', $estimate) }}">
        @csrf
        @method('PUT')

        <section class="span-12" style="margin-bottom: 4px;">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                <div>
                    <h1>Edit estimate #{{ $estimate->id }}</h1>
                    <p class="muted">Recalculates all totals on save. Original created {{ $estimate->created_at->format('M j, Y') }}.</p>
                </div>
                <div class="actions no-print">
                    <a class="button secondary" href="{{ route('estimates.show', $estimate) }}">Cancel</a>
                </div>
            </div>
        </section>

        <section class="span-8 panel">
            <h2>Job Info</h2>
            <div class="grid">
                <div class="span-6 field">
                    <label for="job_name">Job name</label>
                    <input id="job_name" name="job_name" value="{{ $val('job_name', '') }}">
                </div>
                <div class="span-6 field">
                    <label for="customer_name">Customer</label>
                    <input id="customer_name" name="customer_name" value="{{ $val('customer_name', '') }}">
                </div>
                <div class="span-6 field">
                    <label for="customer_email">Customer email</label>
                    <input id="customer_email" name="customer_email" type="email" value="{{ $val('customer_email', '') }}">
                </div>
                <div class="span-6 field">
                    <label for="customer_phone">Customer phone</label>
                    <input id="customer_phone" name="customer_phone" value="{{ $val('customer_phone', '') }}">
                </div>
                <div class="span-12 field">
                    <label for="project_address">Project address</label>
                    <input id="project_address" name="project_address" value="{{ $val('project_address', '') }}">
                </div>
            </div>
        </section>

        <aside class="span-4 panel">
            <h2>Pricing Controls</h2>
            <div class="field">
                <label for="roof_type_id">Roof system</label>
                <select id="roof_type_id" name="roof_type_id" required>
                    @foreach($roofTypes as $roofType)
                        <option value="{{ $roofType->id }}"
                            @selected((old('roof_type_id') ?? $val('roof_type_id', $estimate->roof_type_id)) == $roofType->id)>
                            {{ $roofType->name }} - {{ number_format($roofType->margin_percent, 2) }}% margin
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="roof_pitch_id">Pitch tier</label>
                <select id="roof_pitch_id" name="roof_pitch_id" required>
                    @foreach($pitches as $pitch)
                        <option value="{{ $pitch->id }}"
                            @selected((old('roof_pitch_id') ?? $val('roof_pitch_id', $estimate->roof_pitch_id)) == $pitch->id)>
                            {{ $pitch->label }} ({{ number_format($pitch->multiplier, 2) }}x)
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="roof_complexity_id">Complexity tier</label>
                <select id="roof_complexity_id" name="roof_complexity_id" required>
                    @foreach($complexities as $complexity)
                        <option value="{{ $complexity->id }}"
                            @selected((old('roof_complexity_id') ?? $val('roof_complexity_id', $estimate->roof_complexity_id)) == $complexity->id)>
                            {{ $complexity->label }} ({{ number_format($complexity->multiplier, 2) }}x)
                        </option>
                    @endforeach
                </select>
            </div>
            {!! $field('waste_percent', 'Waste', '%', 10) !!}
            {!! $field('sales_tax_percent', 'Sales tax on materials', '%', 7) !!}
            {!! $field('contingency_percent', 'Contingency', '%', 0) !!}
        </aside>

        <section class="span-12 panel">
            <h2>Measurements &amp; Quantities</h2>
            <div class="grid">
                {!! $field('roof_area_squares', 'Roof area', 'Squares', 14) !!}
                {!! $field('tear_off_squares', 'Tear-off', 'Squares', 14) !!}
                {!! $field('decking_sheets', 'Decking replacement', 'Sheets', 15) !!}
                {!! $field('eaves_lf', 'Eaves', 'LF', 92) !!}
                {!! $field('rakes_lf', 'Rakes', 'LF', 80) !!}
                {!! $field('valleys_lf', 'Valleys', 'LF', 28) !!}
                {!! $field('hips_ridges_lf', 'Hips &amp; ridges', 'LF', 58) !!}
                {!! $field('ridge_vent_replace_lf', 'Ridge vent replace', 'LF', 58) !!}
                {!! $field('ridge_vent_cut_in_lf', 'Ridge vent cut-in', 'LF', 58) !!}
                {!! $field('drip_edge_lf', 'Drip edge', 'LF', 172) !!}
                {!! $field('step_flashing_lf', 'Step flashing', 'LF', 0) !!}
                {!! $field('sidewall_flashing_lf', 'Sidewall flashing', 'LF', 0) !!}
                {!! $field('endwall_flashing_lf', 'Endwall flashing', 'LF', 0) !!}
                {!! $field('transition_metal_lf', 'Transition metal', 'LF', 0) !!}
                {!! $field('vented_closures_lf', 'Vented closures - metal', 'LF', 0) !!}
                {!! $field('non_vented_closures_lf', 'Non-vented closures - metal', 'LF', 0) !!}
                {!! $field('pipe_boots_shingle_qty', 'Pipe boots - shingle', 'Qty', 3) !!}
                {!! $field('pipe_boots_metal_qty', 'Pipe boots - metal', 'Qty', 0) !!}
                {!! $field('gutters_lf', 'Gutters', 'LF', 0) !!}
                {!! $field('downspouts_lf', 'Downspouts', 'LF', 0) !!}
                {!! $field('snow_guards_qty', 'Snow guards', 'Qty', 0) !!}
                {!! $field('permit_misc_allowance', 'Permit / dump / misc', '$', 400) !!}
                {!! $field('skylight_flash_kits', 'Skylight flash kits', 'EA', 0) !!}
            </div>
        </section>

        <section class="span-12 panel">
            <h2>Fasteners &amp; Misc Adders</h2>
            <div class="grid">
                {!! $field('butyl_tape_rolls', 'Butyl tape', 'Rolls', 0) !!}
                {!! $field('sealant_tubes', 'Sealant / caulk', 'Tubes', 0) !!}
                {!! $field('misc_shingle_accessories', 'Misc shingle accessories', '$', 0) !!}
                {!! $field('misc_metal_accessories', 'Misc metal accessories', '$', 0) !!}
            </div>
        </section>

        @if($errors->any())
            <section class="span-12 panel" style="border-color: #fca5a5; background: #fef2f2;">
                <h2 style="color: #b91c1c; border-bottom-color: #fca5a5;">Check these fields</h2>
                @foreach($errors->all() as $error)
                    <div class="error">{{ $error }}</div>
                @endforeach
            </section>
        @endif

        <section class="span-12 actions">
            <button class="button blue" type="submit">Save &amp; recalculate</button>
            <a class="button secondary" href="{{ route('estimates.show', $estimate) }}">Cancel</a>
        </section>
    </form>
@endsection
