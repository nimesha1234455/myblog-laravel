<x-blog-layout title="Pending Posts">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Pending Posts</h1>
        <p class="text-sm text-slate-500">Readers submitted these posts. Approve to publish, or reject to send back as a draft.</p>
    </div>

    @if ($posts->count())
        <div class="space-y-4">
            @foreach ($posts as $post)
                <div class="flex flex-col gap-4 rounded-lg bg-white p-4 shadow-sm sm:flex-row">
                    @if ($post->cover_image)
                        <img src="{{ asset('storage/' . $post->cover_image) }}"
                             alt="{{ $post->title }}"
                             class="h-40 w-full rounded-md object-cover sm:h-32 sm:w-48 sm:shrink-0">
                    @endif

                    <div class="flex min-w-0 flex-1 flex-col">
                        <h2 class="text-lg font-semibold">{{ $post->title }}</h2>

                        <p class="mt-1 text-sm text-slate-500">
                            By {{ $post->user->name ?? 'Unknown' }}
                            @if ($post->category)
                                &middot; {{ $post->category->name }}
                            @endif
                            &middot; {{ $post->created_at->diffForHumans() }}
                        </p>

                        <p class="mt-2 line-clamp-2 text-sm text-slate-600">
                            {{ \Illuminate\Support\Str::limit(strip_tags($post->content ?? $post->body ?? ''), 160) }}
                        </p>

                        {{-- route(..., $post) දාන නිසා URL එකේ slug එක ඉබේම එනවා (Post::getRouteKeyName) --}}
                        <div class="mt-auto flex flex-wrap items-center gap-2 pt-4">
                            <a href="{{ route('posts.show', $post) }}"
                               class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium hover:bg-slate-50">
                                Preview
                            </a>

                            <form method="POST" action="{{ route('posts.approve', $post) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700">
                                    Approve
                                </button>
                            </form>

                            <form method="POST" action="{{ route('posts.reject', $post) }}"
                                  data-confirm="This post will be sent back to the reader as a draft."
                                  data-confirm-title="Reject this post?"
                                  data-confirm-button="Reject">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700">
                                    Reject
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if (method_exists($posts, 'links'))
            <div class="mt-6">{{ $posts->links() }}</div>
        @endif
    @else
        <div class="rounded-lg bg-white p-10 text-center shadow-sm">
            <p class="text-slate-500">No posts are waiting for approval 🎉</p>
        </div>
    @endif
</x-blog-layout>