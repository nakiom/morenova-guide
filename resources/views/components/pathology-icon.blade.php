@props(['pathology', 'class' => 'pathology-icon-img', 'size' => null])

@php
    $url = $pathology->iconAssetUrl();
    $style = $size ? "width:{$size};height:{$size};" : null;
@endphp

@if($url)
    <img src="{{ $url }}" alt="{{ $pathology->slug }}" class="{{ $class }}" @if($style) style="{{ $style }}" @endif>
@elseif($pathology->icon)
    <i class="{{ $pathology->icon }} {{ $class === 'pathology-icon-img' ? 'pathology-icon' : $class }}"></i>
@endif
