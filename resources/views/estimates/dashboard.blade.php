@extends('layouts.app', ['title' => 'Dashboard', 'pageTitle' => 'Dashboard'])

@section('content')

{{-- ─────────────────────────────────────────────────────────────────────────
     Helper: builds a URL that toggles sort direction on the chosen column
     while keeping all current query params (search, filters, etc.)
────────────────────────────────────────────────────────────────────────── --}}
@php
    $sortUrl = function (string $col) use ($sortBy, $sortDir): string {
        $dir = ($sortBy === $col && $sortDir === 'asc') ? 'desc' : 'asc';
        return request()->fullUrlWithQuery(['sort' => $col, 'dir' => $dir, 'page' => 1]);
    };

    $sortIcon = function (string $col) use ($sortBy, $sortDir): string {
        if ($sortBy !== $col) {
            return '<svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="opacity:.3;flex-shrink:0;"><path d="M12 5v14M5 12l7-7 7 7"/></svg>';
        }
        if ($sortDir === 'asc') {
            return '<svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--accent);flex-shrink:0;"><path d="M12 19V5M5 12l7-7 7 7"/></svg>';
        }
        return '<svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--accent);flex-shrink:0;"><path d="M12 5v14M5 12l7 7 7-7"/></svg>';
    };

    $hasFilters = request()->hasAny(['search','roof_type','date_from','date_to','min_margin']);
@endphp

{{-- ── KPI row ── --}}
<div class="grid" style="margin-bottom:8px;">
    <div class="span-3 kpi">
        <div class="label">Total estimates</div>
        <div class="value">{{ number_format($stats['total']) }}</div>
    </div>
    <div class="span-3 kpi">
        <div class="label">This month</div>
        <div class="value">{{ number_format($stats['this_month']) }}</div>
    </div>
    <div class="span-3 kpi">
        <div class="label">Total recommended sell</div>
        <div class="value">${{ number_format($stats['total_sell'], 0) }}</div>
    </div>
    <div class="span-3 kpi">
        <div class="label">Avg gross margin</div>
        <div class="value">{{ number_format($stats['avg_margin'], 1) }}%</div>
    </div>
</div>

{{-- ── Flash status ── --}}
@if(session('status'))
    <div class="grid" style="margin-bottom:0;">
        <div class="span-12 status">{{ session('status') }}</div>
    </div>
@endif

{{-- ── Main panel ── --}}
<div class="grid">
<section class="span-12 panel" style="padding:0;overflow:hidden;">

    {{-- ══ Search & filter bar ══ --}}
    <form method="GET" action="{{ route('dashboard') }}" id="filter-form">
        {{-- Preserve active sort --}}
        @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
        @if(request('dir'))<input type="hidden" name="dir" value="{{ request('dir') }}">@endif

        {{-- Main filter row --}}
        <div style="display:flex;align-items:center;gap:8px;padding:14px 22px;border-bottom:1px solid var(--line);flex-wrap:wrap;">

            {{-- Search --}}
            <div style="position:relative;flex:1;min-width:200px;max-width:320px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                     style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted);pointer-events:none;">
                    <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                </svg>
                <input type="text" name="search" id="search-input"
                       value="{{ request('search') }}"
                       placeholder="Search job, customer, address…"
                       autocomplete="off"
                       style="padding-left:32px;height:36px;font-size:13px;">
            </div>

            {{-- Roof type filter --}}
            <select name="roof_type"
                    onchange="document.getElementById('filter-form').submit()"
                    style="height:36px;font-size:12px;min-width:150px;width:auto;color:{{ request('roof_type') ? 'var(--ink)' : 'var(--muted)' }};">
                <option value="" @selected(!request('roof_type'))>All roof types</option>
                @foreach($roofTypes as $rt)
                    <option value="{{ $rt->id }}" @selected(request('roof_type') == $rt->id)>{{ $rt->name }}</option>
                @endforeach
            </select>

            {{-- Min margin filter --}}
            <select name="min_margin"
                    onchange="document.getElementById('filter-form').submit()"
                    style="height:36px;font-size:12px;min-width:135px;width:auto;color:{{ (request('min_margin') !== null && request('min_margin') !== '') ? 'var(--ink)' : 'var(--muted)' }};">
                <option value="" @selected(request('min_margin') === null || request('min_margin') === '')>Any margin</option>
                <option value="10" @selected(request('min_margin') == '10')>Margin ≥ 10%</option>
                <option value="20" @selected(request('min_margin') == '20')>Margin ≥ 20%</option>
                <option value="30" @selected(request('min_margin') == '30')>Margin ≥ 30%</option>
                <option value="40" @selected(request('min_margin') == '40')>Margin ≥ 40%</option>
            </select>

            {{-- Date range toggle --}}
            <button type="button" id="date-toggle"
                    style="display:inline-flex;align-items:center;gap:5px;height:36px;padding:0 13px;border-radius:6px;
                           border:1px solid {{ request()->hasAny(['date_from','date_to']) ? 'var(--accent)' : 'var(--line)' }};
                           background:{{ request()->hasAny(['date_from','date_to']) ? 'var(--accent-light)' : '#fff' }};
                           color:{{ request()->hasAny(['date_from','date_to']) ? 'var(--accent)' : 'var(--muted)' }};
                           font-size:12px;font-weight:500;cursor:pointer;">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                Date range
            </button>

            {{-- Search submit --}}
            <button class="button" type="submit" style="height:36px;font-size:11px;min-height:auto;padding:0 16px;">Apply</button>

            {{-- Clear --}}
            @if($hasFilters)
                <a href="{{ route('dashboard') }}"
                   style="font-size:12px;color:var(--muted);text-decoration:none;white-space:nowrap;display:inline-flex;align-items:center;gap:4px;">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Clear all
                </a>
            @endif

            <div style="flex:1;"></div>

            <span style="font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;color:var(--muted);white-space:nowrap;">
                {{ $estimates->total() }} result{{ $estimates->total() !== 1 ? 's' : '' }}
            </span>

            <a class="button" href="{{ route('estimates.create') }}"
               style="height:36px;font-size:11px;min-height:auto;white-space:nowrap;">
                + New estimate
            </a>
        </div>

        {{-- Date range drawer --}}
        <div id="date-row"
             style="display:{{ request()->hasAny(['date_from','date_to']) ? 'flex' : 'none' }};
                    align-items:center;gap:10px;padding:10px 22px;
                    background:var(--soft);border-bottom:1px solid var(--line);flex-wrap:wrap;">
            <span style="font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);">From</span>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   style="height:34px;font-size:13px;width:auto;min-width:148px;">
            <span style="color:var(--muted);font-size:13px;">to</span>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   style="height:34px;font-size:13px;width:auto;min-width:148px;">
            <button class="button secondary" type="submit"
                    style="height:34px;font-size:11px;min-height:auto;">Apply dates</button>
            @if(request('date_from') || request('date_to'))
                <a href="{{ request()->fullUrlWithQuery(['date_from'=>null,'date_to'=>null,'page'=>1]) }}"
                   style="font-size:12px;color:var(--muted);text-decoration:none;">Clear dates</a>
            @endif
        </div>
    </form>

    {{-- ══ Active filter chips ══ --}}
    @if($hasFilters)
        <div style="display:flex;flex-wrap:wrap;gap:6px;align-items:center;padding:9px 22px;border-bottom:1px solid var(--line);background:var(--soft);">
            <span style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);">Filtered by:</span>

            @if(request('search'))
                <span style="display:inline-flex;align-items:center;gap:5px;background:#fff;border:1px solid var(--line);border-radius:20px;padding:2px 10px;font-size:12px;">
                    Search: <strong>{{ Str::limit(request('search'), 30) }}</strong>
                    <a href="{{ request()->fullUrlWithQuery(['search'=>null,'page'=>1]) }}" style="color:var(--muted);line-height:1;text-decoration:none;font-size:15px;margin-left:2px;">×</a>
                </span>
            @endif

            @if(request('roof_type'))
                @php $activeRt = $roofTypes->firstWhere('id', request('roof_type')); @endphp
                <span style="display:inline-flex;align-items:center;gap:5px;background:#fff;border:1px solid var(--line);border-radius:20px;padding:2px 10px;font-size:12px;">
                    Type: <strong>{{ $activeRt?->name }}</strong>
                    <a href="{{ request()->fullUrlWithQuery(['roof_type'=>null,'page'=>1]) }}" style="color:var(--muted);line-height:1;text-decoration:none;font-size:15px;margin-left:2px;">×</a>
                </span>
            @endif

            @if(request('min_margin') !== null && request('min_margin') !== '')
                <span style="display:inline-flex;align-items:center;gap:5px;background:#fff;border:1px solid var(--line);border-radius:20px;padding:2px 10px;font-size:12px;">
                    Margin ≥ <strong>{{ request('min_margin') }}%</strong>
                    <a href="{{ request()->fullUrlWithQuery(['min_margin'=>null,'page'=>1]) }}" style="color:var(--muted);line-height:1;text-decoration:none;font-size:15px;margin-left:2px;">×</a>
                </span>
            @endif

            @if(request('date_from'))
                <span style="display:inline-flex;align-items:center;gap:5px;background:#fff;border:1px solid var(--line);border-radius:20px;padding:2px 10px;font-size:12px;">
                    From: <strong>{{ \Carbon\Carbon::parse(request('date_from'))->format('M j, Y') }}</strong>
                    <a href="{{ request()->fullUrlWithQuery(['date_from'=>null,'page'=>1]) }}" style="color:var(--muted);line-height:1;text-decoration:none;font-size:15px;margin-left:2px;">×</a>
                </span>
            @endif

            @if(request('date_to'))
                <span style="display:inline-flex;align-items:center;gap:5px;background:#fff;border:1px solid var(--line);border-radius:20px;padding:2px 10px;font-size:12px;">
                    To: <strong>{{ \Carbon\Carbon::parse(request('date_to'))->format('M j, Y') }}</strong>
                    <a href="{{ request()->fullUrlWithQuery(['date_to'=>null,'page'=>1]) }}" style="color:var(--muted);line-height:1;text-decoration:none;font-size:15px;margin-left:2px;">×</a>
                </span>
            @endif
        </div>
    @endif

    {{-- ══ Table ══ --}}
    @if($estimates->isEmpty())
        <div style="padding:64px 28px;text-align:center;">
            @if($hasFilters)
                <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color:var(--muted);margin-bottom:14px;"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <p style="font-size:15px;color:var(--muted);margin-bottom:16px;">No estimates match your filters.</p>
                <a href="{{ route('dashboard') }}" class="button secondary">Clear filters</a>
            @else
                <p style="font-size:15px;color:var(--muted);margin-bottom:20px;">No estimates yet. Create your first one.</p>
                <a class="button" href="{{ route('estimates.create') }}">Create estimate</a>
            @endif
        </div>
    @else
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th style="padding-left:28px;white-space:nowrap;">
                            <a href="{{ $sortUrl('id') }}" style="display:inline-flex;align-items:center;gap:4px;color:inherit;text-decoration:none;">
                                # {!! $sortIcon('id') !!}
                            </a>
                        </th>
                        <th style="white-space:nowrap;min-width:130px;">
                            <a href="{{ $sortUrl('job_name') }}" style="display:inline-flex;align-items:center;gap:4px;color:inherit;text-decoration:none;">
                                Job {!! $sortIcon('job_name') !!}
                            </a>
                        </th>
                        <th style="white-space:nowrap;min-width:120px;">
                            <a href="{{ $sortUrl('customer_name') }}" style="display:inline-flex;align-items:center;gap:4px;color:inherit;text-decoration:none;">
                                Customer {!! $sortIcon('customer_name') !!}
                            </a>
                        </th>
                        <th style="white-space:nowrap;">Roof type</th>
                        <th style="white-space:nowrap;">
                            <a href="{{ $sortUrl('roof_area_squares') }}" style="display:inline-flex;align-items:center;gap:4px;color:inherit;text-decoration:none;">
                                Area (SQ) {!! $sortIcon('roof_area_squares') !!}
                            </a>
                        </th>
                        <th style="white-space:nowrap;">
                            <a href="{{ $sortUrl('direct_job_cost') }}" style="display:inline-flex;align-items:center;gap:4px;color:inherit;text-decoration:none;">
                                Direct cost {!! $sortIcon('direct_job_cost') !!}
                            </a>
                        </th>
                        <th style="white-space:nowrap;">
                            <a href="{{ $sortUrl('recommended_sell') }}" style="display:inline-flex;align-items:center;gap:4px;color:inherit;text-decoration:none;">
                                Sell price {!! $sortIcon('recommended_sell') !!}
                            </a>
                        </th>
                        <th style="white-space:nowrap;">
                            <a href="{{ $sortUrl('margin_percent') }}" style="display:inline-flex;align-items:center;gap:4px;color:inherit;text-decoration:none;">
                                Margin {!! $sortIcon('margin_percent') !!}
                            </a>
                        </th>
                        <th style="white-space:nowrap;">
                            <a href="{{ $sortUrl('created_at') }}" style="display:inline-flex;align-items:center;gap:4px;color:inherit;text-decoration:none;">
                                Date {!! $sortIcon('created_at') !!}
                            </a>
                        </th>
                        <th style="padding-right:28px;text-align:right;white-space:nowrap;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($estimates as $estimate)
                    <tr>
                        <td style="padding-left:28px;color:var(--muted);font-size:12px;font-weight:500;">#{{ $estimate->id }}</td>
                        <td>
                            <a href="{{ route('estimates.show', $estimate) }}"
                               style="font-weight:600;color:var(--ink);transition:color .15s;"
                               onmouseover="this.style.color='var(--accent)'"
                               onmouseout="this.style.color='var(--ink)'">
                                {{ $estimate->job_name ?: '—' }}
                            </a>
                        </td>
                        <td style="color:var(--muted);">{{ $estimate->customer_name ?: '—' }}</td>
                        <td>{{ $estimate->roofType->name }}</td>
                        <td>{{ number_format($estimate->roof_area_squares, 1) }}</td>
                        <td>${{ number_format($estimate->direct_job_cost, 0) }}</td>
                        <td style="font-weight:600;">${{ number_format($estimate->recommended_sell, 0) }}</td>
                        <td>
                            @php
                                $m = $estimate->margin_percent;
                                $mColor = $m >= 30 ? 'var(--accent)' : ($m >= 20 ? 'var(--ink)' : 'var(--danger)');
                            @endphp
                            <span style="font-size:12px;font-weight:600;color:{{ $mColor }};">
                                {{ number_format($m, 1) }}%
                            </span>
                        </td>
                        <td style="color:var(--muted);font-size:12px;white-space:nowrap;">
                            {{ $estimate->created_at->format('M j, Y') }}
                        </td>
                        <td style="padding-right:28px;text-align:right;white-space:nowrap;">
                            <div style="display:inline-flex;gap:5px;align-items:center;">

                                {{-- View --}}
                                <a href="{{ route('estimates.show', $estimate) }}" title="View estimate"
                                   style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;border:1px solid var(--line);background:#fff;color:var(--muted);transition:all .15s;text-decoration:none;"
                                   onmouseover="this.style.borderColor='var(--accent)';this.style.color='var(--accent)'"
                                   onmouseout="this.style.borderColor='var(--line)';this.style.color='var(--muted)'">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('estimates.edit', $estimate) }}" title="Edit estimate"
                                   style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;border:1px solid var(--line);background:#fff;color:var(--muted);transition:all .15s;text-decoration:none;"
                                   onmouseover="this.style.borderColor='var(--accent-2)';this.style.color='var(--accent-2)'"
                                   onmouseout="this.style.borderColor='var(--line)';this.style.color='var(--muted)'">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>

                                {{-- PDF --}}
                                <a href="{{ route('estimates.pdf', $estimate) }}" title="Download PDF"
                                   style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;border:1px solid var(--line);background:#fff;color:var(--muted);transition:all .15s;text-decoration:none;"
                                   onmouseover="this.style.borderColor='var(--gold)';this.style.color='var(--gold)'"
                                   onmouseout="this.style.borderColor='var(--line)';this.style.color='var(--muted)'">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                                </a>

                                {{-- Delete --}}
                                <form method="POST" action="{{ route('estimates.destroy', $estimate) }}"
                                      onsubmit="return confirm('Permanently delete estimate #{{ $estimate->id }}?\n\nThis cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete estimate"
                                            style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;border:1px solid var(--line);background:#fff;color:var(--muted);transition:all .15s;cursor:pointer;"
                                            onmouseover="this.style.borderColor='var(--danger)';this.style.color='var(--danger)'"
                                            onmouseout="this.style.borderColor='var(--line)';this.style.color='var(--muted)'">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- ══ Pagination ══ --}}
        @if($estimates->hasPages())
            <div style="padding:14px 28px;border-top:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                <span style="font-size:12px;color:var(--muted);">
                    Showing {{ $estimates->firstItem() }}–{{ $estimates->lastItem() }} of {{ $estimates->total() }}
                </span>
                <div style="display:flex;gap:5px;flex-wrap:wrap;">
                    {{-- Prev --}}
                    @if($estimates->onFirstPage())
                        <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;border:1px solid var(--line);color:var(--muted);opacity:.35;font-size:15px;">‹</span>
                    @else
                        <a href="{{ $estimates->previousPageUrl() }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;border:1px solid var(--line);color:var(--ink);text-decoration:none;font-size:15px;">‹</a>
                    @endif

                    {{-- Page numbers with ellipsis --}}
                    @php
                        $last    = $estimates->lastPage();
                        $current = $estimates->currentPage();
                        $prev    = null;
                        $pages   = collect(range(1, $last))
                            ->filter(fn($p) => $p === 1 || $p === $last || abs($p - $current) <= 2);
                    @endphp

                    @foreach($pages as $page)
                        @if($prev !== null && $page - $prev > 1)
                            <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;color:var(--muted);font-size:12px;">…</span>
                        @endif
                        @if($page === $current)
                            <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;border:1px solid var(--accent);background:var(--accent);color:#fff;font-size:12px;font-weight:600;">{{ $page }}</span>
                        @else
                            <a href="{{ $estimates->url($page) }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;border:1px solid var(--line);color:var(--ink);font-size:12px;text-decoration:none;">{{ $page }}</a>
                        @endif
                        @php $prev = $page; @endphp
                    @endforeach

                    {{-- Next --}}
                    @if($estimates->hasMorePages())
                        <a href="{{ $estimates->nextPageUrl() }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;border:1px solid var(--line);color:var(--ink);text-decoration:none;font-size:15px;">›</a>
                    @else
                        <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;border:1px solid var(--line);color:var(--muted);opacity:.35;font-size:15px;">›</span>
                    @endif
                </div>
            </div>
        @endif
    @endif

</section>
</div>

<script>
    // Toggle date drawer
    const toggle = document.getElementById('date-toggle');
    const dateRow = document.getElementById('date-row');
    if (toggle && dateRow) {
        toggle.addEventListener('click', function () {
            const open = dateRow.style.display !== 'none';
            dateRow.style.display   = open ? 'none' : 'flex';
            toggle.style.borderColor = open ? 'var(--line)' : 'var(--accent)';
            toggle.style.background  = open ? '#fff'        : 'var(--accent-light)';
            toggle.style.color       = open ? 'var(--muted)' : 'var(--accent)';
        });
    }

    // Debounced auto-submit on search input (500ms idle)
    let _debounce;
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(_debounce);
            _debounce = setTimeout(() => document.getElementById('filter-form').submit(), 500);
        });
    }
</script>
@endsection
