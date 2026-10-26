<x-app-layout>
    <div class="container py-5 flex-grow-1 d-flex flex-column">
        @if(session('error'))
            <div class="alert alert-warning">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @php
            $dashUser = auth()->user();
            $dashCenter = $dashUser?->center;
            $isAdmin = $dashUser?->isAdmin();
        @endphp
        <div class="page-header page-header-with-logo">
            <x-center-logo size="lg" :center="$dashCenter" />
            <div class="page-header-text">
                <h1 class="h2 fw-bold text-dark-blue mb-0">{{ __('messages.dashboard') }}</h1>
                @if($dashCenter)
                    <p class="text-muted small mb-0 mt-1">{{ $dashCenter->name }}</p>
                @endif
            </div>
        </div>

        <div class="row g-4 mb-4" data-stagger>
            @if($isAdmin)
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-building fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.crm_centers') }}</h5>
                        <a href="{{ route('admin.centers.index') }}" class="btn btn-primary">{{ __('messages.manage') }}</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-users fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.crm_users') }}</h5>
                        <a href="{{ route('admin.crm-users.index') }}" class="btn btn-primary">{{ __('messages.manage') }}</a>
                    </div>
                </div>
            </div>
            @endif

            <div class="col-12 col-sm-6 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-user-injured fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.clinical_patients') }}</h5>
                        <a href="{{ route('clinical.patients.index') }}" class="btn btn-primary">{{ __('messages.manage') }}</a>
                    </div>
                </div>
            </div>

            @if($isAdmin)
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-hdd fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.equipment') }}</h5>
                        <a href="{{ route('admin.equipment.index') }}" class="btn btn-primary">{{ __('messages.manage') }}</a>
                    </div>
                </div>
            </div>
            @endif

            <div class="col-12 col-sm-6 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-chart-line fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.clinical_reports') }}</h5>
                        <a href="{{ route('clinical.reports.index') }}" class="btn btn-primary">{{ __('messages.manage') }}</a>
                    </div>
                </div>
            </div>

            @if($isAdmin)
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-notes-medical fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.center_cases_report') }}</h5>
                        <a href="{{ route('admin.reports.center-cases') }}" class="btn btn-primary">{{ __('messages.view') }}</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-trophy fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.center_success_ranking') }}</h5>
                        <a href="{{ route('admin.reports.center-success') }}" class="btn btn-primary">{{ __('messages.view') }}</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-envelope-open-text fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.mail_settings_title') }}</h5>
                        <a href="{{ route('admin.mail.edit') }}" class="btn btn-primary">{{ __('messages.manage') }}</a>
                    </div>
                </div>
            </div>
            @endif

            @if(! $isAdmin && ! $dashUser?->isSubUser() && $dashUser?->center_id)
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-key fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.credentials_management') }}</h5>
                        <a href="{{ route('center.credentials.index') }}" class="btn btn-primary">{{ __('messages.manage') }}</a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        @if($isAdmin)
        <p class="section-label">{{ __('messages.protocol_catalog') }}</p>
        <div class="row g-4 mb-0" data-stagger>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-folder-open fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.categories') }}</h5>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-primary">{{ __('messages.manage') }}</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-stethoscope fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.pathologies') }}</h5>
                        <a href="{{ route('admin.pathologies.index') }}" class="btn btn-primary">{{ __('messages.manage') }}</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-table fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.protocol_summary') }}</h5>
                        <a href="{{ route('admin.protocol-summary') }}" class="btn btn-primary">{{ __('messages.view') }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4 mt-0" data-stagger>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.application_areas_management') }}</h5>
                        <a href="{{ route('admin.application-areas.index') }}" class="btn btn-primary">{{ __('messages.manage') }}</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-list-ol fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.steps_management') }}</h5>
                        <a href="{{ route('admin.steps.index') }}" class="btn btn-primary">{{ __('messages.manage') }}</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-clipboard-check fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.session_checklist_management') }}</h5>
                        <a href="{{ route('admin.session-checklist.index') }}" class="btn btn-primary">{{ __('messages.manage') }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4 mt-0" data-stagger>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-calendar-check fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.follow_up_management') }}</h5>
                        <a href="{{ route('admin.follow-up.index') }}" class="btn btn-primary">{{ __('messages.manage') }}</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-lightbulb fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.suggestions') }}</h5>
                        <a href="{{ route('admin.suggestions.index') }}" class="btn btn-primary">{{ __('messages.manage') }}</a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
