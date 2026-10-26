<x-app-layout>
    <div class="container py-5">
        @php $credCenter = auth()->user()?->center; @endphp
        <div class="mb-4 page-header-with-logo">
            <x-center-logo size="lg" :center="$credCenter" />
            <div class="page-header-text">
                <h1 class="h2 fw-bold text-dark-blue mb-1">{{ __('messages.credentials_management') }}</h1>
                <p class="text-muted mb-0">
                    @if($credCenter)
                        {{ $credCenter->name }} ·
                    @endif
                    {{ __('messages.credentials_management_hint') }}
                </p>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form method="GET" action="{{ route('center.credentials.index') }}" class="row g-2 mb-3">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="input-icon-wrap">
                    <i class="fas fa-search"></i>
                    <input type="search" name="q" value="{{ $q }}" class="form-control"
                           placeholder="{{ __('messages.credentials_search_placeholder') }}"
                           autocomplete="off">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-secondary">
                    <i class="fas fa-search me-1"></i>{{ __('messages.search') }}
                </button>
            </div>
            @if($q !== '')
            <div class="col-auto">
                <a href="{{ route('center.credentials.index') }}" class="btn btn-link">{{ __('messages.clear_filters') }}</a>
            </div>
            @endif
        </form>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:3.5rem;"></th>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.email') }}</th>
                            <th>{{ __('messages.role_type') }}</th>
                            <th class="text-end">{{ __('messages.password') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>
                                @if($user->photoUrl())
                                    <img src="{{ $user->photoUrl() }}"
                                         alt="{{ $user->name }}"
                                         class="rounded-circle credentials-user-avatar"
                                         width="40" height="40">
                                @else
                                    <span class="credentials-user-avatar credentials-user-avatar--placeholder" aria-hidden="true">
                                        {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-dark-blue">{{ $user->name }}</div>
                                @if($user->job_title)
                                    <div class="small text-muted">{{ $user->job_title }}</div>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->is($manager) ? __('messages.primary_user') : __('messages.sub_user') }}</td>
                            <td class="text-end">
                                <a href="{{ route('center.credentials.edit', $user) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-key me-1"></i>{{ __('messages.change_password') }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                {{ $q !== '' ? __('messages.credentials_search_empty') : __('messages.no_results') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
