<x-app-layout>
    <div class="container py-5">
        <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h2 fw-bold text-dark-blue mb-1">{{ __('messages.upcoming_visits') }}</h1>
                <p class="text-muted mb-0">{{ __('messages.upcoming_visits_hint') }}</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('clinical.patients.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-users me-1"></i>{{ __('messages.clinical_patients') }}
                </a>
            </div>
        </div>

        <div class="btn-group mb-3" role="group" aria-label="{{ __('messages.period') }}">
            <a href="{{ route('clinical.upcoming.index', array_merge(request()->except('period', 'page'), ['period' => 'week'])) }}"
               class="btn {{ $period === 'week' ? 'btn-primary' : 'btn-outline-primary' }}">{{ __('messages.period_week') }}</a>
            <a href="{{ route('clinical.upcoming.index', array_merge(request()->except('period', 'page'), ['period' => 'month'])) }}"
               class="btn {{ $period === 'month' ? 'btn-primary' : 'btn-outline-primary' }}">{{ __('messages.period_month') }}</a>
            <a href="{{ route('clinical.upcoming.index', array_merge(request()->except('period', 'page'), ['period' => 'all'])) }}"
               class="btn {{ $period === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">{{ __('messages.period_all_upcoming') }}</a>
        </div>

        <form method="GET" class="row g-2 mb-4">
            <input type="hidden" name="period" value="{{ $period }}">
            @if($canFilterCenter)
            <div class="col-12 col-md-4">
                <select name="center_id" class="form-select" onchange="this.form.submit()">
                    <option value="">{{ __('messages.all_centers') }}</option>
                    @foreach($centers as $center)
                    <option value="{{ $center->id }}" @selected(request('center_id') == $center->id)>{{ $center->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            @if($canFilterDoctor)
            <div class="col-12 col-md-4">
                <select name="user_id" class="form-select" onchange="this.form.submit()">
                    <option value="">{{ __('messages.all_doctors') }}</option>
                    @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}" @selected(request('user_id') == $doctor->id)>{{ $doctor->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-12 col-md-4 d-flex align-items-center">
                <span class="text-muted small">
                    {{ $periodLabel }}
                    · {{ $from->format('d/m/Y') }}
                    @if($to)
                        – {{ $to->format('d/m/Y') }}
                    @endif
                </span>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>{{ __('messages.expected_next_visit') }}</th>
                        <th>{{ __('messages.patient_name') }}</th>
                        <th>{{ __('messages.protocol') }}</th>
                        <th>{{ __('messages.next_session_number') }}</th>
                        <th>{{ __('messages.contact_data') }}</th>
                        @if($canFilterDoctor)
                        <th>{{ __('messages.doctor') }}</th>
                        @endif
                        @if($canFilterCenter)
                        <th>{{ __('messages.center') }}</th>
                        @endif
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                    @php
                        $isOverdue = $patient->expected_next_visit_at->lt(now()->startOfDay());
                        $isToday = $patient->expected_next_visit_at->isToday();
                    @endphp
                    <tr class="{{ $isOverdue ? 'table-warning' : ($isToday ? 'table-primary' : '') }}">
                        <td>
                            <strong>{{ $patient->expected_next_visit_at->format('d/m/Y') }}</strong>
                            @if($isToday)
                                <span class="badge bg-primary ms-1">{{ __('messages.today') }}</span>
                            @elseif($isOverdue)
                                <span class="badge bg-warning text-dark ms-1">{{ __('messages.overdue') }}</span>
                            @endif
                        </td>
                        <td>{{ $patient->name }}</td>
                        <td>{{ $patient->pathology?->translation()?->name ?? $patient->pathology?->slug }}</td>
                        <td>
                            <span class="badge bg-secondary">#{{ $patient->nextSessionNumber() }}</span>
                            <small class="text-muted">({{ $patient->sessions_count }} {{ __('messages.done') }})</small>
                        </td>
                        <td>
                            <div class="small">
                                @if($patient->email)
                                    <div>
                                        <i class="fas fa-envelope me-1 text-muted"></i>
                                        <a href="mailto:{{ $patient->email }}">{{ $patient->email }}</a>
                                    </div>
                                @endif
                                @forelse($patient->phoneList() as $phone)
                                    <div>
                                        <i class="fas fa-phone me-1 text-muted"></i>
                                        <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}">{{ $phone }}</a>
                                    </div>
                                @empty
                                    @unless($patient->email)
                                        <span class="text-muted">—</span>
                                    @endunless
                                @endforelse
                            </div>
                        </td>
                        @if($canFilterDoctor)
                        <td>{{ $patient->user?->name }}</td>
                        @endif
                        @if($canFilterCenter)
                        <td>{{ $patient->user?->center?->name ?? '—' }}</td>
                        @endif
                        <td class="text-end text-nowrap">
                            <a href="{{ route('clinical.patients.show', $patient) }}" class="btn btn-sm btn-outline-primary">{{ __('messages.view') }}</a>
                            @can('update', $patient)
                            <a href="{{ route('clinical.sessions.create', $patient) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-calendar-plus"></i>
                            </a>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">{{ __('messages.no_upcoming_visits') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $patients->links() }}
    </div>
</x-app-layout>
