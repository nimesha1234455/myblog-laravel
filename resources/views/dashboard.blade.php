<x-blog-layout title="Dashboard">
    {{-- Admin sidebar (dashboard page eke witharai) --}}
    <x-slot:sidebar>
        <x-admin-sidebar />
    </x-slot:sidebar>

    <style>
        .t1{color:#0f172a} html.dark .t1{color:#f1f5f9}
        .t2{color:#64748b} html.dark .t2{color:#94a3b8}

        /* Glass boxes: blur tik wadi, text contrast hoda wenna */
        .pbox{background:#fff;border:1px solid #e2e8f0;border-radius:16px;box-shadow:0 4px 14px rgba(15,23,42,.10)}
        html.dark .pbox{background:rgba(10,18,40,.82);border-color:rgba(255,255,255,.10);-webkit-backdrop-filter:blur(14px);backdrop-filter:blur(14px)}

        /* ===== Post card ===== */
        .pcard{display:flex;flex-direction:column;height:100%;overflow:hidden;border-radius:14px;background:#fff;border:1px solid #e2e8f0;box-shadow:0 4px 14px rgba(15,23,42,.12);transition:transform .25s,box-shadow .25s}
        .pcard:hover{transform:translateY(-3px);box-shadow:0 14px 30px rgba(15,23,42,.24)}
        html.dark .pcard{background:#0e1830;border-color:rgba(255,255,255,.10)}
        .pcard a:focus-visible,.pcard-btn:focus-visible{outline:2px solid #60a5fa;outline-offset:2px;border-radius:6px}
        .pcard-img{position:relative;display:block;height:124px;overflow:hidden;background:#cbd5e1;flex-shrink:0}
        .pcard-img img{display:block;width:100%;height:100%;object-fit:cover;transition:transform .5s}
        .pcard:hover .pcard-img img{transform:scale(1.06)}
        .pcard-badge{position:absolute;left:10px;bottom:10px;color:#fff;font-size:10px;font-weight:600;padding:3px 9px;border-radius:999px;box-shadow:0 2px 6px rgba(0,0,0,.3)}
        .pcard-body{display:flex;flex:1;flex-direction:column;padding:12px 12px 12px}
        .pcard-title{font-size:14px;font-weight:700;line-height:1.35;margin:0 0 6px;min-height:2.7em;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
        .pcard-title a:hover{color:#2563eb}
        .pcard-excerpt{font-size:12px;line-height:1.5;margin:0 0 12px;min-height:3em;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}

        /* FIX: footer eke date / button peli dekakt kaden  ek nawaththanw */
        .pcard-foot{margin-top:auto;display:flex;flex-wrap:nowrap;align-items:center;gap:8px;padding-top:10px;border-top:1px solid #e2e8f0}
        html.dark .pcard-foot{border-top-color:rgba(255,255,255,.10)}
        .pcard-foot,.pcard-foot *{white-space:nowrap}
        .pcard-meta{display:inline-flex;align-items:center;gap:4px;font-size:10.5px;min-width:0}
        .pcard-btn{margin-left:auto;flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;height:30px;background:#2563eb;color:#fff;font-size:11px;font-weight:600;padding:0 10px;border-radius:8px;transition:background .2s}
        .pcard-btn:hover{background:#1d4ed8}

        .tabbtn{padding:6px 14px;border-radius:8px;font-size:12px;font-weight:500;transition:.2s}
        .tabbtn.on{background:#2563eb;color:#fff}
        .tabbtn:not(.on){color:#475569} html.dark .tabbtn:not(.on){color:#cbd5e1}
        .tabbtn:not(.on):hover{background:rgba(100,116,139,.15)}
        .tabbtn:focus-visible,.focus-ring:focus-visible{outline:2px solid #60a5fa;outline-offset:2px}

        /* Trending rows */
        .trend-row{border-radius:12px;padding:6px;margin:-6px;transition:background .2s}
        .trend-row:hover{background:rgba(148,163,184,.10)}
    </style>

    <div class="grid gap-6 xl:grid-cols-3">

        {{-- LEFT: main --}}
        <div class="space-y-6 xl:col-span-2">

            {{-- Hero banner + stats --}}
            @php
                $pct = fn ($n) => $stats['total'] ? round($n / $stats['total'] * 100) . '% of total' : '0% of total';
                $cards = [
                    ['label' => 'Total Posts', 'value' => $stats['total'],                'sub' => 'All time',               'color' => '#2563eb', 'icon' => 'M9 12h6m-6 4h6m2 4H7a2 2 0 01-2-2V6a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V18a2 2 0 01-2 2z'],
                    ['label' => 'Published',   'value' => $stats['published'],            'sub' => $pct($stats['published']), 'color' => '#10b981', 'icon' => 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12'],
                    ['label' => 'Drafts',      'value' => $stats['drafts'],               'sub' => $pct($stats['drafts']),    'color' => '#f59e0b', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                    ['label' => 'Views',       'value' => number_format($stats['views']), 'sub' => 'Total views',            'color' => '#9333ea', 'icon' => 'M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                ];
            @endphp
            <section class="relative overflow-hidden rounded-2xl border border-white/10 shadow-lg"
                     style="background:#0b1530 url('{{ asset('images/bg.jpg') }}') center/cover no-repeat;">
                <div class="absolute inset-0" style="background:linear-gradient(90deg,rgba(8,15,35,.88),rgba(8,15,35,.45));"></div>

                <div class="relative p-6 sm:p-7">
                    <div class="flex items-center justify-between gap-6">
                        <div class="flex min-w-0 items-center gap-4">
                            <x-avatar :user="auth()->user()" size="h-16 w-16" text="text-xl" />
                            <div class="min-w-0">
                                <h1 class="truncate text-2xl font-bold text-white">Welcome back, {{ \Illuminate\Support\Str::title(\Illuminate\Support\Str::before(auth()->user()->name, ' ')) }}!</h1>
                                <p class="mt-0.5 text-sm" style="color:#cbd5e1;">Create. Share. Inspire. Your stories matter.</p>
                            </div>
                        </div>
                        <a href="{{ route('posts.create') }}"
                           class="focus-ring hidden shrink-0 items-center gap-2 whitespace-nowrap rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition hover:bg-blue-700 sm:inline-flex">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                            Create New Post
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </a>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
                        @foreach ($cards as $c)
                            <div class="flex h-full items-center gap-3 rounded-xl p-3.5"
                                 style="background:rgba(10,18,40,.6);border:1px solid rgba(255,255,255,.10);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg" style="background:{{ $c['color'] }};">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $c['icon'] }}"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs" style="color:#cbd5e1;">{{ $c['label'] }}</p>
                                    <p class="text-xl font-bold leading-tight text-white">{{ $c['value'] }}</p>
                                    <p class="truncate text-[11px]" style="color:#94a3b8;">{{ $c['sub'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- Latest posts --}}
            @php
                $items = $latest->map(fn ($p) => [
                    'id'     => $p->id,
                    'status' => strtolower((string) ($p->status ?? 'published')),
                    'ts'     => $p->created_at->timestamp,
                    'views'  => (int) $p->views,
                ])->values();
            @endphp
            <div x-data="postFilter(@js($items))">
                <div class="pbox mb-4 flex flex-wrap items-center justify-between gap-3 px-5 py-3">
                    <div class="flex items-center gap-3">
                        <span class="h-8 w-1 rounded-full bg-indigo-500"></span>
                        <div>
                            <h2 class="t1 text-lg font-bold">Latest Posts</h2>
                            <p class="t2 text-xs">Your recent blog posts</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        {{-- Filter tabs --}}
                        <div class="flex items-center gap-1 rounded-xl p-1" style="background:rgba(100,116,139,.12);">
                            <button type="button" class="tabbtn" :class="tab==='all' && 'on'" @click="tab='all'">All</button>
                            <button type="button" class="tabbtn" :class="tab==='published' && 'on'" @click="tab='published'">Published</button>
                            <button type="button" class="tabbtn" :class="tab==='draft' && 'on'" @click="tab='draft'">Drafts</button>
                        </div>

                        {{-- Sort --}}
                        <div class="relative" @click.outside="open=false">
                            <button type="button" @click="open=!open"
                                    class="t1 focus-ring flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-medium"
                                    style="background:rgba(100,116,139,.12);">
                                <span x-text="label()"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-cloak x-transition
                                 class="pbox absolute right-0 z-20 mt-2 w-40 overflow-hidden p-1">
                                <button type="button" class="t1 block w-full rounded-lg px-3 py-2 text-left text-xs hover:bg-slate-500/10" @click="sort='new';open=false">Newest first</button>
                                <button type="button" class="t1 block w-full rounded-lg px-3 py-2 text-left text-xs hover:bg-slate-500/10" @click="sort='old';open=false">Oldest first</button>
                                <button type="button" class="t1 block w-full rounded-lg px-3 py-2 text-left text-xs hover:bg-slate-500/10" @click="sort='views';open=false">Most viewed</button>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($latest->count())
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($latest as $post)
                            <div x-show="matches({{ $post->id }})" :style="'order:' + order({{ $post->id }})">
                                @include('posts._card-compact', ['post' => $post])
                            </div>
                        @endforeach
                    </div>

                    <p x-show="visibleCount() === 0" x-cloak class="pbox t2 mt-4 p-8 text-center text-sm">No posts in this filter.</p>
                @else
                    <p class="pbox t2 p-10 text-center">You haven't written any posts yet.</p>
                @endif
            </div>
        </div>

        {{-- RIGHT: trending + activity --}}
        <aside class="space-y-6">

            <div class="pbox p-5">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#f97316" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/><path d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z"/></svg>
                    <h3 class="t1 font-bold">Trending Posts</h3>
                </div>
                <p class="t2 mb-5 mt-0.5 text-xs">Most viewed</p>

                <div class="space-y-4">
                    @forelse ($trending as $i => $post)
                        <a href="{{ route('posts.show', $post) }}" class="trend-row focus-ring group flex items-center gap-3">
                            <span class="t2 w-5 shrink-0 text-center text-sm font-bold">{{ $i + 1 }}</span>
                            @if ($post->cover_image)
                                <img src="{{ asset('storage/' . $post->cover_image) }}" alt=""
                                     class="h-14 w-16 shrink-0 rounded-lg object-cover">
                            @else
                                <div class="h-14 w-16 shrink-0 rounded-lg" style="background:rgba(100,116,139,.25);"></div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <p class="t1 line-clamp-2 text-sm font-semibold leading-snug group-hover:text-blue-500">{{ $post->title }}</p>
                                <div class="mt-1.5 flex items-center justify-between gap-2 text-xs">
                                    <span class="truncate rounded px-2 py-0.5" style="background:rgba(59,130,246,.18);color:#3b82f6;">{{ $post->category->name }}</span>
                                    <span class="t2 inline-flex shrink-0 items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ number_format($post->views) }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <p class="t2 text-sm">No posts yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Recent Activity (activity_logs table eken) --}}
            @php
                // action eka anuwa icon + color ekak (stroke color, background)
                $activityMeta = function (string $action): array {
                    return match (true) {
                        $action === 'auth.login'    => ['#3b82f6', 'rgba(59,130,246,.18)',  'M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75'],
                        $action === 'auth.logout'   => ['#94a3b8', 'rgba(148,163,184,.18)', 'M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9'],
                        $action === 'auth.register' => ['#10b981', 'rgba(16,185,129,.18)',  'M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z'],
                        str_ends_with($action, 'created') => ['#10b981', 'rgba(16,185,129,.18)',  'M12 4.5v15m7.5-7.5h-15'],
                        str_ends_with($action, 'updated') => ['#f59e0b', 'rgba(245,158,11,.18)',  'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z'],
                        str_ends_with($action, 'deleted') => ['#ef4444', 'rgba(239,68,68,.18)',   'M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0'],
                        str_ends_with($action, 'status')  => ['#8b5cf6', 'rgba(139,92,246,.18)',  'M4.5 12.75l6 6 9-13.5'],
                        default                           => ['#818cf8', 'rgba(129,140,248,.18)', 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'],
                    };
                };
            @endphp
            <div class="pbox p-5">
                <div class="mb-4 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#818cf8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <h3 class="t1 font-bold">Recent Activity</h3>
                    </div>
                    @if ($activities->count())
                        <a href="{{ route('admin.activity-logs') }}"
                           class="focus-ring text-xs font-medium text-indigo-500 hover:text-indigo-400">View all</a>
                    @endif
                </div>

                <div class="space-y-4">
                    @forelse ($activities as $log)
                        @php [$stroke, $bg, $path] = $activityMeta($log->action); @endphp
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full" style="background:{{ $bg }};">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="{{ $stroke }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $path }}"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="t1 text-sm font-medium">{{ $log->description }}</p>
                                <p class="t2 text-xs">
                                    {{ $log->user?->name ?? 'System' }} &middot; {{ $log->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @empty
                        {{-- Nicer empty state --}}
                        <div class="flex flex-col items-center py-4 text-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full" style="background:rgba(129,140,248,.15);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#818cf8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="t1 mt-3 text-sm font-medium">No recent activity</p>
                            <p class="t2 mt-1 text-xs">Logins and post changes will show up here.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>

    <script>
        function postFilter(items) {
            return {
                tab: 'all',
                sort: 'new',
                open: false,
                items: items,
                matches(id) {
                    const p = this.items.find(x => x.id === id);
                    return this.tab === 'all' || (p && p.status === this.tab);
                },
                visibleCount() {
                    return this.items.filter(x => this.matches(x.id)).length;
                },
                order(id) {
                    const s = [...this.items].sort((a, b) =>
                        this.sort === 'old' ? a.ts - b.ts :
                        this.sort === 'views' ? b.views - a.views :
                        b.ts - a.ts);
                    return s.findIndex(x => x.id === id);
                },
                label() {
                    return { new: 'Newest first', old: 'Oldest first', views: 'Most viewed' }[this.sort];
                }
            };
        }
    </script>
</x-blog-layout>