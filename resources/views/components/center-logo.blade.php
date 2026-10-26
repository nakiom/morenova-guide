@props([
    'center' => null,
    'size' => 'md', // sm | md | lg
    'showName' => false,
])

@php
    $center ??= auth()->user()?->center;
    $logoUrl = $center?->logoUrl();
    $sizeClass = match ($size) {
        'sm' => 'center-logo--sm',
        'lg' => 'center-logo--lg',
        default => 'center-logo--md',
    };
@endphp

@if($center && $logoUrl)
<div {{ $attributes->class(['center-logo-wrap', $sizeClass]) }}>
    <img
        src="{{ $logoUrl }}"
        alt="{{ $center->name }}"
        class="center-logo"
        title="{{ $center->name }}"
    >
    @if($showName)
        <span class="center-logo-name">{{ $center->name }}</span>
    @endif
</div>
@endif
