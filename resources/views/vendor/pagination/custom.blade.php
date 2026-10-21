@if ($paginator->hasPages())
<nav class="flex items-center gap-2" role="navigation" aria-label="Pagination">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span class="rounded-full border border-slate-700 bg-slate-800 px-4 py-2 text-sm text-slate-500 opacity-50">‹ Prev</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="rounded-full border border-slate-700 bg-slate-800 px-4 py-2 text-sm text-slate-200 transition hover:bg-slate-700">‹ Prev</a>
    @endif

    {{-- Page numbers --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="px-2 text-slate-500">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="rounded-full border border-slate-700 bg-slate-800 px-4 py-2 text-sm text-slate-200 transition hover:bg-slate-700">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="rounded-full border border-slate-700 bg-slate-800 px-4 py-2 text-sm text-slate-200 transition hover:bg-slate-700">Next ›</a>
    @else
        <span class="rounded-full border border-slate-700 bg-slate-800 px-4 py-2 text-sm text-slate-500 opacity-50">Next ›</span>
    @endif
</nav>
@endif