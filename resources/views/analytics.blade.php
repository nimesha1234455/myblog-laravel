<x-blog-layout title="Analytics">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Analytics</h1>
        <p class="text-sm text-slate-500">How your published posts are performing</p>
    </div>

    {{-- Stat cards --}}
    @php
        $cards = [
            ['Total Views',    $totals['views'],    'bg-purple-600'],
            ['Total Likes',    $totals['likes'],    'bg-red-500'],
            ['Total Comments', $totals['comments'], 'bg-blue-600'],
            ['Total Saves',    $totals['saves'],    'bg-emerald-500'],
        ];
        $maxViews = max(1, $topPosts->max('views') ?? 0);
        $maxCat   = max(1, $categories->max('views') ?? 0);
    @endphp

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ($cards as [$label, $value, $color])
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 shrink-0 rounded-lg {{ $color }}"></div>
                    <div>
                        <p class="text-sm text-slate-500">{{ $label }}</p>
                        <p class="text-2xl font-bold">{{ number_format($value) }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">

        {{-- Top posts (bar chart) --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="mb-1 font-bold">Top Posts by Views</h2>
            <p class="mb-5 text-xs text-slate-500">Your 5 most viewed posts</p>

            @forelse ($topPosts as $post)
                <a href="{{ route('posts.show', $post) }}" class="mb-4 block last:mb-0">
                    <div class="mb-1 flex items-center justify-between text-sm">
                        <span class="truncate pr-4 font-medium hover:text-blue-600">{{ $post->title }}</span>
                        <span class="shrink-0 text-slate-500">👁 {{ number_format($post->views) }}</span>
                    </div>
                    <div class="h-2.5 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-blue-600"
                             style="width: {{ round($post->views / $maxViews * 100) }}%"></div>
                    </div>
                </a>
            @empty
                <p class="py-8 text-center text-sm text-slate-500">No published posts yet.</p>
            @endforelse
        </div>

        {{-- Category breakdown --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-1 font-bold">By Category</h2>
            <p class="mb-5 text-xs text-slate-500">Posts and views per category</p>

            @forelse ($categories as $cat)
                <div class="mb-4 last:mb-0">
                    <div class="mb-1 flex items-center justify-between text-sm">
                        <span class="font-medium">{{ $cat['name'] }}</span>
                        <span class="text-xs text-slate-500">
                            {{ $cat['posts'] }} {{ \Illuminate\Support\Str::plural('post', $cat['posts']) }}
                            · {{ number_format($cat['views']) }} views
                        </span>
                    </div>
                    <div class="h-2.5 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-purple-600"
                             style="width: {{ round($cat['views'] / $maxCat * 100) }}%"></div>
                    </div>
                </div>
            @empty
                <p class="py-8 text-center text-sm text-slate-500">No data yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Per-post table --}}
    <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4 font-bold">All Published Posts</div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Post</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3 text-right">Views</th>
                        <th class="px-6 py-3 text-right">Likes</th>
                        <th class="px-6 py-3 text-right">Comments</th>
                        <th class="px-6 py-3 text-right">Saves</th>
                    </tr>
                </thead>
                <tbody>
                    @php $rows = $topPosts; @endphp
                    @forelse ($rows as $post)
                        <tr class="border-t border-slate-100">
                            <td class="px-6 py-3 font-medium">
                                <a href="{{ route('posts.show', $post) }}" class="hover:text-blue-600">{{ $post->title }}</a>
                            </td>
                            <td class="px-6 py-3 text-slate-500">{{ $post->category->name }}</td>
                            <td class="px-6 py-3 text-right">{{ number_format($post->views) }}</td>
                            <td class="px-6 py-3 text-right">{{ $post->liked_by_count }}</td>
                            <td class="px-6 py-3 text-right">{{ $post->comments_count }}</td>
                            <td class="px-6 py-3 text-right">{{ $post->bookmarked_by_count }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-slate-500">No published posts yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-blog-layout>