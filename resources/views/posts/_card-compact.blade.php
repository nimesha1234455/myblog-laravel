@php
    $cat = $post->category->name ?? 'General';
    $palette = [
        'Technology'      => '#2563eb',
        'Programming'     => '#6366f1',
        'Web Development' => '#3b82f6',
        'Lifestyle'       => '#ec4899',
        'Food'            => '#22c55e',
        'Travel'          => '#0d9488',
        'Productivity'    => '#f59e0b',
        'Personal Growth' => '#f59e0b',
        'Motivation'      => '#a855f7',
    ];
    $badge = $palette[$cat] ?? '#4f46e5';
@endphp

<article class="pcard group">
    {{-- Image + category badge --}}
    <a href="{{ route('posts.show', $post) }}" class="pcard-img" aria-label="{{ $post->title }}">
        @if ($post->cover_image)
            <img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}">
        @endif
        <span class="pcard-badge" style="background:{{ $badge }};">{{ $cat }}</span>
    </a>

    {{-- Content --}}
    <div class="pcard-body">
        <h3 class="pcard-title t1">
            <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
        </h3>

        <p class="pcard-excerpt t2">
            {{ \Illuminate\Support\Str::limit(strip_tags($post->content), 90) }}
        </p>

        <div class="pcard-foot">
            <span class="pcard-meta t2">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="16" rx="2"/><path stroke-linecap="round" d="M3 10h18M8 3v4M16 3v4"/></svg>
                {{ $post->created_at->format('M d, Y') }}
            </span>

            <span class="pcard-meta t2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12S6 5 12 5s9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7z"/><circle cx="12" cy="12" r="3"/></svg>
                {{ number_format((int) $post->views) }}
            </span>

            <a href="{{ route('posts.show', $post) }}" class="pcard-btn">Read more →</a>
        </div>
    </div>
</article>