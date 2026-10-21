<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - MyBlog</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --text: #f1f5f9;
            --muted: #94a3b8;
            --accent: #818cf8;
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
        .grad-text {
            background: linear-gradient(90deg, #60a5fa, #a78bfa);
            -webkit-background-clip: text; background-clip: text;
            color: transparent;
        }
        .headline { margin-top: 36px; font-size: 44px; font-weight: 800; line-height: 1.2; }
        .lead { margin-top: 22px; max-width: 420px; font-size: 17px; line-height: 1.6; color: #e2e8f0; }

        .features { margin-top: 48px; display: flex; gap: 36px; flex-wrap: wrap; }
        .feature { max-width: 170px; }
        .feature-icon {
            width: 52px; height: 52px; border-radius: 12px;
            display: grid; place-items: center;
            border: 1px solid rgba(129, 140, 248, .6);
            background: rgba(30, 27, 75, .55);
            color: #c7d2fe;
        }
        .feature-icon svg { width: 24px; height: 24px; }
        .feature h3 { margin-top: 14px; font-size: 16px; font-weight: 700; }
        .feature p { margin-top: 6px; font-size: 14px; line-height: 1.45; color: #cbd5e1; }

        /* ===== Login card ===== */
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
        .card h1 { margin-top: 6px; font-size: 34px; font-weight: 800; }
        .card .sub { margin: 14px auto 0; max-width: 340px; font-size: 16px; line-height: 1.5; color: #e2e8f0; }

        form { margin-top: 28px; text-align: left; }

        .field { position: relative; margin-bottom: 16px; }
        .field > svg.ico {
            position: absolute; left: 18px; top: 50%; transform: translateY(-50%);
            width: 20px; height: 20px; color: #cbd5e1; pointer-events: none;
        }
        .field input {
            width: 100%; height: 58px;
            padding: 0 52px 0 54px;
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
        .toggle-pw {
            position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
            width: 40px; height: 40px; display: grid; place-items: center;
            background: none; border: 0; border-radius: 8px; cursor: pointer; color: #cbd5e1;
        }
        .toggle-pw:hover { color: #fff; background: rgba(255, 255, 255, .06); }
        .toggle-pw svg { width: 20px; height: 20px; }

        .error { margin: -8px 0 14px; font-size: 13px; color: #f87171; }
        .status {
            margin-bottom: 16px; padding: 10px 14px; border-radius: 10px; font-size: 14px;
            color: #bbf7d0; background: rgba(34, 197, 94, .12); border: 1px solid rgba(34, 197, 94, .3);
        }

        .row { display: flex; justify-content: space-between; align-items: center; margin: 4px 0 20px; font-size: 15px; }
        .remember { display: flex; align-items: center; gap: 12px; cursor: pointer; color: #e2e8f0; }
        .remember input { position: absolute; opacity: 0; pointer-events: none; }
        .box {
            width: 24px; height: 24px; border-radius: 6px;
            display: grid; place-items: center;
            background: transparent; border: 1px solid var(--border);
            transition: background .15s, border-color .15s;
        }
        .box svg { width: 14px; height: 14px; color: #fff; opacity: 0; }
        .remember input:checked + .box { background: #3b82f6; border-color: #3b82f6; }
        .remember input:checked + .box svg { opacity: 1; }
        .remember input:focus-visible + .box { box-shadow: 0 0 0 3px rgba(99, 102, 241, .35); }
        .forgot { color: var(--link); }
        .forgot:hover { text-decoration: underline; }

        .btn-login {
            width: 100%; height: 58px;
            display: flex; align-items: center; justify-content: center; gap: 12px;
            font: inherit; font-size: 17px; font-weight: 600; color: #fff;
            background: linear-gradient(90deg, #2563eb, #8b5cf6);
            border: 0; border-radius: 12px; cursor: pointer;
            box-shadow: 0 10px 24px rgba(79, 70, 229, .35);
            transition: transform .15s, filter .15s;
        }
        .btn-login:hover { filter: brightness(1.08); transform: translateY(-1px); }
        .btn-login svg { width: 20px; height: 20px; }

        .divider { display: flex; align-items: center; gap: 14px; margin: 22px 0; font-size: 14px; color: #cbd5e1; }
        .divider::before, .divider::after { content: ""; flex: 1; height: 1px; background: rgba(148, 163, 184, .3); }

        .btn-google {
            width: 100%; height: 58px;
            display: flex; align-items: center; justify-content: center; gap: 12px;
            font-size: 16px; font-weight: 600; color: #fff;
            background: rgba(15, 23, 42, .35);
            border: 1px solid rgba(99, 102, 241, .65);
            border-radius: 12px; transition: background .15s;
        }
        .btn-google:hover { background: rgba(99, 102, 241, .15); }
        .btn-google svg { width: 24px; height: 24px; }

        .signup { margin-top: 24px; text-align: center; font-size: 15px; color: #e2e8f0; }
        .signup a { color: var(--link); }
        .signup a:hover { text-decoration: underline; }

        /* ===== Responsive ===== */
        @media (max-width: 1024px) {
            .page { grid-template-columns: 1fr; gap: 32px; padding: 28px 20px; }
            .hero { text-align: center; }
            .brand { justify-content: center; }
            .lead, .features { display: none; }
            .headline { font-size: 30px; margin-top: 20px; }
            .brand-name { font-size: 42px; }
            .brand svg { width: 48px; height: 48px; }
            .card { justify-self: center; padding: 28px 22px 24px; }
        }
        @media (max-width: 420px) {
            .headline { display: none; }
            .card h1 { font-size: 28px; }
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

        <h2 class="headline">
            Share Your Stories,<br>
            <span class="grad-text">Inspire Others</span>
        </h2>

        <p class="lead">Write, share and grow your thoughts with a community that values your voice.</p>

        <div class="features">
            <div class="feature">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                </div>
                <h3>Easy to Write</h3>
                <p>Create beautiful posts in minutes.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <h3>Connect</h3>
                <p>Share ideas and get inspired.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m7 15 4-4 3 3 5-6"/></svg>
                </div>
                <h3>Grow</h3>
                <p>Build your audience and make an impact.</p>
            </div>
        </div>
    </section>

    {{-- ================= RIGHT: login card ================= --}}
    <section class="card">
        <div class="card-logo">{!! str_replace('__ID__', 'b', $feather) !!}</div>
        <h1>Welcome Back!</h1>
        <p class="sub">Log in to your account and continue your blogging journey.</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            @if (session('status'))
                <div class="status">{{ session('status') }}</div>
            @endif

            {{-- Email --}}
            <div class="field">
                <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Email address"
                       required autofocus autocomplete="username">
            </div>
            @error('email') <p class="error">{{ $message }}</p> @enderror

            {{-- Password --}}
            <div class="field">
                <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg>
                <input type="password" id="password" name="password" placeholder="Password"
                       required autocomplete="current-password">
                <button type="button" class="toggle-pw" id="togglePw" aria-label="Show password">
                    <svg id="eyeOff" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/><path d="m1 1 22 22"/></svg>
                    <svg id="eyeOn" style="display:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            @error('password') <p class="error">{{ $message }}</p> @enderror

            {{-- Remember + Forgot --}}
            <div class="row">
                <label class="remember">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span class="box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
                    </span>
                    Remember me
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot">Forgot password?</a>
                @endif
            </div>

            <button type="submit" class="btn-login">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="m10 17 5-5-5-5"/><path d="M15 12H3"/></svg>
                Login
            </button>
        </form>

        @if (Route::has('google.redirect'))
            <div class="divider">or continue with</div>

            <a href="{{ route('google.redirect') }}" class="btn-google">
                <svg viewBox="0 0 48 48" aria-hidden="true">
                    <path fill="#FFC107" d="M43.6 20.1H42V20H24v8h11.3C33.7 32.7 29.2 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.8 1.2 8 3l5.7-5.7C34 6.1 29.3 4 24 4 13 4 4 13 4 24s9 20 20 20 20-9 20-20c0-1.3-.1-2.7-.4-3.9z"/>
                    <path fill="#FF3D00" d="m6.3 14.7 6.6 4.8C14.7 15.1 19 12 24 12c3.1 0 5.8 1.2 8 3l5.7-5.7C34 6.1 29.3 4 24 4 16.3 4 9.7 8.3 6.3 14.7z"/>
                    <path fill="#4CAF50" d="M24 44c5.2 0 9.9-2 13.4-5.2l-6.2-5.2C29.2 35.1 26.7 36 24 36c-5.2 0-9.6-3.3-11.3-8l-6.5 5C9.5 39.6 16.2 44 24 44z"/>
                    <path fill="#1976D2" d="M43.6 20.1H42V20H24v8h11.3c-.8 2.2-2.2 4.2-4.1 5.6l6.2 5.2C37 39.2 44 34 44 24c0-1.3-.1-2.7-.4-3.9z"/>
                </svg>
                Google
            </a>
        @endif

        @if (Route::has('register'))
            <p class="signup">Don't have an account? <a href="{{ route('register') }}">Sign up</a></p>
        @endif
    </section>
</main>

<script>
    (function () {
        var input = document.getElementById('password');
        var btn   = document.getElementById('togglePw');
        var on    = document.getElementById('eyeOn');
        var off   = document.getElementById('eyeOff');
        btn.addEventListener('click', function () {
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            on.style.display  = show ? '' : 'none';
            off.style.display = show ? 'none' : '';
            btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });
    })();
</script>
</body>
</html>