<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? 'MoreNova' }}</title>
        <link rel="icon" type="image/x-icon" href="https://direxgroup.com/favicon.ico">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="guest-shell d-flex flex-column align-items-center py-4">
        <div class="mb-3 guest-logo text-center">
            @if($centerLogoUrl)
                <img src="{{ $centerLogoUrl }}"
                     alt="{{ $centerName ?: 'Centro' }}"
                     class="d-block mx-auto"
                     style="max-width: 180px; max-height: 80px; object-fit: contain;" />
                @if($centerName)
                    <div class="small text-muted mt-2">{{ $centerName }}</div>
                @endif
            @else
                <img src="{{ asset('img/dema-transparent-small.png') }}" alt="Direx Logo" class="d-block mx-auto" style="width: 64px; height: 64px; object-fit: contain;" />
            @endif
        </div>

        <div class="w-100 px-3" style="max-width: 44rem;">
            <div class="card guest-card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
