<x-blog-layout title="Trash">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Trash</h1>
        <p class="text-sm text-slate-500">Posts you moved to trash</p>
    </div>

    @if ($posts->count())
        <div class="space-y-4">
            @foreach ($posts as $post)
                <div class="flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <img src="{{ asset('storage/' . $post->cover_image) }}" class="h-20 w-28 rounded-lg object-cover">

                    <div class="min-w-0 flex-1">
                        <h3 class="truncate font-bold">{{ $post->title }}</h3>
                        <p class="text-xs text-slate-500">
                            {{ $post->category->name }} · Deleted {{ $post->deleted_at->diffForHumans() }}
                        </p>
                    </div>

                    {{-- CHANGED: $post->id වෙනුවට $post->slug --}}
                    <form method="POST" action="{{ route('posts.restore', $post->slug) }}">
                        @csrf
                        @method('PATCH')
                        <button class="rounded-lg bg-green-600 px-3 py-2 text-sm font-medium text-white hover:bg-green-700">
                            Restore
                        </button>
                    </form>

                    {{-- CHANGED: $post->id වෙනුවට $post->slug --}}
                    <form method="POST" action="{{ route('posts.force', $post->slug) }}"
                          data-confirm="This post will be deleted permanently. This cannot be undone."
                          data-confirm-title="Delete permanently?"
                          data-confirm-button="Delete Forever">
                        @csrf
                        @method('DELETE')
                        <button class="rounded-lg bg-red-500 px-3 py-2 text-sm font-medium text-white hover:bg-red-600">
                            Delete Forever
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
        <div class="mt-8">{{ $posts->links() }}</div>
    @else
        <p class="rounded-lg bg-white p-10 text-center text-slate-500">Trash is empty.</p>
    @endif
</x-blog-layout>