<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Estimate #{{ $estimate->id }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1c1e25;
            background: #ffffff;
            line-height: 1.5;
        }

        .page { padding: 40px 44px 36px; }

        /* ── Header ── */
        .header {
            border-bottom: 2.5px solid #1a6b5f;
            padding-bottom: 18px;
            margin-bottom: 22px;
        }

        .header-left {
            display: inline-block;
            width: 60%;
            vertical-align: top;
        }

        .header-right {
            display: inline-block;
            width: 39%;
            vertical-align: top;
            text-align: right;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #111318;
        }

        .company-tagline {
            font-size: 8.5px;
            color: #8a8f9d;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-top: 3px;
        }

        .estimate-label {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #8a8f9d;
            margin-bottom: 3px;
        }

        .estimate-number {
            font-size: 19px;
            font-weight: bold;
            color: #1a6b5f;
        }

        .estimate-date {
            font-size: 8.5px;
            color: #8a8f9d;
            margin-top: 3px;
        }

        /* ── Info blocks ── */
        .info-row { margin-bottom: 18px; }

        .info-block {
            display: inline-block;
            vertical-align: top;
            width: 32.3%;
            background: #f9f8f6;
            border: 1px solid #e9e5df;
            border-radius: 5px;
            padding: 11px 13px;
            margin-right: 1%;
        }

        .info-block:last-child { margin-right: 0; }

        .info-block-title {
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #8a8f9d;
            margin-bottom: 7px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e9e5df;
        }

        .info-row-item { margin-bottom: 4px; }

        .info-label {
            font-size: 8.5px;
            color: #8a8f9d;
            display: inline-block;
            width: 42%;
        }

        .info-value {
            font-size: 8.5px;
            color: #1c1e25;
            font-weight: 500;
            display: inline-block;
            width: 57%;
        }

        /* ── KPI strip ── */
        .kpi-row {
            background: #111318;
            border-radius: 6px;
            padding: 13px 18px;
            margin-bottom: 20px;
        }

        .kpi-cell {
            display: inline-block;
            width: 24%;
            vertical-align: top;
            text-align: center;
        }

        .kpi-label {
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #9ca3af;
            margin-bottom: 4px;
        }

        .kpi-value {
            font-size: 14px;
            font-weight: bold;
            color: #ffffff;
        }

        .kpi-value.accent { color: #6ee7b7; }

        /* ── Flags ── */
        .flags {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 5px;
            padding: 9px 13px;
            margin-bottom: 16px;
        }

        .flags-title {
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #92400e;
            margin-bottom: 4px;
        }

        .flag-item { font-size: 9px; color: #78350f; margin-bottom: 2px; }

        /* ── Section heading ── */
        .section-heading {
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #8a8f9d;
            border-bottom: 1.5px solid #e9e5df;
            padding-bottom: 5px;
            margin-bottom: 8px;
            margin-top: 16px;
        }

        /* ── Tables ── */
        table { width: 100%; border-collapse: collapse; }

        thead tr { background: #f4f2ee; }

        th {
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #8a8f9d;
            padding: 6px 9px;
            text-align: left;
            border-bottom: 1.5px solid #e9e5df;
        }

        th.right, td.right { text-align: right; }

        td {
            font-size: 9px;
            color: #1c1e25;
            padding: 6px 9px;
            border-bottom: 1px solid #f0ece6;
            vertical-align: middle;
        }

        td.muted  { color: #8a8f9d; }
        td.bold   { font-weight: bold; }
        td.small  { font-size: 8px; }

        .category-row td {
            background: #f0fdf4;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #1a6b5f;
            padding: 5px 9px;
            border-bottom: 1px solid #d1fae5;
        }

        .totals-row td {
            background: #1a6b5f;
            color: #ffffff;
            font-weight: bold;
            font-size: 10px;
            padding: 8px 9px;
            border-bottom: 0;
        }

        .totals-row td.accent {
            color: #6ee7b7;
            font-size: 11px;
        }

        /* ── Footer ── */
        .footer {
            margin-top: 26px;
            padding-top: 10px;
            border-top: 1px solid #e9e5df;
            text-align: center;
            color: #8a8f9d;
            font-size: 7.5px;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- ── Header ── --}}
    <div class="header">
        <div class="header-left">
            <div class="company-name">Steep Roof Estimator</div>
            <div class="company-tagline">Residential Steep-Slope Roofing &amp; Estimates</div>
        </div>
        <div class="header-right">
            <div class="estimate-label">Estimate</div>
            <div class="estimate-number">#{{ str_pad($estimate->id, 5, '0', STR_PAD_LEFT) }}</div>
            <div class="estimate-date">{{ $estimate->created_at->format('F j, Y') }}</div>
        </div>
    </div>

    {{-- ── Info blocks ── --}}
    <div class="info-row">
        <div class="info-block">
            <div class="info-block-title">Customer</div>
            <div class="info-row-item">
                <span class="info-label">Name</span>
                <span class="info-value">{{ $estimate->customer_name ?: '—' }}</span>
            </div>
            <div class="info-row-item">
                <span class="info-label">Email</span>
                <span class="info-value">{{ $estimate->customer_email ?: '—' }}</span>
            </div>
            <div class="info-row-item">
                <span class="info-label">Phone</span>
                <span class="info-value">{{ $estimate->customer_phone ?: '—' }}</span>
            </div>
            <div class="info-row-item">
                <span class="info-label">Address</span>
                <span class="info-value">{{ $estimate->project_address ?: '—' }}</span>
            </div>
        </div>

        <div class="info-block">
            <div class="info-block-title">Job Details</div>
            <div class="info-row-item">
                <span class="info-label">Job name</span>
                <span class="info-value">{{ $estimate->job_name ?: '—' }}</span>
            </div>
            <div class="info-row-item">
                <span class="info-label">Roof system</span>
                <span class="info-value">{{ $estimate->roofType->name }}</span>
            </div>
            <div class="info-row-item">
                <span class="info-label">Pitch tier</span>
                <span class="info-value">{{ $estimate->roofPitch->label }}</span>
            </div>
            <div class="info-row-item">
                <span class="info-label">Complexity</span>
                <span class="info-value">{{ $estimate->roofComplexity->label }}</span>
            </div>
        </div>

        <div class="info-block">
            <div class="info-block-title">Measurements</div>
            <div class="info-row-item">
                <span class="info-label">Roof area</span>
                <span class="info-value">{{ number_format($estimate->roof_area_squares, 2) }} SQ</span>
            </div>
            <div class="info-row-item">
                <span class="info-label">Adjusted area</span>
                <span class="info-value">{{ number_format($estimate->calculation_snapshot['adjusted_area_squares'] ?? $estimate->roof_area_squares, 2) }} SQ</span>
            </div>
            <div class="info-row-item">
                <span class="info-label">Labor factor</span>
                <span class="info-value">{{ number_format($estimate->calculation_snapshot['labor_difficulty_factor'] ?? 1, 4) }}x</span>
            </div>
            <div class="info-row-item">
                <span class="info-label">Sales tax</span>
                <span class="info-value">{{ number_format($estimate->calculation_snapshot['sales_tax_percent'] ?? 0, 2) }}%</span>
            </div>
        </div>
    </div>

    {{-- ── Review flags ── --}}
    @php
        $flags = array_filter($estimate->review_flags ?? [], fn($f) => $f !== 'OK');
    @endphp
    @if(count($flags))
        <div class="flags">
            <div class="flags-title">Review Flags</div>
            @foreach($flags as $flag)
                <div class="flag-item">· {{ $flag }}</div>
            @endforeach
        </div>
    @endif

    {{-- ── Financial KPIs ── --}}
    <div class="kpi-row">
        <div class="kpi-cell">
            <div class="kpi-label">Direct job cost</div>
            <div class="kpi-value">${{ number_format($estimate->direct_job_cost, 2) }}</div>
        </div>
        <div class="kpi-cell">
            <div class="kpi-label">Recommended sell</div>
            <div class="kpi-value accent">${{ number_format($estimate->recommended_sell, 2) }}</div>
        </div>
        <div class="kpi-cell">
            <div class="kpi-label">Gross profit</div>
            <div class="kpi-value">${{ number_format($estimate->gross_profit, 2) }}</div>
        </div>
        <div class="kpi-cell">
            <div class="kpi-label">Gross margin</div>
            <div class="kpi-value">{{ number_format($estimate->margin_percent, 2) }}%</div>
        </div>
    </div>

    {{-- ── Category summary ── --}}
    @if(!empty($estimate->category_totals))
        <div class="section-heading">Category Summary</div>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th class="right">Direct cost</th>
                    <th class="right">Recommended sell</th>
                </tr>
            </thead>
            <tbody>
                @foreach($estimate->category_totals as $category => $totals)
                    <tr>
                        <td>{{ $category }}</td>
                        <td class="right">${{ number_format($totals['direct_cost'], 2) }}</td>
                        <td class="right bold">${{ number_format($totals['recommended_sell'], 2) }}</td>
                    </tr>
                @endforeach
                <tr class="totals-row">
                    <td colspan="2">Total</td>
                    <td class="right accent">${{ number_format($estimate->recommended_sell, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    {{-- ── Line items ── --}}
    @php
        $lineItems = collect($estimate->line_items ?? [])
            ->filter(fn($l) => ($l['quantity'] ?? 0) > 0 || ($l['direct_cost'] ?? 0) > 0);
    @endphp

    @if($lineItems->isNotEmpty())
        <div class="section-heading">Detailed Line Items</div>
        <table>
            <thead>
                <tr>
                    <th style="width:7%">Code</th>
                    <th style="width:34%">Description</th>
                    <th style="width:7%">Unit</th>
                    <th class="right" style="width:8%">Qty</th>
                    <th class="right" style="width:11%">Material</th>
                    <th class="right" style="width:11%">Labor</th>
                    <th class="right" style="width:11%">Direct</th>
                    <th class="right" style="width:11%">Sell</th>
                </tr>
            </thead>
            <tbody>
                @php $currentCategory = null; @endphp
                @foreach($lineItems as $line)
                    @php $cat = $line['category'] ?? null; @endphp
                    @if($cat && $cat !== $currentCategory)
                        <tr class="category-row">
                            <td colspan="8">{{ $cat }}</td>
                        </tr>
                        @php $currentCategory = $cat; @endphp
                    @endif
                    <tr>
                        <td class="muted small">{{ $line['code'] }}</td>
                        <td>{{ $line['line_item'] }}</td>
                        <td class="muted">{{ $line['unit'] }}</td>
                        <td class="right">{{ number_format($line['quantity'], 2) }}</td>
                        <td class="right">${{ number_format($line['material_cost'], 2) }}</td>
                        <td class="right">${{ number_format($line['labor_cost'], 2) }}</td>
                        <td class="right">${{ number_format($line['direct_cost'], 2) }}</td>
                        <td class="right bold">${{ number_format($line['recommended_sell'], 2) }}</td>
                    </tr>
                @endforeach
                <tr class="totals-row">
                    <td colspan="6">Total</td>
                    <td class="right">${{ number_format($estimate->direct_job_cost, 2) }}</td>
                    <td class="right accent">${{ number_format($estimate->recommended_sell, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @endif

</div>
</body>
</html>
