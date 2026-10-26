@props([
    'align' => 'right',
    'width' => '48',
    'contentClasses' => 'py-1 bg-white dark:bg-gray-700',
    'panelClass' => '',
])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$width = match ($width) {
    '48' => 'w-48',
    'user' => 'user-menu-w',
    default => $width,
};
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open" :class="{ 'is-open': open }">
        {{ $trigger }}
    </div>

    <div x-show="open"
            x-transition:enter="user-menu-enter"
            x-transition:enter-start="user-menu-enter-start"
            x-transition:enter-end="user-menu-enter-end"
            x-transition:leave="user-menu-leave"
            x-transition:leave-start="user-menu-leave-start"
            x-transition:leave-end="user-menu-leave-end"
            class="absolute z-50 mt-2 {{ $width }} {{ $alignmentClasses }} {{ $panelClass }}"
            style="display: none;"
            @click="open = false">
        <div class="{{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
