<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Roof Estimator' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #1c1e25;
            --muted: #8a8f9d;
            --line: #e9e5df;
            --bg: #f4f2ee;
            --panel: #ffffff;
            --nav: #111318;
            --nav-hover: #1e2128;
            --accent: #1a6b5f;
            --accent-light: #e8f5f3;
            --accent-2: #1d4ed8;
            --soft: #fafaf8;
            --danger: #b91c1c;
            --danger-light: #fef2f2;
            --gold: #a07828;
            --success-bg: #f0fdf4;
            --success-border: #86efac;
            --success-text: #14532d;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Outfit', sans-serif;
            color: var(--ink);
            background: var(--bg);
            font-size: 14px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        a { color: inherit; text-decoration: none; }

        /* ── Layout Shell ── */
        .shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 240px 1fr;
        }

        /* ── Sidebar ── */
        .sidebar {
            background: var(--nav);
            color: #e5e7eb;
            padding: 28px 16px 24px;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .brand {
            display: block;
            font-family: 'Cormorant Garamond', serif;
            font-size: 17px;
            font-weight: 700;
            letter-spacing: 0.03em;
            color: #ffffff;
            line-height: 1.3;
            margin-bottom: 32px;
            padding: 0 8px;
        }

        .brand::after {
            content: '';
            display: block;
            width: 28px;
            height: 2px;
            background: var(--accent);
            margin-top: 10px;
        }

        .nav-link,
        .nav-button {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 8px;
            min-height: 38px;
            padding: 0 10px;
            border-radius: 5px;
            font-family: 'Outfit', sans-serif;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            color: #9ca3af;
            background: transparent;
            border: 0;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
        }

        .nav-link:hover,
        .nav-button:hover {
            background: var(--nav-hover);
            color: #ffffff;
        }

        .nav-section {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
        }

        .nav-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #4b5563;
            padding: 0 10px;
            margin-bottom: 6px;
        }

        .user-chip {
            margin-top: auto;
            padding-top: 24px;
            padding: 16px 10px 0;
            color: #6b7280;
            font-size: 12px;
            line-height: 1.5;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
            margin-top: 28px;
        }

        .user-chip strong {
            display: block;
            color: #9ca3af;
            font-weight: 500;
            margin-bottom: 2px;
        }

        .content { min-width: 0; }

        .topbar {
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            background: var(--panel);
            border-bottom: 1px solid var(--line);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .topbar h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 22px;
            font-weight: 600;
            letter-spacing: 0.01em;
            color: var(--ink);
        }

        .topbar-date {
            font-size: 12px;
            font-weight: 400;
            color: var(--muted);
            letter-spacing: 0.04em;
        }

        main {
            padding: 32px;
            max-width: 1320px;
        }

        /* ── Typography ── */
        h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 28px;
            font-weight: 600;
            line-height: 1.2;
            letter-spacing: 0.01em;
            margin-bottom: 8px;
        }

        h2 {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 18px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--line);
        }

        h3 {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        p { line-height: 1.65; }

        .muted { color: var(--muted); font-size: 13px; }

        /* ── Grid ── */
        .grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 20px;
        }

        .span-3  { grid-column: span 3; }
        .span-4  { grid-column: span 4; }
        .span-6  { grid-column: span 6; }
        .span-8  { grid-column: span 8; }
        .span-12 { grid-column: span 12; }

        /* ── Panel ── */
        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 24px 28px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        }

        /* ── Forms ── */
        label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 6px;
        }

        input,
        select {
            width: 100%;
            border: 1px solid #ddd9d3;
            border-radius: 6px;
            padding: 9px 12px;
            background: #fefefe;
            color: var(--ink);
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(26, 107, 95, 0.08);
        }

        input[type="checkbox"] {
            width: auto;
            accent-color: var(--accent);
        }

        .field { margin-bottom: 18px; }

        .error {
            color: var(--danger);
            font-size: 12px;
            font-weight: 500;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .error::before { content: '↑'; }

        /* ── Buttons ── */
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-height: 40px;
            padding: 0 20px;
            border-radius: 6px;
            font-family: 'Outfit', sans-serif;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.15s;
            border: 1.5px solid var(--accent);
            background: var(--accent);
            color: #ffffff;
        }

        .button:hover {
            background: #155f54;
            border-color: #155f54;
            box-shadow: 0 2px 8px rgba(26, 107, 95, 0.25);
        }

        .button.blue {
            border-color: var(--accent-2);
            background: var(--accent-2);
        }

        .button.blue:hover {
            background: #1e40af;
            border-color: #1e40af;
            box-shadow: 0 2px 8px rgba(29, 78, 216, 0.25);
        }

        .button.secondary {
            background: transparent;
            color: var(--ink);
            border-color: var(--line);
        }

        .button.secondary:hover {
            background: var(--bg);
            border-color: #c5bfb8;
            box-shadow: none;
        }

        .button.danger {
            background: transparent;
            color: var(--danger);
            border-color: #fca5a5;
        }

        .button.danger:hover {
            background: var(--danger-light);
            box-shadow: none;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        /* ── Tables ── */
        table { width: 100%; border-collapse: collapse; }

        th {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            background: transparent;
            padding: 10px 14px;
            border-bottom: 1.5px solid var(--line);
            text-align: left;
            vertical-align: middle;
        }

        td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--line);
            text-align: left;
            vertical-align: middle;
            font-size: 13.5px;
            color: var(--ink);
        }

        tbody tr:last-child td { border-bottom: 0; }

        tbody tr:hover td { background: var(--soft); }

        /* ── KPI Cards ── */
        .kpi {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 20px 24px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        }

        .kpi::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--accent), #2d9e90);
        }

        .kpi .label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 10px;
        }

        .kpi .value {
            font-family: 'Cormorant Garamond', serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--ink);
            line-height: 1;
        }

        .total {
            font-family: 'Cormorant Garamond', serif;
            font-size: 38px;
            font-weight: 700;
        }

        /* ── Status / Alert ── */
        .status {
            background: var(--success-bg);
            border: 1px solid var(--success-border);
            color: var(--success-text);
            padding: 12px 16px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 500;
        }

        /* ── Login Page ── */
        .login-page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 420px;
        }

        .login-hero {
            background: linear-gradient(145deg, #111318 55%, #1a3a35);
            color: #fff;
            display: flex;
            align-items: flex-end;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }

        .login-hero::before {
            content: '';
            position: absolute;
            top: -120px; right: -120px;
            width: 500px; height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(26,107,95,0.25) 0%, transparent 70%);
        }

        .login-hero::after {
            content: '';
            position: absolute;
            bottom: 60px; left: 60px;
            width: 80px; height: 2px;
            background: var(--accent);
        }

        .login-hero-content { position: relative; z-index: 1; }

        .login-hero .eyebrow {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: #6ee7b7;
            margin-bottom: 20px;
        }

        .login-hero h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 42px;
            font-weight: 600;
            max-width: 580px;
            line-height: 1.15;
            color: #ffffff;
            margin-bottom: 20px;
        }

        .login-hero .sub {
            max-width: 520px;
            color: #a7c4be;
            font-size: 14px;
            line-height: 1.7;
        }

        .login-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 44px;
            background: #ffffff;
        }

        .login-card { width: 100%; }

        .login-card h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 30px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .login-card .muted { margin-bottom: 28px; }

        @media (max-width: 1024px) {
            .shell { grid-template-columns: 1fr; }
            .sidebar { position: static; height: auto; flex-direction: row; flex-wrap: wrap; gap: 8px; }
            .brand { margin-bottom: 0; }
            .login-page { grid-template-columns: 1fr; }
            .login-hero { min-height: 220px; padding: 32px 36px; align-items: flex-start; }
            .login-hero h1 { font-size: 28px; }
        }

        @media (max-width: 768px) {
            .span-3, .span-4, .span-6, .span-8 { grid-column: span 12; }
            main { padding: 20px; }
            .topbar { padding: 0 20px; }
            .panel { padding: 18px 20px; }
        }

        @media print {
            .sidebar, .topbar, .no-print { display: none !important; }
            .shell { display: block; }
            body { background: #fff; }
            main { padding: 0; max-width: none; }
            .panel { border: 0; box-shadow: none; padding: 0; border-radius: 0; }
            .kpi { border: 1px solid #ddd; }
        }
    </style>
</head>
<body>
@auth
    <div class="shell">
        <aside class="sidebar">
            <a class="brand" href="{{ route('estimates.create') }}">Steep Roof Estimator</a>
            <a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-chart-line"></i>Dashboard</a>
            <a class="nav-link" href="{{ route('estimates.create') }}"><i class="fas fa-file-invoice-dollar"></i></i>New estimate</a>
            @if(auth()->user()->isAdmin())
                <div class="nav-section" >
                    <div class="nav-label">Admin</div>
                    <a class="nav-link" href="{{ route('admin.pricing') }}"><i class="fas fa-calculator"></i></i>Pricing variables</a>
                    <a class="nav-link" href="{{ route('admin.users.index') }}"><i class="fas fa-users-gear"></i>User management</a>
                </div>
            @endif
            <div class="nav-section">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="nav-button" type="submit"><i class="fas fa-right-from-bracket"></i>Logout</button>
                </form>
            </div>
            <div class="user-chip">
                <strong><i class="fas fa-user-tie"></i>&nbsp&nbsp&nbsp{{ auth()->user()->name }}</strong>
                <!-- {{ auth()->user()->getRoleNames()->map(fn ($role) => ucfirst($role))->implode(', ') ?: 'User' }} -->
            </div>
        </aside>
        <div class="content">
            <div class="topbar">
                <h1>{{ $pageTitle ?? 'Estimator' }}</h1>    
                <!-- <span class="topbar-date muted">{{ now()->format('M j, Y') }}</span> -->
            </div>
            <main>@yield('content')</main>
        </div>
    </div>
@else
    @yield('content')
@endauth
</body>
</html>
