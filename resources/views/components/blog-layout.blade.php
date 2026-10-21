@props([
    'title'       => 'Home',
    'description' => 'MyBlog - Write, share and get inspired. Read the latest posts on programming, technology, lifestyle and more.',
    'keywords'    => 'blog, myblog, programming, technology, lifestyle, laravel',
    'image'       => null,
    'type'        => 'website',
])
@php
    $authUser      = auth()->user();
    $isAdmin       = $authUser && $authUser->role === 'admin';
    $unreadCount   = $authUser ? $authUser->unreadNotifications()->count() : 0;
    $notifications = $authUser ? $authUser->notifications()->latest()->take(8)->get() : collect();

    // posts.create permission thiyen readers lata Add Post / My Posts / Drafts penna
    $canCreate     = $authUser && $authUser->can('posts.create');
    $draftCount    = $canCreate ? $authUser->posts()->where('status', 'draft')->count() : 0;
    // admin ට approval balagen inna posts ganan
    $pendingCount  = $isAdmin ? \App\Models\Post::where('status', 'pending')->count() : 0;

    $hasRoute = fn ($r) => \Illuminate\Support\Facades\Route::has($r);

    $searchAction = $hasRoute('posts.index') ? route('posts.index') : url('/');

    // Main navbar links: [route, label, active?, visible?, badge]
    $mainLinks = [
        ['posts.index',   'Home',        request()->routeIs('posts.index'),   true,                     null],
        ['posts.saved',   'Saved Posts', request()->routeIs('posts.saved'),   (bool) $authUser,         null],
        // NEW: Follow system - follow karana ayage posts witharak pennana feed eka
        ['following',     'Following',   request()->routeIs('following'),     (bool) $authUser,         null],
        ['dashboard',     'Dashboard',   request()->routeIs('dashboard'),     $isAdmin,                 null],
        ['posts.pending', 'Pending',     request()->routeIs('posts.pending'), $isAdmin,                 $pendingCount],
        ['posts.my',      'My Posts',    request()->routeIs('posts.my'),      $canCreate && ! $isAdmin, null],
        ['posts.create',  'Add Post',    request()->routeIs('posts.create'),  $canCreate && ! $isAdmin, null],
        ['posts.drafts',  'Drafts',      request()->routeIs('posts.drafts'),  $canCreate && ! $isAdmin, $draftCount],
    ];

    // Admin "Manage" links (phone / tablet menu eke witharai): [route, label, active?, badge]
    $manageLinks = [
        ['posts.my',          'My Posts',    request()->routeIs('posts.my'),         null],
        ['posts.create',      'Add Post',    request()->routeIs('posts.create'),     null],
        ['posts.drafts',      'Drafts',      request()->routeIs('posts.drafts'),     $draftCount],
        ['categories.index',  'Categories',  request()->routeIs('categories.*'),     null],
        ['analytics',         'Analytics',   request()->routeIs('analytics'),        null],
        ['permissions.index', 'Permissions', request()->routeIs('permissions.*'),    null],
        ['posts.trash',       'Trash',       request()->routeIs('posts.trash'),      null],
    ];

    // ===== SEO / Meta =====
    $metaDescription = \Illuminate\Support\Str::limit(
        trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags(str_replace('<', ' <', $description))))),
        160
    );
    $metaImage = $image
        ? (str_starts_with($image, 'http') ? $image : asset($image))
        : asset('images/bg.jpg');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - MyBlog</title>

    {{-- ===== Meta tags ===== --}}
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords" content="{{ $keywords }}">
    <meta name="author" content="MyBlog">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph (Facebook, WhatsApp, LinkedIn) --}}
    <meta property="og:site_name" content="MyBlog">
    <meta property="og:type" content="{{ $type }}">
    <meta property="og:title" content="{{ $title }} - MyBlog">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $metaImage }}">

    {{-- Twitter / X --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }} - MyBlog">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $metaImage }}">

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <style>[x-cloak] { display: none !important; }</style>

    {{-- Theme: page eka render wenna kalin apply karanawa (flash ekak nathuwa) --}}
    <script>
        (function () {
            try {
                var t = localStorage.getItem('theme');
                if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Background image (asset() haraha, Vite haraha newei ) --}}
    <style>
        body {
            background-image:
                linear-gradient(rgba(248, 250, 252, 0.35), rgba(248, 250, 252, 0.55)),
                url('{{ asset('images/bg.jpg') }}');
        }
        .dark body {
            background-image:
                linear-gradient(rgba(2, 6, 23, 0.55), rgba(2, 6, 23, 0.75)),
                url('{{ asset('images/bg.jpg') }}');
        }

        /* ===== Dropdown (avatar + notifications): solid background ===== */
        .dropdown-solid {
            background-color: #ffffff !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }
        .dark .dropdown-solid {
            background-color: #0f172a !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6) !important;
        }
        .dark .dropdown-solid .text-slate-900,
        .dark .dropdown-solid .text-slate-700,
        .dark .dropdown-solid .font-medium,
        .dark .dropdown-solid .font-bold,
        .dark .dropdown-solid .font-semibold {
            color: #f1f5f9 !important;
        }
        .dark .dropdown-solid .text-slate-500,
        .dark .dropdown-solid .text-slate-400 {
            color: #94a3b8 !important;
        }
        .dark .dropdown-solid a:hover,
        .dark .dropdown-solid button:hover {
            background-color: rgba(255, 255, 255, 0.08) !important;
        }
        .dark .dropdown-solid .border-b,
        .dark .dropdown-solid .border-slate-200 {
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        /* Logout: hover කළාම විතරක් red (light + dark) */
        .dropdown-solid .logout-item:hover {
            background-color: rgba(239, 68, 68, 0.1) !important;
            color: #dc2626 !important;
        }
        .dark .dropdown-solid .logout-item:hover {
            background-color: rgba(239, 68, 68, 0.15) !important;
            color: #f87171 !important;
        }
        .dropdown-solid .logout-item:hover svg {
            color: currentColor !important;
        }

        /* Search input eke browser eke default "x" clear button eka ain karanawa */
        input[type="search"]::-webkit-search-cancel-button { -webkit-appearance: none; appearance: none; }
    </style>

    {{-- ===== Top progress bar styles ===== --}}
    <style>
        #top-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            width: 0;
            z-index: 10000;
            pointer-events: none;
            background: linear-gradient(90deg, #3b82f6, #9333ea, #ec4899);
            box-shadow: 0 0 10px rgba(59,130,246,.8);
            transition: width .3s ease, opacity .4s ease;
        }
    </style>

    {{ $head ?? '' }}
</head>
<body class="text-slate-800 antialiased">

{{-- ================= TOP PROGRESS BAR ================= --}}
<div id="top-progress"></div>

<script>
(() => {
    const bar = document.getElementById('top-progress');
    let timer;

    const startBar = () => {
        clearInterval(timer);
        bar.style.opacity = 1;
        let w = 10;
        bar.style.width = w + '%';
        timer = setInterval(() => {
            w += (90 - w) * 0.08;
            bar.style.width = w + '%';
        }, 200);
    };

    const finishBar = () => {
        clearInterval(timer);
        bar.style.width = '100%';
        setTimeout(() => { bar.style.opacity = 0; bar.style.width = 0; }, 400);
    };

    /* Link click karaddi */
    document.addEventListener('click', e => {
        const a = e.target.closest('a[href]');
        if (!a || a.target === '_blank' || a.hasAttribute('download') || e.ctrlKey || e.metaKey || e.shiftKey) return;
        const url = new URL(a.href, location.href);
        if (url.origin === location.origin && url.href !== location.href && !a.hash) startBar();
    });

    /* Form submit karaddi (confirm modal eken cancel una ewa ignore karanawa) */
    window.addEventListener('submit', e => {
        setTimeout(() => { if (!e.defaultPrevented) startBar(); }, 0);
    });

    /* Load unama / back button eken awama */
    window.addEventListener('load', finishBar);
    window.addEventListener('pageshow', e => { if (e.persisted) finishBar(); });
})();
</script>
{{-- ================= /TOP PROGRESS BAR ================= --}}

{{-- flex-col: footer eka content kuda unath page eke paha-ma thiyenawa --}}
<div class="flex min-h-screen flex-col" x-data="{ menu: false }" x-on:keydown.escape.window="menu = false">

    {{-- ================= TOP NAVBAR ================= --}}
    <header class="sticky top-0 z-30 border-b bg-white">
        <div class="mx-auto flex max-w-[1400px] items-center gap-2 px-4 py-3 sm:gap-4 sm:px-8">

            {{-- Hamburger (phone / tablet) --}}
            <button type="button" x-on:click="menu = !menu" aria-label="Toggle menu"
                    class="shrink-0 rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>
            </button>

            {{-- Logo --}}
            <a href="{{ $searchAction }}"
               class="shrink-0 text-lg font-bold text-slate-900 sm:text-xl">✒️ MyBlog</a>

            {{-- Desktop links --}}
            <nav class="ml-6 hidden items-center gap-1.5 lg:flex">
                @foreach ($mainLinks as [$route, $label, $active, $visible, $badge])
                    @if ($visible && $hasRoute($route))
                        <a href="{{ route($route) }}"
                           class="flex items-center gap-2 rounded-lg px-3.5 py-2 text-sm font-medium transition
                                  {{ $active ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                            {{ $label }}
                            @if ($badge)
                                <span class="flex h-5 min-w-[20px] items-center justify-center rounded-full bg-red-500 px-1.5 text-[11px] font-bold text-white">{{ $badge }}</span>
                            @endif
                        </a>
                    @endif
                @endforeach
            </nav>

            {{-- Search (tablet / desktop) --}}
            <form method="GET" action="{{ $searchAction }}" role="search"
                  class="ml-auto hidden w-full min-w-0 max-w-xs md:block">
                @if (request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="relative">
                    <input type="search" name="search" value="{{ request('search') }}"
                           placeholder="Search posts..." autocomplete="off"
                           class="w-full rounded-lg border-slate-200 bg-slate-50 pr-10 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <button type="submit" aria-label="Search"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-blue-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/>
                        </svg>
                    </button>
                </div>
            </form>

            {{-- Right side --}}
            <div class="ml-auto flex shrink-0 items-center gap-2 sm:gap-3 md:ml-0">

                {{-- Dark mode toggle --}}
                <div x-data="{
                        dark: document.documentElement.classList.contains('dark'),
                        toggle() {
                            this.dark = !this.dark;
                            document.documentElement.classList.toggle('dark', this.dark);
                            try { localStorage.setItem('theme', this.dark ? 'dark' : 'light'); } catch (e) {}
                        }
                     }">
                    <button type="button" @click="toggle()" title="Toggle dark mode"
                            class="rounded-full p-2 text-lg leading-none hover:bg-slate-100">
                        <span x-text="dark ? '☀️' : '🌙'"></span>
                    </button>
                </div>

                @auth
                    {{-- Notification bell --}}
                    <div x-data="{ open: false }" class="relative">
                        <button type="button" @click="open = !open" @click.outside="open = false"
                                class="relative rounded-full p-2 hover:bg-slate-100">
                            <svg class="h-5 w-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                            </svg>
                            @if ($unreadCount > 0)
                                <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </button>

                        {{-- Phone di screen eke pahaltm, desktop destop eked w-80 --}}
                        <div x-show="open" x-cloak
                             class="dropdown-solid fixed inset-x-4 top-16 z-50 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg sm:absolute sm:inset-x-auto sm:right-0 sm:top-auto sm:mt-2 sm:w-80">
                            <div class="flex items-center justify-between border-b px-4 py-3">
                                <span class="text-sm font-bold">Notifications</span>
                                @if ($unreadCount > 0)
                                    <form method="POST" action="{{ route('notifications.readAll') }}">
                                        @csrf
                                        <button class="text-xs text-blue-600 hover:underline">Mark all as read</button>
                                    </form>
                                @endif
                            </div>

                            <div class="max-h-80 overflow-y-auto">
                                @forelse ($notifications as $n)
                                    @php $isComment = ($n->data['type'] ?? 'post') === 'comment'; @endphp
                                    <a href="{{ route('notifications.read', $n->id) }}"
                                       class="block border-b px-4 py-3 text-sm hover:bg-slate-50 {{ $n->read_at ? '' : 'bg-blue-50' }}">
                                        <p class="font-medium">
                                            {{ $isComment ? '💬' : '📝' }}
                                            {{ $n->data['author'] }}
                                            {{ $isComment ? 'commented on your post' : 'published a new post' }}
                                        </p>
                                        <p class="truncate text-slate-500">{{ $n->data['title'] }}</p>
                                        <p class="mt-1 text-xs text-slate-400">{{ $n->created_at->diffForHumans() }}</p>
                                    </a>
                                @empty
                                    <p class="px-4 py-8 text-center text-sm text-slate-400">No notifications yet</p>
                                @endforelse
                            </div>

                            {{-- NEW: Notifications page ekata yanna --}}
                            <a href="{{ route('notifications.index') }}"
                               class="block border-t px-4 py-3 text-center text-sm font-medium text-blue-600 hover:bg-slate-50">
                                View all notifications
                            </a>
                        </div>
                    </div>

                    {{-- Avatar dropdown: Settings + Logout --}}
                    <div x-data="{ open: false }" class="relative">
                        <button type="button" @click="open = !open" @click.outside="open = false"
                                aria-label="Account menu" :aria-expanded="open"
                                class="flex rounded-full outline-none ring-2 ring-transparent transition
                                       hover:ring-blue-500/40 focus-visible:ring-blue-400"
                                :class="open && 'ring-blue-500/40'">
                            <x-avatar :user="auth()->user()" size="h-9 w-9" />
                        </button>

                        <div x-show="open" x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="dropdown-solid absolute right-0 top-full z-50 mt-3 w-52 origin-top-right rounded-2xl border border-slate-200 bg-white p-1.5 shadow-xl">

                            {{-- Header --}}
                            <div class="flex items-center gap-3 px-2.5 pb-2.5 pt-2">
                                <x-avatar :user="auth()->user()" size="h-8 w-8" />
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $isAdmin ? 'Author' : 'Reader' }}</div>
                                </div>
                            </div>

                            <div class="mx-1 mb-1 border-t border-slate-200"></div>

                            @if ($hasRoute('profile.edit'))
                                <a href="{{ route('profile.edit') }}"
                                   class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm text-slate-700 transition hover:bg-slate-100">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                    </svg>
                                    Settings
                                </a>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="logout-item flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-sm text-slate-700 transition hover:bg-red-50 hover:text-red-600">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                       class="rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">
                        Login
                    </a>
                    @if ($hasRoute('register'))
                        <a href="{{ route('register') }}"
                           class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700 sm:px-4">
                            Register
                        </a>
                    @endif
                @endauth
            </div>
        </div>

        {{-- Mobile / tablet menu panel --}}
        <div x-show="menu" x-cloak x-transition class="border-t px-4 pb-4 pt-3 sm:px-8 lg:hidden">
            <form method="GET" action="{{ $searchAction }}" role="search" class="mb-3 md:hidden">
                @if (request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="relative">
                    <input type="search" name="search" value="{{ request('search') }}"
                           placeholder="Search posts..." autocomplete="off"
                           class="w-full rounded-lg border-slate-200 bg-slate-50 pr-10 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <button type="submit" aria-label="Search"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-blue-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/>
                        </svg>
                    </button>
                </div>
            </form>

            <div class="space-y-1">
                @foreach ($mainLinks as [$route, $label, $active, $visible, $badge])
                    @if ($visible && $hasRoute($route))
                        <a href="{{ route($route) }}"
                           class="flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium
                                  {{ $active ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                            <span>{{ $label }}</span>
                            @if ($badge)
                                <span class="flex h-5 min-w-[20px] items-center justify-center rounded-full bg-red-500 px-1.5 text-[11px] font-bold text-white">{{ $badge }}</span>
                            @endif
                        </a>
                    @endif
                @endforeach

                @if ($isAdmin)
                    <p class="px-3 pb-1 pt-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Manage</p>
                    @foreach ($manageLinks as [$route, $label, $active, $badge])
                        @if ($hasRoute($route))
                            <a href="{{ route($route) }}"
                               class="flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium
                                      {{ $active ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                                <span>{{ $label }}</span>
                                @if ($badge)
                                    <span class="flex h-5 min-w-[20px] items-center justify-center rounded-full bg-blue-500 px-1.5 text-[11px] font-bold text-white">{{ $badge }}</span>
                                @endif
                            </a>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    </header>

    {{-- ================= MAIN (optional left sidebar slot) ================= --}}
    <main class="mx-auto w-full max-w-[1400px] flex-1 p-4 sm:p-6 lg:p-8">
        @isset($sidebar)
            <div class="flex gap-6">
                <aside class="hidden w-60 shrink-0 lg:block">
                    {{ $sidebar }}
                </aside>
                <div class="min-w-0 flex-1">
                    {{ $slot }}
                </div>
            </div>
        @else
            {{ $slot }}
        @endisset
    </main>

    {{-- ================= FOOTER ================= --}}
    <x-footer />
</div>

{{-- Toast notifications --}}
<div x-data="{
        toasts: [],
        add(type, message) {
            this.toasts.push({ id: Date.now() + Math.random(), type, message });
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
     }"
     x-init="
        @if (session('success')) add('success', @js(session('success'))); @endif
        @if (session('error')) add('error', @js(session('error'))); @endif
     "
     x-on:toast.window="add($event.detail.type, $event.detail.message)"
     class="pointer-events-none fixed right-4 top-4 z-[60] flex w-80 max-w-[calc(100vw-2rem)] flex-col gap-3">

    <template x-for="t in toasts" :key="t.id">
        <div x-data="{
                show: false,
                timer: null,
                start() { this.timer = setTimeout(() => this.close(), 4000); },
                pause() { clearTimeout(this.timer); },
                close() { this.show = false; setTimeout(() => remove(t.id), 300); }
             }"
             x-init="$nextTick(() => show = true); start()"
             x-show="show"
             x-on:mouseenter="pause()"
             x-on:mouseleave="start()"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-full opacity-0"
             class="pointer-events-auto flex items-start gap-3 rounded-xl border bg-white p-4 shadow-lg"
             :class="t.type === 'success' ? 'border-green-200' : 'border-red-200'">

            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold"
                 :class="t.type === 'success' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'"
                 x-text="t.type === 'success' ? '✓' : '!'"></div>

            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-slate-900" x-text="t.type === 'success' ? 'Success' : 'Error'"></p>
                <p class="mt-0.5 break-words text-sm text-slate-600" x-text="t.message"></p>
            </div>

            <button type="button" x-on:click="close()" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
    </template>
</div>

{{-- Confirm modal --}}
<div x-data="{
        open: false,
        title: '',
        message: '',
        button: 'Confirm',
        form: null,
        intercept(e) {
            const form = e.target;
            if (form.dataset && form.dataset.confirm) {
                e.preventDefault();
                this.ask(form);
            }
        },
        ask(form) {
            this.title = form.dataset.confirmTitle || 'Are you sure?';
            this.message = form.dataset.confirm;
            this.button = form.dataset.confirmButton || 'Confirm';
            this.form = form;
            this.open = true;
        },
        accept() {
            this.open = false;
            this.form.submit();
        }
     }"
     x-on:submit.window="intercept($event)"
     x-on:keydown.escape.window="open = false"
     x-on:click.self="open = false"
     x-show="open" x-cloak x-transition.opacity
     class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 px-4">

    <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-2xl">
            🗑️
        </div>
        <h3 class="mt-4 text-center text-lg font-bold text-slate-900" x-text="title"></h3>
        <p class="mt-2 text-center text-sm text-slate-500" x-text="message"></p>

        <div class="mt-6 flex gap-3">
            <button type="button" x-on:click="open = false"
                    class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium hover:bg-slate-50">
                Cancel
            </button>
            <button type="button" x-on:click="accept()" x-text="button"
                    class="flex-1 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
            </button>
        </div>
    </div>
</div>
</body>
</html>