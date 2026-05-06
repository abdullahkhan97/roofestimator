@extends('layouts.app', ['title' => 'Estimate #' . $estimate->id, 'pageTitle' => 'Estimate #' . $estimate->id])

@section('content')
    <div class="grid">
        <section class="span-12 panel" style="padding: 22px 28px;">
            <div class="actions" style="justify-content: space-between; align-items: center;">
                <div>
                    <h1>{{ $estimate->job_name ?: 'Roof estimate' }}</h1>
                    <p class="muted" style="margin-top: 4px;">
                        {{ $estimate->customer_name ?: 'No customer entered' }}{{ $estimate->project_address ? ' · '.$estimate->project_address : '' }}
                    </p>
                </div>
                <div class="actions no-print">
                    <a class="button" href="{{ route('estimates.pdf', $estimate) }}"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>Open PDF</a>
                    <a class="button secondary" href="{{ route('estimates.create') }}">New estimate</a>
                </div>
            </div>
        </section>

        <section class="span-3 kpi">
            <div class="label">Direct job cost</div>
            <div class="value">${{ number_format($estimate->direct_job_cost, 2) }}</div>
        </section>
        <section class="span-3 kpi">
            <div class="label">Recommended sell</div>
            <div class="value">${{ number_format($estimate->recommended_sell, 2) }}</div>
        </section>
        <section class="span-3 kpi">
            <div class="label">Gross profit</div>
            <div class="value">${{ number_format($estimate->gross_profit, 2) }}</div>
        </section>
        <section class="span-3 kpi">
            <div class="label">Gross margin</div>
            <div class="value">{{ number_format($estimate->margin_percent, 2) }}%</div>
        </section>

        <section class="span-8 panel">
            <h2>Estimate controls</h2>
            <table>
                <tr><th>Roof system</th><td>{{ $estimate->roofType->name }}</td></tr>
                <tr><th>Pitch</th><td>{{ $estimate->roofPitch->label }}</td></tr>
                <tr><th>Complexity</th><td>{{ $estimate->roofComplexity->label }}</td></tr>
                <tr><th>Labor difficulty factor</th><td>{{ number_format($estimate->calculation_snapshot['labor_difficulty_factor'] ?? 1, 4) }}x</td></tr>
                <tr><th>Adjusted roof area</th><td>{{ number_format($estimate->calculation_snapshot['adjusted_area_squares'] ?? $estimate->roof_area_squares, 2) }} SQ</td></tr>
                <tr><th>Sales tax on materials</th><td>{{ number_format($estimate->calculation_snapshot['sales_tax_percent'] ?? 0, 2) }}%</td></tr>
            </table>
        </section>

        <aside class="span-4 panel">
            <h2>Review flags</h2>
            @foreach(($estimate->review_flags ?: ['OK']) as $flag)
                <p class="muted" style="padding: 8px 0; border-bottom: 1px solid var(--line); font-size: 13px;">{{ $flag }}</p>
            @endforeach
        </aside>

        <section class="span-12 panel">
            <h2>Category summary</h2>
            <table>
                <thead>
                    <tr>
                        <th>Section</th>
                        <th>Direct cost</th>
                        <th>Recommended sell</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($estimate->category_totals ?? [] as $category => $totals)
                    <tr>
                        <td>{{ $category }}</td>
                        <td>${{ number_format($totals['direct_cost'], 2) }}</td>
                        <td>${{ number_format($totals['recommended_sell'], 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>

        <section class="span-12 panel">
            <h2>Detailed estimate</h2>
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Line item</th>
                        <th>Unit</th>
                        <th>Qty</th>
                        <th>Material</th>
                        <th>Labor</th>
                        <th>Direct</th>
                        <th>Sell</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($estimate->line_items ?? [] as $line)
                    @continue(($line['quantity'] ?? 0) <= 0 && ($line['direct_cost'] ?? 0) <= 0)
                    <tr>
                        <td style="color: var(--muted); font-size: 12px; font-weight: 500;">{{ $line['code'] }}</td>
                        <td style="font-weight: 500;">{{ $line['line_item'] }}</td>
                        <td style="color: var(--muted);">{{ $line['unit'] }}</td>
                        <td>{{ number_format($line['quantity'], 2) }}</td>
                        <td>${{ number_format($line['material_cost'], 2) }}</td>
                        <td>${{ number_format($line['labor_cost'], 2) }}</td>
                        <td>${{ number_format($line['direct_cost'], 2) }}</td>
                        <td style="font-weight: 600;">${{ number_format($line['recommended_sell'], 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>
    </div>
@endsection
