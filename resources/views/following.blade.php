@php
    $authUser = auth()->user();
@endphp

<x-blog-layout title="Following" description="Latest posts from the writers you follow on MyBlog.">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Following</h1>
        <p class="text-sm text-slate-500">Latest posts from the writers you follow</p>
    </div>

    @if ($posts->count())
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($posts as $post)
                <article class="flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md">
                    <a href="{{ route('posts.show', $post) }}" class="block">
                        @if ($post->cover_image)
                            <img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}"
                                 class="h-44 w-full object-cover" loading="lazy">
                        @else
                            <div class="flex h-44 w-full items-center justify-center bg-slate-100 text-4xl">📝</div>
                        @endif
                    </a>

                    <div class="flex flex-1 flex-col p-4">
                        <span class="w-fit rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700">
                            {{ $post->category->name }}
                        </span>

                        <a href="{{ route('posts.show', $post) }}"
                           class="mt-2 line-clamp-2 text-lg font-bold text-slate-900 hover:text-blue-600">
                            {{ $post->title }}
                        </a>

                        <p class="mt-1 line-clamp-2 text-sm text-slate-500">
                            {{ \Illuminate\Support\Str::limit(trim(strip_tags($post->content)), 110) }}
                        </p>

                        <div class="mt-auto flex items-center justify-between pt-4 text-xs text-slate-500">
                            <div class="flex items-center gap-2">
                                <x-avatar :user="$post->user" size="h-6 w-6" />
                                <span class="font-medium text-slate-700">{{ $post->user->name }}</span>
                            </div>
                            <span>{{ $post->created_at->format('M d, Y') }}</span>
                        </div>

                        <div class="mt-3 flex items-center gap-4 border-t pt-3 text-xs text-slate-500">
                            <span>❤️ {{ $post->liked_by_count }}</span>
                            <span>💬 {{ $post->comments_count }}</span>
                            <a href="{{ route('posts.show', $post) }}"
                               class="ml-auto font-semibold text-blue-600 hover:underline">Read more →</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    @else
        {{-- Empty state --}}
        <div class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center">
            <div class="text-4xl">👥</div>
            <h2 class="mt-3 text-lg font-bold text-slate-900">
                {{ $authUser->following()->exists() ? 'No new posts yet' : "You're not following anyone yet" }}
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                {{ $authUser->following()->exists()
                    ? 'The writers you follow have not published any posts yet.'
                    : 'Follow writers to see their latest posts here.' }}
            </p>
            <a href="{{ route('posts.index') }}"
               class="mt-4 inline-block rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Browse all posts
            </a>
        </div>

        {{-- Suggested writers --}}
        @if ($suggested->count())
            <div class="mt-8">
                <h2 class="mb-3 text-lg font-bold text-slate-900">Suggested writers</h2>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($suggested as $writer)
                        <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="flex min-w-0 items-center gap-3">
                                <x-avatar :user="$writer" size="h-10 w-10" />
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-semibold text-slate-900">{{ $writer->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $writer->posts_count }} {{ \Illuminate\Support\Str::plural('post', $writer->posts_count) }}</div>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('users.follow', $writer) }}">
                                @csrf
                                <button type="submit"
                                        class="rounded-full bg-blue-600 px-3 py-1 text-xs font-semibold text-white hover:bg-blue-700">
                                    + Follow
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</x-blog-layout>