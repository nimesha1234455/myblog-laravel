@php
    $authUser = auth()->user();
    $isAdmin  = $authUser && $authUser->role === 'admin';

    // CHANGED: admin ta hama post ekakama, reader ta thamange post eke witharai (permission thiyenawa nam)
    $isOwner   = $authUser && $post->user_id === $authUser->id;
    $canEdit   = $authUser && ($isAdmin || ($isOwner && $authUser->can('posts.edit')));
    $canDelete = $authUser && ($isAdmin || ($isOwner && $authUser->can('posts.delete')));

    // NEW: meta fields purawala thibboth ewa, nathnam auto (content / category eken)
    $metaDesc     = $post->meta_description ?: $post->content;
    $metaKeywords = $post->meta_keywords ?: ($post->category->name . ', ' . $post->title . ', blog, myblog');
@endphp

<x-blog-layout
    :title="$post->title"
    :description="$metaDesc"
    :keywords="$metaKeywords"
    :image="$post->cover_image ? 'storage/' . $post->cover_image : null"
    type="article">
    <a href="{{ route('posts.index') }}" class="mb-4 inline-block text-sm text-slate-500 hover:text-blue-600">
        ← Back to Posts
    </a>

    <div class="grid gap-6 lg:grid-cols-4">
        <div class="space-y-6 lg:col-span-3">

            {{-- Post --}}
            <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

                {{-- CHANGED: zoom ain kala --}}
                @if ($post->cover_image)
                    <div class="relative overflow-hidden rounded-lg"
                         style="aspect-ratio:1200/630;max-height:420px;background:#0b1530;">
                        <img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}"
                             class="absolute inset-0 block h-full w-full object-cover"
                             decoding="async">
                    </div>
                @endif

                <span class="mt-4 inline-block rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700">
                    {{ $post->category->name }}
                </span>
                <h1 class="mt-3 text-3xl font-bold">{{ $post->title }}</h1>

                <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-slate-500">
                    <span class="font-medium text-slate-700">{{ $post->user->name }}</span>

                    {{-- NEW: Follow / Unfollow --}}
                    @if ($authUser && $authUser->id !== $post->user_id)
                        @php $isFollowing = $authUser->isFollowing($post->user); @endphp
                        <form method="POST" action="{{ route('users.follow', $post->user) }}">
                            @csrf
                            <button type="submit"
                                    class="rounded-full border px-3 py-0.5 text-xs font-semibold transition
                                           {{ $isFollowing
                                                ? 'border-blue-200 bg-blue-100 text-blue-700 hover:border-red-200 hover:bg-red-50 hover:text-red-600'
                                                : 'border-blue-600 bg-blue-600 text-white hover:bg-blue-700' }}">
                                {{ $isFollowing ? 'Following' : '+ Follow' }}
                            </button>
                        </form>
                    @elseif (! $authUser)
                        <a href="{{ route('login') }}" title="Login to follow"
                           class="rounded-full border border-blue-600 bg-blue-600 px-3 py-0.5 text-xs font-semibold text-white hover:bg-blue-700">
                            + Follow
                        </a>
                    @endif

                    <span>• {{ $post->created_at->format('M d, Y') }}</span>
                    <span>• {{ $readTime }} min read</span>
                </div>

                {{-- Like / Comments / Save --}}
                <div class="mt-4 flex flex-wrap items-center gap-3">

                    {{-- Like --}}
                    @auth
                        <form method="POST" action="{{ route('posts.like', $post) }}">
                            @csrf
                            <button type="submit"
                                    class="flex items-center gap-2 rounded-full border px-4 py-1.5 text-sm font-medium transition
                                           {{ $liked ? 'border-red-200 bg-red-100 text-red-600' : 'border-slate-200 text-slate-600 hover:border-red-200 hover:text-red-500' }}">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                                     fill="{{ $liked ? 'currentColor' : 'none' }}">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>
                                </svg>
                                {{ $liked ? 'Liked' : 'Like' }} · {{ $likesCount }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" title="Login to like"
                           class="flex items-center gap-2 rounded-full border border-slate-200 px-4 py-1.5 text-sm font-medium text-slate-600 transition hover:border-red-200 hover:text-red-500">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>
                            </svg>
                            Like · {{ $likesCount }}
                        </a>
                    @endauth

                    {{-- Comments --}}
                    <a href="#comments"
                       class="flex items-center gap-2 rounded-full border border-slate-200 px-4 py-1.5 text-sm font-medium text-slate-600 transition hover:border-blue-200 hover:text-blue-600">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/>
                        </svg>
                        Comments · {{ $comments->count() }}
                    </a>

                    {{-- Save --}}
                    @auth
                        <form method="POST" action="{{ route('posts.bookmark', $post) }}">
                            @csrf
                            <button type="submit"
                                    class="flex items-center gap-2 rounded-full border px-4 py-1.5 text-sm font-medium transition
                                           {{ $bookmarked ? 'border-blue-200 bg-blue-100 text-blue-700' : 'border-slate-200 text-slate-600 hover:border-blue-200 hover:text-blue-600' }}">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                                     fill="{{ $bookmarked ? 'currentColor' : 'none' }}">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z"/>
                                </svg>
                                {{ $bookmarked ? 'Saved' : 'Save' }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" title="Login to save"
                           class="flex items-center gap-2 rounded-full border border-slate-200 px-4 py-1.5 text-sm font-medium text-slate-600 transition hover:border-blue-200 hover:text-blue-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z"/>
                            </svg>
                            Save
                        </a>
                    @endauth
                </div>

                <div class="mt-6 space-y-4 leading-relaxed text-slate-700
                            [&_h1]:text-2xl [&_h1]:font-bold [&_h2]:text-xl [&_h2]:font-bold [&_h3]:text-lg [&_h3]:font-bold
                            [&_ul]:list-disc [&_ul]:pl-6 [&_ol]:list-decimal [&_ol]:pl-6
                            [&_a]:text-blue-600 [&_a]:underline
                            [&_blockquote]:border-l-4 [&_blockquote]:border-slate-300 [&_blockquote]:pl-4 [&_blockquote]:italic">
                    {!! \Mews\Purifier\Facades\Purifier::clean($post->content, 'blog') !!}
                </div>
            </article>

            {{-- Comments --}}
            <section id="comments" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-bold">Comments ({{ $comments->count() }})</h2>

                @auth
                    <form method="POST" action="{{ route('comments.store', $post) }}" class="mt-4">
                        @csrf
                        <textarea name="body" rows="3" placeholder="Write a comment..."
                                  class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('body') }}</textarea>
                        @error('body')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="mt-2 flex justify-end">
                            <button type="submit"
                                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                Post Comment
                            </button>
                        </div>
                    </form>
                @else
                    <div class="mt-4 rounded-lg border border-dashed border-slate-300 p-4 text-center text-sm text-slate-500">
                        <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:underline">Login</a>
                        @if (\Illuminate\Support\Facades\Route::has('register'))
                            or
                            <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:underline">Register</a>
                        @endif
                        to join the conversation.
                    </div>
                @endauth

                <div class="mt-6 space-y-5">
                    @forelse ($comments as $comment)
                        <div class="flex gap-3">
                            <x-avatar :user="$comment->user" size="h-9 w-9" />

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="text-sm">
                                        <span class="font-semibold text-slate-800">{{ $comment->user->name }}</span>
                                        @if ($comment->user_id === $post->user_id)
                                            <span class="ml-1 rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium text-slate-600">Author</span>
                                        @endif
                                        <span class="ml-2 text-xs text-slate-400">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>

                                    {{-- Comment එකේ අයිතිකාරයාට, post එකේ author ට, නැත්නම් admin ට --}}
                                    @if ($authUser && ($comment->user_id === $authUser->id || $post->user_id === $authUser->id || $isAdmin))
                                        <form method="POST" action="{{ route('comments.destroy', $comment) }}"
                                              data-confirm="This comment will be deleted permanently."
                                              data-confirm-title="Delete comment?"
                                              data-confirm-button="Delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-500 hover:underline">Delete</button>
                                        </form>
                                    @endif
                                </div>

                                <p class="mt-1 whitespace-pre-line break-words text-sm text-slate-700">{{ $comment->body }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm text-slate-400">No comments yet. Be the first to comment!</p>
                    @endforelse
                </div>
            </section>
        </div>

        <aside class="space-y-4">
            {{-- CHANGED: Edit / Delete: admin ta hama post ekakama, reader ta thamange post eke witharai (permission thiyenawa nam) --}}
            @if ($canEdit || $canDelete)
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="mb-3 font-bold">Actions</h3>

                    @if ($canEdit)
                        <a href="{{ route('posts.edit', $post) }}"
                           class="block rounded-lg bg-blue-600 px-4 py-2 text-center text-sm font-medium text-white hover:bg-blue-700">
                            Edit Post
                        </a>
                    @endif

                    @if ($canDelete)
                        <form method="POST" action="{{ route('posts.destroy', $post) }}" class="mt-3"
                              data-confirm="This post will be moved to trash. You can restore it later from the Trash page."
                              data-confirm-title="Move to trash?"
                              data-confirm-button="Move to Trash">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full rounded-lg bg-red-500 px-4 py-2 text-sm font-medium text-white hover:bg-red-600">
                                Move to Trash
                            </button>
                        </form>
                    @endif
                </div>
            @endif

            <div class="rounded-xl border border-slate-200 bg-white p-5 text-sm shadow-sm">
                <h3 class="mb-3 font-bold">Post Info</h3>
                <p class="text-slate-500">Category</p>
                <p class="mb-3 font-medium">{{ $post->category->name }}</p>
                <p class="text-slate-500">Created At</p>
                <p class="mb-3 font-medium">{{ $post->created_at->format('M d, Y h:i A') }}</p>
                <p class="text-slate-500">Updated At</p>
                <p class="font-medium">{{ $post->updated_at->format('M d, Y h:i A') }}</p>
            </div>
        </aside>
    </div>
</x-blog-layout>