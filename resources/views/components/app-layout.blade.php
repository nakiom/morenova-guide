@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title . ' - ' : '' }}MoreNova-Direx</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="https://direxgroup.com/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="https://direxgroup.com/favicon.ico">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm flex-shrink-0 app-navbar" id="app-navbar">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                Morenova Guide
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">{{ __('messages.welcome') }}</a>
                    </li>
                    @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('clinical.patients.index') }}">{{ __('messages.clinical_patients') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('clinical.upcoming.*') ? 'active' : '' }}" href="{{ route('clinical.upcoming.index') }}">{{ __('messages.upcoming_visits') }}</a>
                    </li>
                    @endauth
                    <li class="nav-item dropdown language-selector">
                        <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-globe"></i> {{ strtoupper(app()->getLocale()) }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                            <li>
                                <a class="dropdown-item {{ app()->getLocale() === 'es' ? 'active' : '' }}" href="{{ route('locale.switch', 'es') }}">Español</a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ route('locale.switch', 'en') }}">English</a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ app()->getLocale() === 'pt' ? 'active' : '' }}" href="{{ route('locale.switch', 'pt') }}">Português</a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ app()->getLocale() === 'zh' ? 'active' : '' }}" href="{{ route('locale.switch', 'zh') }}">中文</a>
                            </li>
                        </ul>
                    </li>
                    <x-admin-suggestion-notifications />
                    @auth
                    @php $navUser = Auth::user(); @endphp
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 py-1" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @if($navUser->photoUrl())
                                <img src="{{ $navUser->photoUrl() }}" alt="{{ $navUser->name }}" class="navbar-user-avatar">
                            @else
                                <span class="navbar-user-avatar navbar-user-avatar--placeholder" aria-hidden="true">{{ mb_strtoupper(mb_substr($navUser->name, 0, 1)) }}</span>
                            @endif
                            <span class="navbar-user-name">{{ $navUser->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('Profile') }}</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">{{ __('Log Out') }}</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Log in') }}</a>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1">
        {{ $slot }}
    </main>

    <footer class="footer mt-auto flex-shrink-0">
        <div class="container">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                <p class="mb-0">&copy; {{ date('Y') }} Morenova Guide. {{ __('messages.welcome') }}</p>
                <div class="footer-socials" aria-label="{{ __('messages.social_networks') }}">
                    <a href="https://www.instagram.com/direxgroup/" class="footer-social-link" target="_blank" rel="noopener noreferrer" title="Instagram" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.youtube.com/channel/UCt4v6ZOZ2NGddjLvQ-pHt3A" class="footer-social-link" target="_blank" rel="noopener noreferrer" title="YouTube" aria-label="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="https://www.facebook.com/pages/Direx-Group-Initia/320944431278728" class="footer-social-link" target="_blank" rel="noopener noreferrer" title="Facebook" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://x.com/direxgroup1" class="footer-social-link" target="_blank" rel="noopener noreferrer" title="X" aria-label="X">
                        <i class="fab fa-x-twitter"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
