<x-blog-layout title="My Posts">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">My Posts</h1>
        <p class="text-sm text-slate-500">Posts you have written</p>
    </div>

    @if ($posts->count())
        <div class="grid gap-6 md:grid-cols-2">
            @foreach ($posts as $post)
                {{-- NEW: status badge eka card eka udin penna wrapper ekk damma (_card.blade.php වෙනස් කරන්න ඕන නැහැ) --}}
                <div class="relative">
                    @if ($post->status === 'pending')
                        <span class="pointer-events-none absolute left-3 top-3 z-10 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800 shadow">
                            Pending approval
                        </span>
                    @else
                        <span class="pointer-events-none absolute left-3 top-3 z-10 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800 shadow">
                            Published
                        </span>
                    @endif

                    @include('posts._card', ['post' => $post])
                </div>
            @endforeach
        </div>
        <div class="mt-8">{{ $posts->links() }}</div>
    @else
        <p class="rounded-lg bg-white p-10 text-center text-slate-500">You haven't written any posts yet.</p>
    @endif
</x-blog-layout>