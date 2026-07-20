<x-app-layout>
    <div class="container py-5 flex-grow-1 d-flex flex-column">
        <div class="page-header">
            <h1 class="h2 h1-md display-5 fw-bold text-dark-blue">{{ __('messages.dashboard') }}</h1>
        </div>

        <div class="row g-4 mb-4" data-stagger>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-user-injured fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.clinical_patients') }}</h5>
                        <a href="{{ route('clinical.patients.index') }}" class="btn btn-primary">{{ __('messages.manage') }}</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-chart-line fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.clinical_reports') }}</h5>
                        <a href="{{ route('clinical.reports.index') }}" class="btn btn-primary">{{ __('messages.manage') }}</a>
                    </div>
                </div>
            </div>
            @if(auth()->user()?->isAdmin())
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
            @if(!auth()->user()?->isAdmin() && !auth()->user()?->isSubUser() && auth()->user()?->center_id)
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

        @if(auth()->user()?->isAdmin())
        <p class="section-label">{{ __('messages.protocol_catalog') }}</p>
        <div class="row g-4" data-stagger>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-table fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.protocol_summary') }}</h5>
                        <a href="{{ route('admin.protocol-summary') }}" class="btn btn-primary">{{ __('messages.view') }}</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-folder-open fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.categories') }}</h5>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-primary">{{ __('messages.manage') }}</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-stethoscope fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.pathologies') }}</h5>
                        <a href="{{ route('admin.pathologies.index') }}" class="btn btn-primary">{{ __('messages.manage') }}</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-list-ol fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.steps_management') }}</h5>
                        <a href="{{ route('admin.steps.index') }}" class="btn btn-primary">{{ __('messages.manage') }}</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-clipboard-check fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.session_checklist_management') }}</h5>
                        <a href="{{ route('admin.session-checklist.index') }}" class="btn btn-primary">{{ __('messages.manage') }}</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.application_areas_management') }}</h5>
                        <a href="{{ route('admin.application-areas.index') }}" class="btn btn-primary">{{ __('messages.manage') }}</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <i class="fas fa-calendar-check fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">{{ __('messages.follow_up_management') }}</h5>
                        <a href="{{ route('admin.follow-up.index') }}" class="btn btn-primary">{{ __('messages.manage') }}</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
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
