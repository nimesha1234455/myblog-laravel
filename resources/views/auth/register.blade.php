<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - MyBlog</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --blue:#2563eb; --text:#111a33; --mute:#5b6784; --line:#e3e8f2; }
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Inter',system-ui,sans-serif; display:flex; min-height:100vh; color:var(--text); }

        .left {
            flex:0 0 46%; position:relative; overflow:hidden; color:#fff; padding:56px 5vw;
            background:
                linear-gradient(100deg, rgba(10,24,64,.92) 0%, rgba(15,40,100,.7) 55%, rgba(15,40,100,.3) 100%),
                url('/images/login-bg.jpg') center/cover no-repeat,
                linear-gradient(160deg,#10224f 0%,#27498f 60%,#9d7bb0 100%);
        }
        .left::after { content:""; position:absolute; left:-120px; bottom:-160px; width:420px; height:420px; border-radius:50%; background:rgba(37,99,235,.55); }
        .left > * { position:relative; z-index:1; }
        .brand { display:flex; align-items:center; gap:14px; font-size:34px; font-weight:800; }
        .grad { background:linear-gradient(90deg,#3b82f6,#a78bfa); -webkit-background-clip:text; background-clip:text; color:transparent; }
        .tag { margin:10px 0 0 4px; letter-spacing:.06em; color:#c7d2ec; }
        .tag i { font-style:normal; margin:0 12px; }
        h1 { font-size:48px; line-height:1.12; font-weight:800; margin:60px 0 20px; }
        .lead { max-width:400px; line-height:1.6; font-size:17px; color:#e5ebf8; margin-bottom:34px; }
        .perk { display:flex; align-items:center; gap:14px; margin-bottom:16px; font-size:16px; }
        .perk b { width:30px; height:30px; border-radius:50%; background:rgba(79,70,229,.45); display:grid; place-items:center; font-size:14px; flex:none; }

        .right { flex:1; display:flex; align-items:center; justify-content:center; padding:36px 5vw; background:#f6f8fd; position:relative; overflow:hidden; }
        .right::before { content:""; position:absolute; top:-140px; right:-120px; width:340px; height:340px; border-radius:50%; background:#e6ebfb; }
        .card { position:relative; width:100%; max-width:500px; background:#fff; border-radius:22px; padding:38px 40px 30px; box-shadow:0 20px 50px rgba(30,50,110,.1); }
        .card .logo { display:flex; justify-content:center; margin-bottom:10px; }
        .card h2 { text-align:center; font-size:30px; font-weight:800; }
        .sub { text-align:center; color:var(--mute); margin:8px 0 24px; line-height:1.5; }

        .field { position:relative; margin-bottom:14px; }
        .field .icon { position:absolute; left:16px; top:50%; transform:translateY(-50%); color:#3b4560; }
        .field input { width:100%; padding:16px 46px; font:inherit; font-size:16px; color:var(--text); border:1px solid var(--line); border-radius:12px; outline:none; background:#fff; }
        .field input::placeholder { color:#7a86a3; }
        .field input:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(37,99,235,.16); }
        .toggle { position:absolute; right:16px; top:50%; transform:translateY(-50%); background:none; border:0; cursor:pointer; color:#3b4560; }
        .error { color:#dc2626; font-size:13px; margin:-6px 0 10px; }

        a { color:var(--blue); text-decoration:none; font-weight:600; } a:hover { text-decoration:underline; }
        .btn { width:100%; margin-top:6px; padding:16px; border:0; border-radius:12px; color:#fff; font:inherit; font-weight:700; font-size:17px; cursor:pointer; background:linear-gradient(90deg,#2257e0,#7c5cf0); }
        .btn:hover { filter:brightness(1.06); }
        .or { display:flex; align-items:center; gap:14px; color:var(--mute); font-size:14px; margin:20px 0; }
        .or::before,.or::after { content:""; flex:1; height:1px; background:var(--line); }
        .google { display:flex; align-items:center; justify-content:center; gap:10px; padding:14px; border:1px solid var(--line); border-radius:12px; color:var(--text); font-weight:500; background:#fff; }
        .google:hover { background:#f8faff; text-decoration:none; }
        .signin { text-align:center; color:var(--mute); margin-top:22px; }

        @media (max-width:960px){
            body { flex-direction:column; }
            .left { flex:none; padding:28px 7vw 48px; }
            .brand { font-size:28px; } .brand svg { width:40px; height:40px; }
            .tag { font-size:14px; margin-top:6px; }
            h1 { font-size:30px; margin:20px 0 0; }
            .perk, .lead { display:none; }
            .left::after { width:240px; height:240px; left:-90px; bottom:-130px; }
            .right { flex:none; align-items:flex-start; overflow:visible; padding:0 5vw 40px; }
            .right::before { display:none; }
            .card { z-index:2; margin-top:-28px; padding:28px 22px 24px; }
        }
    </style>
</head>
<body>
    <section class="left">
        <div class="brand">
            <svg width="50" height="50" viewBox="0 0 24 24" fill="none"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#3b82f6"/><stop offset="1" stop-color="#a78bfa"/></linearGradient></defs><path d="M21 3c-7 0-13 4-15 11l-3 7 7-3c7-2 11-8 11-15z" fill="url(#g)"/><path d="M3 21 14 10" stroke="#fff" stroke-width="1.4" stroke-linecap="round"/></svg>
            <span>My<span class="grad">Blog</span></span>
        </div>
        <div class="tag">Write<i>•</i>Share<i>•</i>Inspire</div>
        <h1>Join our <span class="grad">community</span> today</h1>
        <p class="lead">Create your free account and start reading, liking, saving and discussing great stories.</p>
        <div class="perk"><b>✓</b> Like and save your favourite posts</div>
        <div class="perk"><b>✓</b> Join the conversation with comments</div>
        <div class="perk"><b>✓</b> Free to join, takes less than a minute</div>
    </section>

    <main class="right">
        <div class="card">
            <div class="logo">
                <svg width="52" height="52" viewBox="0 0 24 24" fill="none"><path d="M21 3c-7 0-13 4-15 11l-3 7 7-3c7-2 11-8 11-15z" fill="url(#g)"/><path d="M3 21 14 10" stroke="#fff" stroke-width="1.4" stroke-linecap="round"/></svg>
            </div>
            <h2>Create Account ✨</h2>
            <p class="sub">Sign up to start your MyBlog journey.</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="field">
                    <svg class="icon" width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Full name" required autofocus autocomplete="name">
                </div>
                @error('name') <div class="error">{{ $message }}</div> @enderror

                <div class="field">
                    <svg class="icon" width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Email address" required autocomplete="username">
                </div>
                @error('email') <div class="error">{{ $message }}</div> @enderror

                <div class="field">
                    <svg class="icon" width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                    <input type="password" id="password" name="password" placeholder="Password" required autocomplete="new-password">
                    <button type="button" class="toggle" aria-label="Show or hide password" onclick="['password','password_confirmation'].forEach(i=>{const p=document.getElementById(i);p.type=p.type==='password'?'text':'password'})">
                        <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                @error('password') <div class="error">{{ $message }}</div> @enderror

                <div class="field">
                    <svg class="icon" width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/><path d="m9 16 2 2 4-4"/></svg>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm password" required autocomplete="new-password">
                </div>
                @error('password_confirmation') <div class="error">{{ $message }}</div> @enderror

                <button type="submit" class="btn">Create Account</button>
            </form>

            <div class="or">or continue with</div>

            <a href="{{ url('/auth/google') }}" class="google">
                <svg width="22" height="22" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.5 0 6.6 1.2 9.1 3.6l6.8-6.8C35.8 2.4 30.3 0 24 0 14.6 0 6.5 5.4 2.6 13.2l7.9 6.1C12.4 13.6 17.7 9.5 24 9.5z"/><path fill="#4285F4" d="M46.1 24.5c0-1.6-.1-3.1-.4-4.5H24v9h12.4c-.5 2.9-2.2 5.3-4.6 6.9l7.1 5.5c4.3-4 6.8-9.9 6.8-16.9z"/><path fill="#FBBC05" d="M10.5 28.7c-.5-1.4-.8-2.9-.8-4.7s.3-3.2.8-4.7l-7.9-6.1C.9 16.4 0 20.1 0 24s.9 7.6 2.6 10.8l7.9-6.1z"/><path fill="#34A853" d="M24 48c6.3 0 11.6-2.1 15.5-5.7l-7.1-5.5c-2 1.4-4.8 2.3-8.4 2.3-6.3 0-11.6-4.1-13.5-9.8l-7.9 6.1C6.5 42.6 14.6 48 24 48z"/></svg>
                Google
            </a>

            <p class="signin">Already registered? <a href="{{ route('login') }}">Log in</a></p>
        </div>
    </main>
</body>
</html>