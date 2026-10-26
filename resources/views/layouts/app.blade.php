<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>MoreNova-Initia</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap">
    @if(request()->routeIs('pathologies.show', 'admin.protocol-summary'))
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=VT323&display=swap">
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex flex-column min-vh-100">
    <script>document.body.classList.add('app-ready');</script>
    <nav class="navbar navbar-expand-lg navbar-light app-navbar flex-shrink-0" id="app-navbar">
        <div class="container">
            <div class="navbar-brand-cluster">
                <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                    <img src="/img/morenova-string.png" alt="MoreNova" width="188" height="34" decoding="async" fetchpriority="high">
                </a>
                @auth
                    @php
                        $topbarCenter = Auth::user()->center;
                        $topbarCenterLogo = $topbarCenter?->logoUrl();
                    @endphp
                    @if($topbarCenter && $topbarCenterLogo)
                        <span class="navbar-brand-divider" aria-hidden="true"></span>
                        <div class="center-logo-wrap center-logo--sm navbar-center-logo">
                            <img
                                src="{{ $topbarCenterLogo }}"
                                alt="{{ $topbarCenter->name }}"
                                class="center-logo"
                                title="{{ $topbarCenter->name }}"
                            >
                        </div>
                    @endif
                @endauth
            </div>
            
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
                        <a class="nav-link" href="{{ route('clinical.upcoming.index') }}">{{ __('messages.upcoming_visits') }}</a>
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
                    @php
                        $navUser = Auth::user();
                        $navUser->loadMissing('center');
                    @endphp
                    <li class="nav-item">
                        <x-dropdown align="right" width="user" contentClasses="user-menu" panelClass="user-menu-panel">
                            <x-slot name="trigger">
                                <button type="button" class="btn btn-link nav-link user-menu-trigger d-flex align-items-center gap-2 py-1" aria-haspopup="menu" aria-label="{{ $navUser->name }}">
                                    @if($navUser->photoUrl())
                                        <img src="{{ $navUser->photoUrl() }}" alt="" class="navbar-user-avatar">
                                    @else
                                        <span class="navbar-user-avatar navbar-user-avatar--placeholder" aria-hidden="true">{{ mb_strtoupper(mb_substr($navUser->name, 0, 1)) }}</span>
                                    @endif
                                    <span class="navbar-user-name">{{ $navUser->name }}</span>
                                    <i class="fas fa-chevron-down user-menu-chevron" aria-hidden="true"></i>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <div class="user-menu-card" @click.stop>
                                    <div class="user-menu-hero">
                                        <div class="user-menu-hero-glow" aria-hidden="true"></div>
                                        <div class="user-menu-identity">
                                            @if($navUser->photoUrl())
                                                <img src="{{ $navUser->photoUrl() }}" alt="{{ $navUser->name }}" class="user-menu-avatar">
                                            @else
                                                <span class="user-menu-avatar user-menu-avatar--placeholder" aria-hidden="true">{{ mb_strtoupper(mb_substr($navUser->name, 0, 1)) }}</span>
                                            @endif
                                            <div class="user-menu-meta">
                                                <div class="user-menu-name">{{ $navUser->name }}</div>
                                                @if($navUser->job_title)
                                                    <div class="user-menu-role">{{ $navUser->job_title }}</div>
                                                @elseif($navUser->center?->name)
                                                    <div class="user-menu-role">{{ $navUser->center->name }}</div>
                                                @endif
                                                <div class="user-menu-email">{{ $navUser->email }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="user-menu-actions">
                                        <a href="{{ route('profile.edit') }}" class="user-menu-action">
                                            <span class="user-menu-action-icon" aria-hidden="true"><i class="fas fa-user-pen"></i></span>
                                            <span class="user-menu-action-copy">
                                                <span class="user-menu-action-label">{{ __('messages.profile_title') }}</span>
                                                <span class="user-menu-action-hint">{{ __('messages.user_menu_profile_hint') }}</span>
                                            </span>
                                            <i class="fas fa-arrow-right user-menu-action-arrow" aria-hidden="true"></i>
                                        </a>
                                    </div>

                                    <div class="user-menu-footer">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="user-menu-logout">
                                                <span class="user-menu-logout-icon" aria-hidden="true"><i class="fas fa-right-from-bracket"></i></span>
                                                <span>{{ __('messages.logout') }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </x-slot>
                        </x-dropdown>
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
                    <a href="https://www.instagram.com/initia_medical/" class="footer-social-link" target="_blank" rel="noopener noreferrer" title="Instagram" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.youtube.com/@Initiamedical" class="footer-social-link" target="_blank" rel="noopener noreferrer" title="YouTube" aria-label="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="https://www.facebook.com/pages/Direx-Group-Initia/320944431278728" class="footer-social-link" target="_blank" rel="noopener noreferrer" title="Facebook" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
