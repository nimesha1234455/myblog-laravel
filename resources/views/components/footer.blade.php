@php
    // WhatsApp number eka methan danna: 94 + number (mula 0 nathiw). ex: 94771234567
    // hiswa thibunoth WhatsApp icon eka pennana na
    $whatsappNumber = '94775747354';

    // oyage links methana vitharak wens karanne
    $socials = [
        [
            'name'  => 'WhatsApp',
            'url'   => 'https://wa.me/' . $whatsappNumber,
            'color' => 'hover:bg-green-500',
            'icon'  => 'M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.03-.52-.07-.15-.67-1.61-.92-2.2-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.21 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.08 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35zM12.05 21.5h-.01a9.45 9.45 0 0 1-4.82-1.32l-.35-.2-3.58.94.96-3.49-.22-.36a9.44 9.44 0 0 1-1.45-5.03c0-5.21 4.24-9.45 9.46-9.45 2.52 0 4.9.99 6.68 2.77a9.4 9.4 0 0 1 2.76 6.69c0 5.21-4.24 9.45-9.45 9.45zM20.52 3.45A11.38 11.38 0 0 0 12.05 0C5.73 0 .59 5.14.59 11.46c0 2.02.53 3.99 1.53 5.73L.5 23.5l6.46-1.69a11.44 11.44 0 0 0 5.09 1.2h.01c6.32 0 11.46-5.14 11.46-11.46 0-3.06-1.19-5.94-3.35-8.1z',
        ],
        [
            'name'  => 'LinkedIn',
            'url'   => 'https://www.linkedin.com/in/nimesha-madushani-bb5263371/',
            'color' => 'hover:bg-blue-600',
            'icon'  => 'M20.45 20.45h-3.56v-5.57c0-1.33-.03-3.04-1.85-3.04-1.86 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.06 2.06 0 1 1 0-4.12 2.06 2.06 0 0 1 0 4.12zM7.12 20.45H3.56V9h3.56v11.45z',
        ],
        [
            'name'  => 'Facebook',
            'url'   => 'https://www.facebook.com/share/18cGgfqiFS/',
            'color' => 'hover:bg-blue-500',
            'icon'  => 'M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.78-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0 0 22 12z',
        ],
        [
            'name'  => 'GitHub',
            'url'   => 'https://github.com/nimesha1234455',
            'color' => 'hover:bg-slate-700',
            'icon'  => 'M12 .3a12 12 0 0 0-3.8 23.38c.6.12.83-.26.83-.57L9 21.07c-3.34.72-4.04-1.61-4.04-1.61-.55-1.39-1.34-1.76-1.34-1.76-1.08-.74.09-.73.09-.73 1.2.09 1.83 1.24 1.83 1.24 1.07 1.83 2.81 1.3 3.5 1 .1-.78.42-1.31.76-1.61-2.67-.3-5.47-1.33-5.47-5.93 0-1.31.47-2.38 1.24-3.22-.14-.3-.54-1.52.1-3.18 0 0 1-.32 3.3 1.23a11.5 11.5 0 0 1 6 0c2.28-1.55 3.29-1.23 3.29-1.23.64 1.66.24 2.88.12 3.18a4.65 4.65 0 0 1 1.23 3.22c0 4.61-2.8 5.63-5.48 5.92.42.36.81 1.1.81 2.22l-.01 3.29c0 .31.2.69.82.57A12 12 0 0 0 12 .3z',
        ],
    ];

    $socials = array_values(array_filter($socials, fn ($s) => $s['url'] !== 'https://wa.me/'));
@endphp

<footer class="mt-16 border-t border-white/10 bg-white/70 backdrop-blur-md dark:bg-slate-900/70">
    <div class="mx-auto flex max-w-7xl flex-col items-center gap-6 px-6 py-8 md:flex-row md:justify-between">

        {{-- Brand --}}
        <div class="text-center md:text-left">
            <a href="{{ route('posts.index') }}" class="text-xl font-bold text-slate-800 dark:text-white">MyBlog</a>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Write, share and get inspired.</p>
        </div>

        {{-- Social icons --}}
        <div class="flex items-center gap-3">
            @foreach ($socials as $social)
                <a href="{{ $social['url'] }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   aria-label="{{ $social['name'] }}"
                   title="{{ $social['name'] }}"
                   class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-200 text-slate-700 transition hover:text-white dark:bg-white/10 dark:text-slate-300 {{ $social['color'] }}">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="{{ $social['icon'] }}" />
                    </svg>
                </a>
            @endforeach
        </div>

        {{-- Copyright --}}
        <p class="text-sm text-slate-600 dark:text-slate-400">
            &copy; {{ date('Y') }} MyBlog. All rights reserved.
        </p>
    </div>
</footer>