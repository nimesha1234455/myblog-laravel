<x-blog-layout title="Home">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">Latest Posts</h1>
            <p class="text-sm text-slate-500">Discover and read the latest articles</p>
        </div>

        {{-- Admin (blog owner) ta witharai --}}
        @if (auth()->check() && auth()->user()->role === 'admin')
            <a href="{{ route('posts.create') }}"
               class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                + Add New Post
            </a>
        @endif
    </div>

    {{-- Category filter --}}
    @php
        $activeCategory = request('category');
        $chip     = 'rounded-full px-4 py-1.5 text-sm font-medium transition';
        $on       = 'bg-blue-600 text-white shadow-sm';
        $off      = 'border border-slate-200 bg-white text-slate-600 hover:border-blue-300 hover:text-blue-600';
    @endphp
    <div class="mb-6 flex flex-wrap items-center gap-2">
        <a href="{{ route('posts.index', array_filter(['search' => request('search')])) }}"
           class="{{ $chip }} {{ $activeCategory ? $off : $on }}">
            All
        </a>

        @foreach ($categories as $category)
            <a href="{{ route('posts.index', array_filter(['search' => request('search'), 'category' => $category->id])) }}"
               class="{{ $chip }} {{ (string) $activeCategory === (string) $category->id ? $on : $off }}">
                {{ $category->name }}
            </a>
        @endforeach
    </div>

    @if ($posts->count())
        <div class="grid gap-6 md:grid-cols-2">
            @foreach ($posts as $post)
                @include('posts._card', ['post' => $post])
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($posts->hasPages())
            <div class="mt-10 flex justify-center">
                {{ $posts->links('vendor.pagination.custom') }}
            </div>
        @endif
    @else
        <p class="rounded-lg bg-white p-10 text-center text-slate-500">
            @if ($activeCategory)
                No posts found in this category.
            @else
                No posts found.
            @endif
        </p>
    @endif
</x-blog-layout>