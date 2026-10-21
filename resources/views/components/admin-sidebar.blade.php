{{-- resources/views/components/admin-sidebar.blade.php --}}
@php
    use Illuminate\Support\Facades\Route;

    $to = fn (string $name, string $fallback) => Route::has($name) ? route($name) : url($fallback);

    // Filled icons (evenodd = cut-out details). My Posts dashboard eke wath active.
    // 'can' = me link eka pennanna one permission eka (admin ta okkoma allow)
    $items = [
        ['label' => 'My Posts',   'href' => $to('posts.my', '/my-posts'),
         'can' => 'posts.create',
         'active' => request()->routeIs('posts.my', 'dashboard'),
         'icon' => 'M6 3h8l5 5v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z M8 11h8v1.6H8z M8 14.5h8v1.6H8z M8 7.5h4v1.6H8z'],
        ['label' => 'Add Post',   'href' => $to('posts.create', '/posts/create'),
         'can' => 'posts.create',
         'active' => request()->routeIs('posts.create'),
         'icon' => 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z M11.2 7h1.6v4.2H17v1.6h-4.2V17h-1.6v-4.2H7v-1.6h4.2z'],
        ['label' => 'Drafts',     'href' => $to('posts.drafts', '/drafts'),
         'can' => 'posts.create',
         'active' => request()->routeIs('posts.drafts'),
         'icon' => 'M6 3h8l5 5v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z M8 13h8v1.6H8z M8 16.5h5v1.6H8z'],
        ['label' => 'Categories', 'href' => $to('categories.index', '/categories'),
         'can' => 'categories.manage',
         'active' => request()->routeIs('categories.*'),
         'icon' => 'M3 3h9.5l8.2 8.2a2 2 0 0 1 0 2.8l-6.7 6.7a2 2 0 0 1-2.8 0L3 12.5z M7.5 6a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z'],
        ['label' => 'Analytics',  'href' => $to('analytics', '/analytics'),
         'can' => 'analytics.view',
         'active' => request()->routeIs('analytics*'),
         'icon' => 'M4 12h4v9H4z M10 3h4v18h-4z M16 8h4v13h-4z'],
        ['label' => 'SMTP Settings', 'href' => $to('admin.smtp', '/admin/smtp'),
         'can' => 'smtp.manage',
         'active' => request()->routeIs('admin.smtp*'),
         'icon' => 'M3 5h18a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1z M4.5 7.3l7.5 5 7.5-5v1.7l-7.5 5-7.5-5z'],
        // Activity Log: admin ta witharai (Gate::before eken admin ta allow, anith ayata gate eka nathi nisa deny)
        ['label' => 'Activity Log', 'href' => $to('admin.activity-logs', '/admin/activity-logs'),
         'can' => 'activitylog.view',
         'active' => request()->routeIs('admin.activity-logs*'),
         'icon' => 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z M11.2 6.5h1.6v5.2l3.6 2.1-.8 1.4-4.4-2.6z'],
        ['label' => 'Permissions', 'href' => $to('permissions.index', '/admin/permissions'),
         'can' => 'permissions.manage',
         'active' => request()->routeIs('permissions.*'),
         'icon' => 'M12 2a5 5 0 0 0-5 5v3H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8a2 2 0 0 0-2-2h-1V7a5 5 0 0 0-5-5z M9 7a3 3 0 0 1 6 0v3H9z M11.2 14h1.6v4h-1.6z'],
        ['label' => 'Trash',      'href' => $to('posts.trash', '/trash'),
         'can' => 'posts.delete',
         'active' => request()->routeIs('posts.trash'),
         'icon' => 'M9 3h6l1 2h4v2H4V5h4z M6 9h12l-1 11a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2z M9.5 11.5h1.4v7H9.5z M13.1 11.5h1.4v7h-1.4z'],
    ];
@endphp

<div class="flex min-h-[34rem] flex-col rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-700/60 dark:bg-slate-900/70">

    {{-- Header --}}
    <div class="flex items-center gap-2 px-3 pb-4 pt-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5">
            <path d="M19 12H5M11 6l-6 6 6 6"/>
        </svg>
        Manage
    </div>

    {{-- Links --}}
    <nav class="space-y-1.5">
        @foreach ($items as $item)
            @if (auth()->user()?->can($item['can']))
                <a href="{{ $item['href'] }}"
                   @if ($item['active']) aria-current="page" @endif
                   class="flex items-center gap-3.5 rounded-xl px-4 py-3 text-[15px] font-medium transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-400
                          {{ $item['active']
                              ? 'bg-indigo-600 text-white dark:bg-indigo-500/30 dark:ring-1 dark:ring-indigo-400/40'
                              : 'text-slate-600 hover:bg-indigo-50 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-indigo-500/10 dark:hover:text-white' }}">
                    <svg viewBox="0 0 24 24" fill="currentColor" fill-rule="evenodd"
                         class="h-[22px] w-[22px] shrink-0 {{ $item['active'] ? 'text-white dark:text-indigo-300' : 'text-indigo-500 dark:text-indigo-400' }}">
                        <path d="{{ $item['icon'] }}"/>
                    </svg>
                    {{ $item['label'] }}
                </a>
            @endif
        @endforeach
    </nav>

    {{-- Bottom promo (sidebar eke yatama) --}}
    <div class="relative mt-auto overflow-hidden rounded-xl border border-slate-200 bg-slate-50 px-3 pb-8 pt-5 text-center
                dark:border-slate-700/60 dark:bg-slate-800/50">
        <svg viewBox="0 0 24 24" fill="currentColor" fill-rule="evenodd" class="mx-auto h-7 w-7 text-indigo-500 dark:text-indigo-400">
            <path d="M16.9 2.6a2.2 2.2 0 0 1 3.1 3.1L8.4 17.3 3 19l1.7-5.4z M6.3 15.5l-.6 1.9 1.9-.6z"/>
        </svg>
        <p class="mt-2 text-[15px] font-semibold text-slate-800 dark:text-white">Write. Share. Inspire.</p>
        <p class="text-xs text-slate-500 dark:text-slate-300">Your stories matter.</p>
        <svg viewBox="0 0 200 30" preserveAspectRatio="none" class="absolute inset-x-0 bottom-0 h-7 w-full text-indigo-500/40">
            <path d="M0 18 C30 4, 60 4, 100 16 S170 28, 200 12 V30 H0z" fill="currentColor"/>
        </svg>
    </div>
</div>