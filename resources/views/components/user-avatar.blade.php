@props(['size' => 'md', 'user' => null])

@php
$sizeClasses = [
    'xs' => 'h-6 w-6',
    'sm' => 'h-8 w-8',
    'md' => 'h-10 w-10',
    'lg' => 'h-12 w-12',
    'xl' => 'h-16 w-16',
    '2xl' => 'h-20 w-20',
];

$user = $user ?? auth()->user();
$avatarUrl = $user->avatar_url;
@endphp

<img src="{{ $avatarUrl }}"
     alt="{{ $user->name }}"
     {{ $attributes->merge(['class' => $sizeClasses[$size] . ' rounded-full object-cover']) }}>
