@props([
    'user' => null,
    'showCenter' => false,
    'size' => 'sm', // sm | md
])

@php
    $photoUrl = $user?->photoUrl();
    $initial = $user?->name ? mb_strtoupper(mb_substr($user->name, 0, 1)) : '?';
    $sizeClass = $size === 'md' ? 'doctor-chip--md' : 'doctor-chip--sm';
@endphp

@if($user)
<div {{ $attributes->class(['doctor-chip', $sizeClass]) }}>
    @if($photoUrl)
        <img src="{{ $photoUrl }}" alt="" class="doctor-chip-avatar">
    @else
        <span class="doctor-chip-avatar doctor-chip-avatar--placeholder" aria-hidden="true">{{ $initial }}</span>
    @endif
    <div class="doctor-chip-meta">
        <div class="doctor-chip-name">{{ $user->name }}</div>
        @if($showCenter && $user->center)
            <div class="doctor-chip-center">{{ $user->center->name }}</div>
        @endif
    </div>
</div>
@else
    <span class="text-muted">—</span>
@endif
