@php
    $isLiked  = $post->liked ?? false;
    $isSaved  = $post->bookmarked ?? false;
    $cardUser = auth()->user();
    $cardIsAdmin = $cardUser && $cardUser->role === 'admin';

    // NEW: follow karana ayage ids tika card okkomata ekapaarak witharak (query ekak witharai)
    $followingIds = request()->attributes->get('followingIds');
    if ($followingIds === null) {
        $followingIds = $cardUser ? $cardUser->following()->pluck('users.id')->all() : [];
        request()->attributes->set('followingIds', $followingIds);
    }
    $cardIsFollowing = in_array($post->user_id, $followingIds);
@endphp

<div class="group relative rounded-xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-md">
    <div class="overflow-hidden rounded-t-xl">
        <img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}"
             class="h-44 w-full object-cover transition duration-300 group-hover:scale-105">
    </div>
    <div class="p-5">
        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700">
            {{ $post->category->name }}
        </span>
        <h3 class="mt-3 text-lg font-bold">
            {{-- after:absolute after:inset-0 = card eka muluwen click wenawa --}}
            <a href="{{ route('posts.show', $post) }}"
               class="after:absolute after:inset-0 group-hover:text-blue-600">{{ $post->title }}</a>
        </h3>
        <p class="mt-2 text-sm text-slate-500">
            {{ \Illuminate\Support\Str::limit(strip_tags($post->content), 110) }}
        </p>

        {{-- relative z-10 = meke buttons click karaddi post eka open wenne na --}}
        <div class="relative z-10 mt-4 flex items-center justify-between text-xs text-slate-500">
            <div class="flex flex-wrap items-center gap-2">
                <x-avatar :user="$post->user" size="h-6 w-6" text="text-[10px]" />
                <span class="font-medium text-slate-700">{{ $post->user->name }}</span>

                {{-- NEW: Follow / Unfollow --}}
                @if ($cardUser && $cardUser->id !== $post->user_id)
                    <form method="POST" action="{{ route('users.follow', $post->user) }}">
                        @csrf
                        <button type="submit"
                                class="rounded-full border px-2 py-0.5 text-[10px] font-semibold transition
                                       {{ $cardIsFollowing
                                            ? 'border-blue-200 bg-blue-100 text-blue-700 hover:border-red-200 hover:bg-red-50 hover:text-red-600'
                                            : 'border-blue-600 bg-blue-600 text-white hover:bg-blue-700' }}">
                            {{ $cardIsFollowing ? 'Following' : '+ Follow' }}
                        </button>
                    </form>
                @elseif (! $cardUser)
                    <a href="{{ route('login') }}" title="Login to follow"
                       class="rounded-full border border-blue-600 bg-blue-600 px-2 py-0.5 text-[10px] font-semibold text-white hover:bg-blue-700">
                        + Follow
                    </a>
                @endif

                <span>•</span>
                <span>{{ $post->created_at->format('M d, Y') }}</span>
                <span>•</span>

                {{-- Comments --}}
                <a href="{{ route('posts.show', $post) }}#comments"
                   class="flex items-center gap-1 hover:text-blue-600" title="Comments">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/>
                    </svg>
                    {{ $post->comments_count ?? 0 }}
                </a>

                <span>•</span>

                {{-- Like --}}
                @auth
                    <form method="POST" action="{{ route('posts.like', $post) }}">
                        @csrf
                        <button type="submit" title="{{ $isLiked ? 'Unlike' : 'Like' }}"
                                class="flex items-center gap-1 transition hover:text-red-500 {{ $isLiked ? 'text-red-500' : '' }}">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                                 fill="{{ $isLiked ? 'currentColor' : 'none' }}">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>
                            </svg>
                            {{ $post->liked_by_count ?? 0 }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" title="Login to like"
                       class="flex items-center gap-1 transition hover:text-red-500">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>
                        </svg>
                        {{ $post->liked_by_count ?? 0 }}
                    </a>
                @endauth
            </div>

            <div class="flex items-center gap-1">
                {{-- Bookmark --}}
                @auth
                    <form method="POST" action="{{ route('posts.bookmark', $post) }}">
                        @csrf
                        <button type="submit" title="{{ $isSaved ? 'Remove from saved' : 'Save post' }}"
                                class="rounded-full p-1.5 transition hover:bg-slate-100
                                       {{ $isSaved ? 'text-blue-600' : 'text-slate-400 hover:text-blue-600' }}">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                                 fill="{{ $isSaved ? 'currentColor' : 'none' }}">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z"/>
                            </svg>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" title="Login to save"
                       class="rounded-full p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-blue-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z"/>
                        </svg>
                    </a>
                @endauth

                {{-- Edit / Delete menu: admin (author) ට විතරයි, හැම post එකකම --}}
                @if ($cardIsAdmin)
                    <div x-data="{ open: false }" class="relative">
                        <button type="button" @click="open = !open" @click.outside="open = false"
                                class="rounded-full px-2 py-1 text-base leading-none tracking-widest text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                            •••
                        </button>

                        <div x-show="open" x-cloak
                             class="absolute bottom-full right-0 z-20 mb-1 w-40 rounded-lg border border-slate-200 bg-white py-1 shadow-lg">
                            <a href="{{ route('posts.edit', $post) }}"
                               class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Edit</a>

                            <form method="POST" action="{{ route('posts.destroy', $post) }}"
                                  data-confirm="This post will be moved to trash. You can restore it later from the Trash page."
                                  data-confirm-title="Move to trash?"
                                  data-confirm-button="Move to Trash">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">
                                    Move to Trash
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>