<x-app-layout>
    <div class="container py-5">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <a href="{{ route('admin.centers.index') }}" class="btn btn-outline-primary btn-sm mb-2">
                    <i class="fas fa-arrow-left me-1"></i>{{ __('messages.back') }}
                </a>
                <div class="d-flex align-items-center gap-3">
                    @if($center->logoUrl())
                    <img src="{{ $center->logoUrl() }}" alt="{{ $center->name }}"
                         class="rounded border bg-white p-2"
                         style="width: 72px; height: 72px; object-fit: contain;">
                    @endif
                    <div>
                        <h1 class="h2 fw-bold text-dark-blue mb-1">{{ $center->name }}</h1>
                        <p class="text-muted mb-0">
                            {{ $center->location ?: '—' }}
                            @if($center->address) · {{ $center->address }} @endif
                        </p>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.crm-users.create', ['center_id' => $center->id]) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-user-plus me-1"></i>{{ __('messages.create_crm_user') }}
                </a>
                <a href="{{ route('admin.equipment.create', ['center_id' => $center->id]) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-hdd me-1"></i>{{ __('messages.create_equipment') }}
                </a>
                <a href="{{ route('admin.centers.edit', $center) }}" class="btn btn-outline-secondary btn-sm">{{ __('messages.edit') }}</a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ __('messages.contact') }}</div>
                        <div>{{ $center->contact_name ?: '—' }}</div>
                        <div>{{ $center->phone ?: '' }}</div>
                        <div>{{ $center->email ?: '' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-2">{{ __('messages.social_networks') }}</div>
                        <div class="d-flex flex-wrap gap-2">
                            @if($center->instagram_url)
                            <a href="{{ $center->instagram_url }}" class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            @endif
                            @if($center->youtube_url)
                            <a href="{{ $center->youtube_url }}" class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                                <i class="fab fa-youtube"></i>
                            </a>
                            @endif
                            @if($center->facebook_url)
                            <a href="{{ $center->facebook_url }}" class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            @endif
                            @if($center->x_url)
                            <a href="{{ $center->x_url }}" class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener noreferrer" aria-label="X">
                                <i class="fab fa-x-twitter"></i>
                            </a>
                            @endif
                            @unless($center->instagram_url || $center->youtube_url || $center->facebook_url || $center->x_url)
                            <span>—</span>
                            @endunless
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ __('messages.notes') }}</div>
                        <div>{{ $center->notes ?: '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="h5 fw-bold text-dark-blue mb-3">{{ __('messages.crm_users') }}</h2>
        <div class="table-responsive mb-5">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th style="width:3.5rem;"></th>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.email') }}</th>
                        <th>{{ __('messages.role_type') }}</th>
                        <th>{{ __('messages.phone') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($center->users as $user)
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
                        <td>
                            @if($user->parent_user_id)
                                {{ __('messages.sub_user') }} ({{ $user->parent?->name }})
                            @else
                                {{ __('messages.primary_user') }}
                            @endif
                        </td>
                        <td>{{ $user->phone ?: '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.crm-users.show', $user) }}" class="btn btn-sm btn-outline-primary">{{ __('messages.view') }}</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted">{{ __('messages.no_crm_users') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h2 class="h5 fw-bold text-dark-blue mb-0">{{ __('messages.center_patients') }}</h2>
            <a href="{{ route('admin.reports.center-cases', ['center_id' => $center->id]) }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-notes-medical me-1"></i>{{ __('messages.center_cases_report') }}
            </a>
        </div>
        <div class="table-responsive mb-5">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>{{ __('messages.patient_name') }}</th>
                        <th>{{ __('messages.protocol') }}</th>
                        <th>{{ __('messages.doctor') }}</th>
                        <th class="text-end">{{ __('messages.sessions_count') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                    <tr>
                        <td>{{ $patient->name }}</td>
                        <td>{{ $patient->pathology?->translation()?->name ?? $patient->pathology?->slug }}</td>
                        <td>{{ $patient->user?->name ?: '—' }}</td>
                        <td class="text-end">{{ $patient->sessions_count }}</td>
                        <td class="text-end">
                            <a href="{{ route('clinical.patients.show', $patient) }}" class="btn btn-sm btn-outline-primary">{{ __('messages.view') }}</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted">{{ __('messages.center_no_patients') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h2 class="h5 fw-bold text-dark-blue mb-3">{{ __('messages.center_patients_deleted') }}</h2>
        <div class="table-responsive mb-5">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>{{ __('messages.patient_name') }}</th>
                        <th>{{ __('messages.protocol') }}</th>
                        <th>{{ __('messages.doctor') }}</th>
                        <th>{{ __('messages.patient_deleted_at') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deletedPatients as $patient)
                    <tr class="table-secondary">
                        <td>
                            {{ $patient->name }}
                            <span class="badge bg-secondary ms-1">{{ __('messages.patient_deleted_badge') }}</span>
                        </td>
                        <td>{{ $patient->pathology?->translation()?->name ?? $patient->pathology?->slug }}</td>
                        <td>{{ $patient->user?->name ?: '—' }}</td>
                        <td>{{ $patient->deleted_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</td>
                        <td class="text-end">
                            <a href="{{ route('clinical.patients.show', $patient) }}" class="btn btn-sm btn-outline-secondary">{{ __('messages.view') }}</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted">{{ __('messages.center_no_deleted_patients') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h2 class="h5 fw-bold text-dark-blue mb-3">{{ __('messages.equipment') }}</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ __('messages.serial_number') }}</th>
                        <th>{{ __('messages.equipment_model') }}</th>
                        <th>{{ __('messages.assigned_user') }}</th>
                        <th>{{ __('messages.location') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($center->equipment as $item)
                    <tr>
                        <td>{{ $item->serial_number }}</td>
                        <td>{{ $item->model }}</td>
                        <td>{{ $item->user?->name ?: '—' }}</td>
                        <td>{{ $item->location ?: '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.equipment.edit', $item) }}" class="btn btn-sm btn-outline-primary">{{ __('messages.edit') }}</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted">{{ __('messages.no_equipment') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
