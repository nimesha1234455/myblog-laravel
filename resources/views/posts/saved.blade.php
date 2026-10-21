<x-blog-layout title="Saved Posts">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Saved Posts</h1>
        <p class="text-sm text-slate-500">Posts you bookmarked to read later</p>
    </div>

    @if ($posts->count())
        <div class="grid gap-6 md:grid-cols-2">
            @foreach ($posts as $post)
                @include('posts._card', ['post' => $post])
            @endforeach
        </div>
        <div class="mt-8">{{ $posts->links() }}</div>
    @else
        <div class="rounded-xl border border-slate-200 bg-white p-10 text-center shadow-sm">
            <p class="text-4xl">📑</p>
            <p class="mt-3 font-medium text-slate-700">No saved posts yet</p>
            <p class="mt-1 text-sm text-slate-500">Click the bookmark icon on any post to save it here.</p>
            <a href="{{ route('posts.index') }}"
               class="mt-5 inline-block rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Browse Posts
            </a>
        </div>
    @endif
</x-blog-layout>