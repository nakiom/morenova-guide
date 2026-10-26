<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>MoreNova-Initia by DemaTech</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="https://direxgroup.com/favicon.ico">
        <link rel="shortcut icon" type="image/x-icon" href="https://direxgroup.com/favicon.ico">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="guest-shell d-flex flex-column justify-content-center align-items-center">
        <div class="mb-4 guest-logo">
            <a href="/">
                <img src="{{ asset('img/dema-transparent-small.png') }}" alt="Direx Logo" class="d-block" style="width: 80px; height: 80px; object-fit: contain;" />
            </a>
        </div>

        <div class="w-100 px-3" style="max-width: 34rem;">
            <div class="card guest-card border-0">
                <div class="card-body p-4">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
