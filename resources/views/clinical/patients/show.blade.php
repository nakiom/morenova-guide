@php
    $pathologyName = $patient->pathology?->translation($locale)?->name ?? $patient->pathology?->slug;
@endphp
<x-app-layout>
    <div class="container py-5">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <a href="{{ route('clinical.patients.index') }}" class="btn btn-outline-primary btn-sm mb-2">
                    <i class="fas fa-arrow-left me-1"></i>{{ __('messages.back') }}
                </a>
                <h1 class="h2 fw-bold text-dark-blue mb-1">{{ $patient->name }}</h1>
                <p class="text-muted mb-0">
                    {{ __('messages.anonymous_code') }}: <strong>{{ $patient->anonymousCode() }}</strong>
                    · {{ __('messages.patient_id') }}: {{ $patient->external_id ?: '—' }}
                    · {{ __('messages.age') }}: {{ $patient->age ?? '—' }}
                    · {{ __('messages.protocol') }}: {{ $pathologyName }}
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('clinical.scores.create', $patient) }}" class="btn btn-primary">
                    <i class="fas fa-clipboard-check me-1"></i>{{ __('messages.complete_questionnaire') }}
                </a>
                <a href="{{ route('clinical.reports.patient', $patient) }}" class="btn btn-outline-primary">
                    <i class="fas fa-chart-line me-1"></i>{{ __('messages.evolution') }}
                </a>
                <a href="{{ route('clinical.patients.edit', $patient) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-edit me-1"></i>{{ __('messages.edit') }}
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('prompt_questionnaire'))
        <div class="alert alert-primary border-0 shadow-sm d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <strong>{{ __('messages.complete_questionnaire') }}</strong>
                <div class="small mb-0">{{ __('messages.after_session_questionnaire_hint') }}</div>
            </div>
            <a href="{{ route('clinical.scores.create', $patient) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-clipboard-check me-1"></i>{{ __('messages.complete_questionnaire') }}
            </a>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ __('messages.sessions_count') }}</div>
                        <div class="h3 mb-0 text-primary">{{ $patient->sessions_count }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ __('messages.total_shocks') }}</div>
                        <div class="h3 mb-0 text-primary">{{ $patient->total_shocks }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ __('messages.expected_next_visit') }}</div>
                        <div class="h5 mb-0 {{ $patient->expected_next_visit_at ? 'text-primary' : 'text-muted' }}">
                            {{ $patient->expected_next_visit_at?->format('d/m/Y') ?? '—' }}
                        </div>
                    </div>
                </div>
            </div>
            @if(auth()->user()->isAdmin() || (auth()->user()->center_id && !auth()->user()->isSubUser()))
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ __('messages.doctor') }}</div>
                        <div class="h6 mb-0">{{ $patient->user?->name }}</div>
                        @if($patient->user?->center)
                        <div class="small text-muted">{{ $patient->user->center->name }}</div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h6 fw-bold text-dark-blue mb-3">{{ __('messages.contact_data') }}</h2>
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="text-muted small">{{ __('messages.email') }}</div>
                        @if($patient->email)
                            <a href="mailto:{{ $patient->email }}">{{ $patient->email }}</a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">{{ __('messages.phones') }}</div>
                        @forelse($patient->phoneList() as $phone)
                            <div><a href="tel:{{ preg_replace('/\s+/', '', $phone) }}">{{ $phone }}</a></div>
                        @empty
                            <span class="text-muted">—</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        @if($patient->notes)
        <div class="alert alert-light border mb-4">{{ $patient->notes }}</div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 fw-bold text-dark-blue mb-0">{{ __('messages.treatment_sessions') }}</h2>
            <a href="{{ route('clinical.sessions.create', $patient) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>{{ __('messages.add_session') }}
            </a>
        </div>
        <div class="table-responsive mb-5">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ __('messages.session_number') }}</th>
                        <th>{{ __('messages.session_date') }}</th>
                        <th>{{ __('messages.shocks') }}</th>
                        <th>{{ __('messages.param_energy') }}</th>
                        <th>{{ __('messages.param_frequency') }}</th>
                        <th>{{ __('messages.notes') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patient->treatmentSessions as $session)
                    @php
                        $energyDiff = $session->energyDiffersFrom($patient->pathology);
                        $freqDiff = $session->frequencyDiffersFrom($patient->pathology);
                    @endphp
                    <tr>
                        <td>
                            <span class="badge bg-primary-subtle text-primary fw-semibold">{{ $session->session_number }}</span>
                        </td>
                        <td>{{ $session->session_date->format('Y-m-d') }}</td>
                        <td>{{ $session->shocks }}</td>
                        <td>
                            @if($session->energy_kv !== null)
                                <span class="{{ $energyDiff ? 'text-warning fw-semibold' : '' }}">{{ $session->energy_kv }} kV</span>
                                @if($energyDiff)
                                    <span class="badge bg-warning text-dark" title="{{ __('messages.session_param_differs') }}">≠</span>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($session->frequency_hz !== null)
                                <span class="{{ $freqDiff ? 'text-warning fw-semibold' : '' }}">{{ $session->frequency_hz }} Hz</span>
                                @if($freqDiff)
                                    <span class="badge bg-warning text-dark" title="{{ __('messages.session_param_differs') }}">≠</span>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $session->notes ?: '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('clinical.sessions.edit', [$patient, $session]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('clinical.sessions.destroy', [$patient, $session]) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted">{{ __('messages.no_sessions') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 fw-bold text-dark-blue mb-0">{{ __('messages.questionnaire_scores') }}</h2>
            <a href="{{ route('clinical.scores.create', $patient) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>{{ __('messages.complete_questionnaire') }}
            </a>
        </div>
        <p class="text-muted small mb-3">{{ __('messages.questionnaire_multivalue_hint') }}</p>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ __('messages.scored_at') }}</th>
                        <th>{{ __('messages.questionnaire') }}</th>
                        <th>{{ __('messages.phase') }}</th>
                        <th>{{ __('messages.score') }}</th>
                        <th>{{ __('messages.notes') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patient->questionnaireScores->sortByDesc('scored_at') as $score)
                    <tr>
                        <td>{{ $score->scored_at->format('Y-m-d') }}</td>
                        <td>{{ $score->questionnaire?->name }}</td>
                        <td><span class="badge bg-secondary">{{ __('messages.phase_'.$score->phase) }}</span></td>
                        <td class="fw-semibold">{{ $score->score }}</td>
                        <td>{{ $score->notes ?: '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('clinical.scores.edit', [$patient, $score]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('clinical.scores.destroy', [$patient, $score]) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted">{{ __('messages.no_scores') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
