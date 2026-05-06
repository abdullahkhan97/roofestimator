@extends('layouts.app', ['title' => 'Pricing Variables', 'pageTitle' => 'Pricing variables'])

@section('content')
    <form class="grid" method="POST" action="{{ route('admin.pricing.update') }}">
        @csrf
        @method('PUT')

        <section class="span-12">
            <h1>Rates and Multipliers</h1>
            <p class="muted">Edit the same variables used by the workbook Lists and Rates tabs. Material tax is applied only to material rates; pitch and complexity apply only to labor.</p>
        </section>

        @if(session('status'))
            <section class="span-12 status">{{ session('status') }}</section>
        @endif

        <section class="span-12 panel">
            <h2>Roof systems and target gross margin</h2>
            <table>
                <thead><tr><th>System</th><th>Margin %</th><th>Active</th></tr></thead>
                <tbody>
                @foreach($roofTypes as $roofType)
                    <tr>
                        <td>{{ $roofType->name }}</td>
                        <td><input name="roof_types[{{ $roofType->id }}][margin_percent]" type="number" min="0" max="95" step="0.01" value="{{ old('roof_types.' . $roofType->id . '.margin_percent', $roofType->margin_percent) }}"><input name="roof_types[{{ $roofType->id }}][base_price_per_square]" type="hidden" value="{{ $roofType->base_price_per_square }}"></td>
                        <td><input name="roof_types[{{ $roofType->id }}][active]" type="checkbox" value="1" @checked($roofType->active)></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>

        <section class="span-6 panel">
            <h2>Pitch tiers</h2>
            <table>
                <thead><tr><th>Pitch</th><th>Multiplier</th><th>Active</th></tr></thead>
                <tbody>
                @foreach($pitches as $pitch)
                    <tr>
                        <td>{{ $pitch->label }}</td>
                        <td><input name="pitches[{{ $pitch->id }}][multiplier]" type="number" min="0" step="0.0001" value="{{ old('pitches.' . $pitch->id . '.multiplier', $pitch->multiplier) }}"></td>
                        <td><input name="pitches[{{ $pitch->id }}][active]" type="checkbox" value="1" @checked($pitch->active)></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>

        <section class="span-6 panel">
            <h2>Complexity tiers</h2>
            <table>
                <thead><tr><th>Complexity</th><th>Multiplier</th><th>Active</th></tr></thead>
                <tbody>
                @foreach($complexities as $complexity)
                    <tr>
                        <td>{{ $complexity->label }}</td>
                        <td><input name="complexities[{{ $complexity->id }}][multiplier]" type="number" min="0" step="0.0001" value="{{ old('complexities.' . $complexity->id . '.multiplier', $complexity->multiplier) }}"></td>
                        <td><input name="complexities[{{ $complexity->id }}][active]" type="checkbox" value="1" @checked($complexity->active)></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>

        <section class="span-12 panel">
            <h2>Estimate rate lines</h2>
            <table>
                <thead><tr><th>Code</th><th>Category</th><th>Line item</th><th>Unit</th><th>Material $ / unit</th><th>Labor $ / unit</th><th>Active</th></tr></thead>
                <tbody>
                @foreach($rateLines as $line)
                    <tr>
                        <td>{{ $line->code }}</td>
                        <td>{{ $line->category }}</td>
                        <td>{{ $line->line_item }}</td>
                        <td>{{ $line->unit }}</td>
                        <td><input name="rate_lines[{{ $line->id }}][material_rate]" type="number" min="0" step="0.01" value="{{ old('rate_lines.' . $line->id . '.material_rate', $line->material_rate) }}"></td>
                        <td><input name="rate_lines[{{ $line->id }}][labor_rate]" type="number" min="0" step="0.01" value="{{ old('rate_lines.' . $line->id . '.labor_rate', $line->labor_rate) }}"></td>
                        <td><input name="rate_lines[{{ $line->id }}][active]" type="checkbox" value="1" @checked($line->active)></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>

        <section class="span-12 actions">
            <button class="button blue" type="submit">Save variables</button>
        </section>
    </form>
@endsection
