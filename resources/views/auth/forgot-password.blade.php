<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - MyBlog</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --text: #f1f5f9;
            --link: #60a5fa;
            --border: rgba(148, 163, 184, .28);
            --field-bg: rgba(15, 23, 42, .55);
        }

        html, body { min-height: 100%; }
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: var(--text);
            background-color: #0b1030;
            background-image:
                linear-gradient(rgba(2, 6, 23, .45), rgba(2, 6, 23, .75)),
                url('{{ asset('images/bg.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            -webkit-font-smoothing: antialiased;
        }
        a { color: inherit; text-decoration: none; }

        .page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            align-items: center;
            gap: 48px;
            max-width: 1280px;
            margin: 0 auto;
            padding: 40px 32px;
        }

        /* ===== Left hero ===== */
        .brand { display: flex; align-items: center; gap: 14px; }
        .brand svg { width: 64px; height: 64px; }
        .brand-name { font-size: 56px; font-weight: 800; letter-spacing: -1px; line-height: 1; }
        .tag { margin: 12px 0 0 4px; font-size: 16px; letter-spacing: .06em; color: #cbd5e1; }
        .tag i { font-style: normal; margin: 0 12px; }
        .grad-text {
            background: linear-gradient(90deg, #60a5fa, #a78bfa);
            -webkit-background-clip: text; background-clip: text;
            color: transparent;
        }
        .headline { margin-top: 48px; font-size: 52px; font-weight: 800; line-height: 1.15; }
        .lead { margin-top: 22px; max-width: 440px; font-size: 17px; line-height: 1.6; color: #e2e8f0; }

        .steps { margin-top: 40px; display: flex; flex-direction: column; gap: 20px; }
        .step { display: flex; align-items: center; gap: 16px; }
        .step-icon {
            width: 52px; height: 52px; flex: none; border-radius: 14px;
            display: grid; place-items: center;
            border: 1px solid rgba(129, 140, 248, .6);
            background: rgba(30, 27, 75, .55);
            color: #c7d2fe;
        }
        .step-icon svg { width: 24px; height: 24px; }
        .step strong { display: block; font-size: 16px; }
        .step span { font-size: 14px; color: #cbd5e1; }

        /* ===== Card ===== */
        .card {
            width: 100%; max-width: 560px; justify-self: end;
            padding: 36px 40px 30px;
            border-radius: 22px;
            border: 1px solid rgba(148, 163, 184, .25);
            background: rgba(15, 23, 42, .62);
            -webkit-backdrop-filter: blur(18px); backdrop-filter: blur(18px);
            box-shadow: 0 24px 60px rgba(0, 0, 0, .45);
            text-align: center;
        }
        .card-logo svg { width: 44px; height: 44px; }
        .card h1 { margin-top: 6px; font-size: 32px; font-weight: 800; }
        .card .sub { margin: 14px auto 0; max-width: 380px; font-size: 16px; line-height: 1.5; color: #e2e8f0; }

        form { margin-top: 28px; text-align: left; }

        .field { position: relative; margin-bottom: 16px; }
        .field > svg.ico {
            position: absolute; left: 18px; top: 50%; transform: translateY(-50%);
            width: 20px; height: 20px; color: #cbd5e1; pointer-events: none;
        }
        .field input {
            width: 100%; height: 58px;
            padding: 0 18px 0 54px;
            font: inherit; font-size: 16px; color: var(--text);
            background: var(--field-bg);
            border: 1px solid var(--border);
            border-radius: 12px; outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .field input::placeholder { color: #94a3b8; }
        .field input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, .25); }
        .field input:-webkit-autofill {
            -webkit-text-fill-color: var(--text);
            -webkit-box-shadow: 0 0 0 1000px #131c35 inset;
        }

        .error { margin: -8px 0 14px; font-size: 13px; color: #f87171; }
        .status {
            margin-bottom: 16px; padding: 10px 14px; border-radius: 10px; font-size: 14px; text-align: left;
            color: #bbf7d0; background: rgba(34, 197, 94, .12); border: 1px solid rgba(34, 197, 94, .3);
        }

        .btn {
            width: 100%; height: 58px; margin-top: 4px;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            font: inherit; font-size: 17px; font-weight: 600; color: #fff;
            background: linear-gradient(90deg, #2563eb, #8b5cf6);
            border: 0; border-radius: 12px; cursor: pointer;
            box-shadow: 0 10px 24px rgba(79, 70, 229, .35);
            transition: transform .15s, filter .15s;
        }
        .btn:hover { filter: brightness(1.08); transform: translateY(-1px); }
        .btn svg { width: 20px; height: 20px; }

        .back { margin-top: 26px; text-align: center; font-size: 15px; color: #e2e8f0; }
        .back a { color: var(--link); font-weight: 600; }
        .back a:hover { text-decoration: underline; }

        /* ===== Responsive ===== */
        @media (max-width: 1024px) {
            .page { grid-template-columns: 1fr; gap: 32px; padding: 28px 20px; }
            .hero { text-align: center; }
            .brand { justify-content: center; }
            .tag { margin-left: 0; }
            .lead, .steps { display: none; }
            .headline { font-size: 30px; margin-top: 20px; }
            .brand-name { font-size: 42px; }
            .brand svg { width: 48px; height: 48px; }
            .card { justify-self: center; padding: 28px 22px 24px; }
        }
        @media (max-width: 420px) {
            .headline { display: none; }
            .card h1 { font-size: 26px; }
        }
    </style>
</head>
<body>

@php
    $feather = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <defs>
            <linearGradient id="fg__ID__" x1="8" y1="58" x2="56" y2="6" gradientUnits="userSpaceOnUse">
                <stop stop-color="#8b5cf6"/><stop offset="1" stop-color="#3b82f6"/>
            </linearGradient>
        </defs>
        <path d="M56 6C34 8 18 20 14 40l-6 16 2 2 14-8c18-2 28-16 32-44Z" fill="url(#fg__ID__)"/>
        <path d="M10 56 38 28" stroke="#0f172a" stroke-opacity=".35" stroke-width="2" stroke-linecap="round"/>
    </svg>';
@endphp

<main class="page">

    {{-- ================= LEFT: hero ================= --}}
    <section class="hero">
        <div class="brand">
            {!! str_replace('__ID__', 'a', $feather) !!}
            <div class="brand-name">My<span class="grad-text">Blog</span></div>
        </div>
        <div class="tag">Write<i>•</i>Share<i>•</i>Inspire</div>

        <h2 class="headline">
            Don't worry,<br>
            <span class="grad-text">we've got you</span>
        </h2>

        <p class="lead">Enter your email and we'll send you a link to reset your password and get back to writing.</p>

        <div class="steps">
            <div class="step">
                <div class="step-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                </div>
                <div>
                    <strong>Check your inbox</strong>
                    <span>We'll email you a secure reset link.</span>
                </div>
            </div>
            <div class="step">
                <div class="step-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg>
                </div>
                <div>
                    <strong>Choose a new password</strong>
                    <span>Pick something strong and memorable.</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= RIGHT: card ================= --}}
    <section class="card">
        <div class="card-logo">{!! str_replace('__ID__', 'b', $feather) !!}</div>
        <h1>Forgot Password? 🔑</h1>
        <p class="sub">No problem. Just let us know your email address and we'll email you a password reset link.</p>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            @if (session('status'))
                <div class="status">{{ session('status') }}</div>
            @endif

            <div class="field">
                <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Email address"
                       required autofocus autocomplete="username">
            </div>
            @error('email') <p class="error">{{ $message }}</p> @enderror

            <button type="submit" class="btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                Email Password Reset Link
            </button>
        </form>

        <p class="back">Remember your password? <a href="{{ route('login') }}">Back to login</a></p>
    </section>
</main>

</body>
</html>