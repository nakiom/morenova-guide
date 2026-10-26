<x-app-layout>
    <div class="container py-5">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-end gap-3 mb-4">
            <div>
                <p class="text-muted small text-uppercase fw-semibold mb-1" style="letter-spacing:.06em;">{{ __('messages.admin_reports') }}</p>
                <h1 class="h2 fw-bold text-dark-blue mb-2">{{ __('messages.center_cases_report') }}</h1>
                <p class="text-muted mb-0">{{ __('messages.center_cases_report_hint') }}</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>{{ __('messages.back') }}
            </a>
        </div>

        <form method="GET" class="row g-2 align-items-end mb-4">
            <div class="col-md-5 col-lg-4">
                <label class="form-label" for="center_id">{{ __('messages.center') }}</label>
                <select name="center_id" id="center_id" class="form-select" onchange="this.form.submit()">
                    <option value="">{{ __('messages.all_centers') }}</option>
                    @foreach($centers as $center)
                        <option value="{{ $center->id }}" @selected((int) $centerId === (int) $center->id)>
                            {{ $center->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 col-lg-3">
                <label class="form-label" for="status">{{ __('messages.status') }}</label>
                <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                    <option value="">{{ __('messages.all_statuses') }}</option>
                    <option value="active" @selected($status === 'active')>{{ __('messages.case_active') }}</option>
                    <option value="finished" @selected($status === 'finished')>{{ __('messages.case_finished') }}</option>
                </select>
            </div>
        </form>

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ __('messages.cases_active') }}</div>
                        <div class="h3 mb-0 text-primary">{{ $totals['active'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ __('messages.cases_finished') }}</div>
                        <div class="h3 mb-0 text-dark-blue">{{ $totals['finished'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ __('messages.sessions_count') }}</div>
                        <div class="h3 mb-0 text-dark-blue">{{ $totals['sessions'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        @forelse($blocks as $block)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <div>
                            <h2 class="h5 fw-bold text-dark-blue mb-1">
                                <a href="{{ route('admin.centers.show', $block['center']) }}" class="text-decoration-none text-dark-blue">
                                    {{ $block['center']->name }}
                                </a>
                            </h2>
                            @if($block['center']->location)
                                <div class="small text-muted">{{ $block['center']->location }}</div>
                            @endif
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-primary">{{ __('messages.cases_active') }}: {{ $block['active'] }}</span>
                            <span class="badge bg-secondary">{{ __('messages.cases_finished') }}: {{ $block['finished'] }}</span>
                            <span class="badge bg-light text-dark border">{{ __('messages.sessions_count') }}: {{ $block['sessions'] }}</span>
                        </div>
                    </div>

                    @if($block['rows']->isEmpty())
                        <p class="text-muted mb-0">{{ __('messages.no_treated_cases') }}</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.patient_name') }}</th>
                                        <th>{{ __('messages.protocol') }}</th>
                                        <th>{{ __('messages.doctor') }}</th>
                                        <th>{{ __('messages.case_started_at') }}</th>
                                        <th>{{ __('messages.case_status') }}</th>
                                        <th class="text-end">{{ __('messages.sessions_count') }}</th>
                                        <th>{{ __('messages.case_scores') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($block['rows'] as $row)
                                    <tr>
                                        <td>
                                            <a href="{{ route('clinical.patients.show', $row['patient']) }}" class="fw-semibold text-decoration-none">
                                                {{ $row['patient']->name }}
                                            </a>
                                            <div class="small text-muted">{{ $row['patient']->anonymousCode() }}</div>
                                        </td>
                                        <td>{{ $row['protocol'] ?: '—' }}</td>
                                        <td>{{ $row['patient']->user?->name ?: '—' }}</td>
                                        <td>{{ $row['started_at']->format('d/m/Y') }}</td>
                                        <td>
                                            @if($row['status'] === 'active')
                                                <span class="badge bg-success">{{ __('messages.case_active') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('messages.case_finished') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ $row['sessions'] }}</td>
                                        <td class="small">
                                            @forelse($row['scores'] as $score)
                                                <div>
                                                    <span class="fw-semibold">{{ $score['name'] }}</span>
                                                    · {{ __('messages.score_initial') }} {{ $score['initial'] ?? '—' }}
                                                    · {{ __('messages.score_final') }} {{ $score['final'] ?? '—' }}
                                                </div>
                                            @empty
                                                —
                                            @endforelse
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-0">{{ __('messages.no_treated_cases') }}</p>
                </div>
            </div>
        @endforelse
    </div>
</x-app-layout>
