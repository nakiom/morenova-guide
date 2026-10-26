<x-app-layout>
    <div class="container py-5">
        <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h1 class="h2 fw-bold text-dark-blue mb-1">{{ __('messages.clinical_patients') }}</h1>
                <p class="text-muted mb-0">{{ __('messages.clinical_patients_hint') }}</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('clinical.upcoming.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-calendar-alt me-2"></i>{{ __('messages.upcoming_visits') }}
                </a>
                <a href="{{ route('clinical.reports.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-chart-line me-2"></i>{{ __('messages.clinical_reports') }}
                </a>
                <a href="{{ route('clinical.patients.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>{{ __('messages.create_patient') }}
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form method="GET" class="row g-2 mb-4">
            <div class="col-12 col-md-4">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('messages.search_patient') }}">
            </div>
            <div class="col-12 col-md-4">
                <select name="pathology_id" class="form-select" onchange="this.form.submit()">
                    <option value="">{{ __('messages.all_protocols') }}</option>
                    @foreach($pathologies as $pathology)
                    <option value="{{ $pathology->id }}" @selected(request('pathology_id') == $pathology->id)>
                        {{ $pathology->translation()?->name ?? $pathology->slug }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <button type="submit" class="btn btn-outline-secondary w-100">{{ __('messages.search') }}</button>
            </div>
        </form>

        <div class="table-responsive patients-table-wrap">
            <table class="table patients-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('messages.patient_name') }}</th>
                        <th>{{ __('messages.patient_id') }}</th>
                        <th>{{ __('messages.age') }}</th>
                        <th>{{ __('messages.sex') }}</th>
                        <th>{{ __('messages.protocol') }}</th>
                        <th>{{ __('messages.sessions_count') }}</th>
                        <th>{{ __('messages.total_shocks') }}</th>
                        @if($user->isAdmin() || $user->center_id)
                        <th>{{ __('messages.doctor') }}</th>
                        @endif
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                    <tr class="patient-row patient-row--{{ $patient->sex ?: 'unspecified' }}">
                        <td>{{ $patient->name }}</td>
                        <td>{{ $patient->external_id ?: '—' }}</td>
                        <td>{{ $patient->age ?? '—' }}</td>
                        <td>{{ $patient->sexLabel() ?? '—' }}</td>
                        <td>{{ $patient->pathology?->translation()?->name ?? $patient->pathology?->slug }}</td>
                        <td>{{ $patient->sessions_count }}</td>
                        <td>{{ $patient->total_shocks }}</td>
                        @if($user->isAdmin() || $user->center_id)
                        <td>
                            <x-doctor-chip :user="$patient->user" />
                        </td>
                        @endif
                        <td class="text-end text-nowrap">
                            <a href="{{ route('clinical.patients.show', $patient) }}" class="btn btn-sm btn-primary">{{ __('messages.view') }}</a>
                            @can('delete', $patient)
                            <form action="{{ route('clinical.patients.destroy', $patient) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm(@json(__('messages.patient_delete_confirm')))">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('messages.delete') }}</button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr class="patient-row patient-row--empty">
                        <td colspan="{{ ($user->isAdmin() || $user->center_id) ? 9 : 8 }}" class="text-center text-muted">{{ __('messages.no_patients') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $patients->links() }}
    </div>
</x-app-layout>
