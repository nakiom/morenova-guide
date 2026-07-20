<x-app-layout>
    <div class="container py-5 profile-page">
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0 ps-3">
                @foreach($errors->getBags() as $bag)
                    @foreach($bag->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="profile-hero card border-0 shadow-sm mb-4">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start gap-4">
                    <div class="profile-avatar-wrap flex-shrink-0">
                        @if($user->photoUrl())
                        <img src="{{ $user->photoUrl() }}" alt="{{ $user->name }}" class="profile-avatar">
                        @else
                        <div class="profile-avatar profile-avatar--placeholder">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        @endif
                    </div>
                    <div class="text-center text-md-start">
                        <h1 class="h2 fw-bold text-dark-blue mb-1">{{ __('messages.profile_title') }}</h1>
                        <p class="text-muted mb-2">{{ __('messages.profile_subtitle') }}</p>
                        <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2">
                            <span class="badge rounded-pill profile-badge">
                                <i class="fas fa-user me-1"></i>{{ $user->name }}
                            </span>
                            <span class="badge rounded-pill profile-badge profile-badge--muted">
                                <i class="fas fa-envelope me-1"></i>{{ $user->email }}
                            </span>
                            @if($user->center)
                            <span class="badge rounded-pill profile-badge profile-badge--muted">
                                <i class="fas fa-building me-1"></i>{{ $user->center->name }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-7">
                <div class="card profile-section-card border-0 shadow-sm h-100">
                    <div class="card-body p-4 p-md-5">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <div class="card profile-section-card border-0 shadow-sm mb-4">
                    <div class="card-body p-4 p-md-5">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="card profile-section-card profile-section-card--danger border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
