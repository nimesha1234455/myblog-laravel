<x-blog-layout title="Drafts">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">Drafts</h1>
            <p class="text-sm text-slate-500">Posts you haven't published yet</p>
        </div>
        <a href="{{ route('posts.create') }}"
           class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
            + Add New Post
        </a>
    </div>

    @if ($posts->count())
        <div class="grid gap-6 md:grid-cols-2">
            @foreach ($posts as $post)
                @include('posts._card', ['post' => $post])
            @endforeach
        </div>

        @if ($posts->hasPages())
            <div class="mt-10 flex justify-center">
                {{ $posts->links('vendor.pagination.custom') }}
            </div>
        @endif
    @else
        <p class="rounded-lg bg-white p-10 text-center text-slate-500">No drafts yet.</p>
    @endif
</x-blog-layout>