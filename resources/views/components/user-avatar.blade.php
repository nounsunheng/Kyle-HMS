@props(['size' => 'md', 'user' => null])

@php
$user = $user ?? auth()->user();
$sizes = [
    'xs' => 'w-8 h-8',
    'sm' => 'w-10 h-10',
    'md' => 'w-12 h-12',
    'lg' => 'w-16 h-16',
    'xl' => 'w-24 h-24',
    '2xl' => 'w-32 h-32',
];
$sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<img src="{{ $user->avatar_url }}"
     alt="{{ $user->name }}"
     {{ $attributes->merge(['class' => "{$sizeClass} rounded-full object-cover"]) }}>
