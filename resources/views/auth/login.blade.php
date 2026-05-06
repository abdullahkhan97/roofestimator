@extends('layouts.app', ['title' => 'Estimator Login'])

@section('content')
    <div class="login-page">
        <section class="login-hero">
            <div class="login-hero-content">
                <p class="eyebrow">Residential steep-slope estimating</p>
                <h1>Workbook pricing, controlled from a secure Laravel backend.</h1>
                <p class="sub">Your team can input roof measurements, compare roof systems, generate estimates, and keep pricing rates current from admin tools.</p>
            </div>
        </section>
        <section class="login-panel">
            <div class="login-card">
                <h1>Sign in</h1>
                <p class="muted">Enter your account credentials to open the estimator.</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
                        @error('email') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div class="field">
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" required>
                        @error('password') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div class="field" style="margin-bottom: 24px;">
                        <label style="text-transform: none; letter-spacing: 0; font-size: 13px; font-weight: 400; color: var(--muted); display: flex; align-items: center; gap: 8px;">
                            <input name="remember" type="checkbox" value="1"> Remember this device
                        </label>
                    </div>
                    <button class="button" type="submit" style="width: 100%; min-height: 44px;">Log in</button>
                </form>
            </div>
        </section>
    </div>
@endsection
