@php
    $tab    = 'inline-block rounded-full px-4 py-1.5 text-sm font-medium transition';
    $tabOn  = 'bg-blue-600 text-white shadow-sm';
    $tabOff = 'border border-slate-200 bg-white text-slate-600 hover:border-blue-300 hover:text-blue-600';
@endphp

<x-blog-layout title="Notifications" description="Your latest notifications on MyBlog.">

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold">Notifications</h1>
            <p class="text-sm text-slate-500">
                {{ $unreadCount > 0 ? $unreadCount . ' unread' : 'You are all caught up' }}
            </p>
        </div>

        @if ($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                <button type="submit"
                        class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-blue-600 hover:bg-slate-50">
                    Mark all as read
                </button>
            </form>
        @endif
    </div>

    {{-- All / Unread tabs --}}
    <div class="mb-5 flex items-center gap-2">
        <a href="{{ route('notifications.index') }}"
           class="{{ $tab }} {{ $filter === 'all' ? $tabOn : $tabOff }}">All</a>
        <a href="{{ route('notifications.index', ['filter' => 'unread']) }}"
           class="{{ $tab }} {{ $filter === 'unread' ? $tabOn : $tabOff }}">
            Unread @if ($unreadCount > 0)({{ $unreadCount }})@endif
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        @forelse ($notifications as $n)
            @php $isComment = ($n->data['type'] ?? 'post') === 'comment'; @endphp

            <a href="{{ route('notifications.read', $n->id) }}"
               class="flex items-start gap-4 border-b border-slate-200 px-5 py-4 transition last:border-b-0 hover:bg-slate-50
                      {{ $n->read_at ? '' : 'bg-blue-50' }}">

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-lg">
                    {{ $isComment ? '💬' : '📝' }}
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-slate-800">
                        {{ $n->data['author'] ?? 'Someone' }}
                        {{ $isComment ? 'commented on your post' : 'published a new post' }}
                    </p>
                    <p class="mt-0.5 truncate text-sm text-slate-600">{{ $n->data['title'] ?? '' }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ $n->created_at->diffForHumans() }}</p>
                </div>

                @unless ($n->read_at)
                    <span class="mt-2 h-2.5 w-2.5 shrink-0 rounded-full bg-blue-600" title="Unread"></span>
                @endunless
            </a>
        @empty
            <div class="px-6 py-14 text-center">
                <div class="text-4xl">🔔</div>
                <p class="mt-3 font-semibold text-slate-800">
                    {{ $filter === 'unread' ? 'No unread notifications' : 'No notifications yet' }}
                </p>
                <p class="mt-1 text-sm text-slate-500">
                    New posts and comments will show up here.
                </p>
            </div>
        @endforelse
    </div>

    @if ($notifications->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $notifications->links('vendor.pagination.custom') }}
        </div>
    @endif
</x-blog-layout>