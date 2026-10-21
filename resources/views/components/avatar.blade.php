@props(['user', 'size' => 'h-9 w-9', 'text' => 'text-sm'])

@if ($user->avatar)
    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
         {{ $attributes->merge(['class' => "$size shrink-0 rounded-full object-cover"]) }}>
@else
    <div {{ $attributes->merge(['class' => "$size $text flex shrink-0 items-center justify-center rounded-full bg-blue-600 font-semibold text-white"]) }}>
        {{ strtoupper(substr($user->name, 0, 1)) }}
    </div>
@endif