<x-app-layout>
    <div class="container py-5">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h1 class="h2 fw-bold text-dark-blue mb-1">{{ __('messages.clinical_reports') }}</h1>
                <p class="text-muted mb-0">{{ __('messages.clinical_reports_hint') }} · {{ __('messages.reports_anonymous_hint') }}</p>
            </div>
            <a href="{{ route('clinical.patients.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-users me-1"></i>{{ __('messages.clinical_patients') }}
            </a>
        </div>

        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-6">
                <select name="pathology_id" class="form-select" onchange="this.form.submit()">
                    <option value="">{{ __('messages.all_protocols') }}</option>
                    @foreach($pathologies as $pathology)
                    <option value="{{ $pathology->id }}" @selected($pathologyId == $pathology->id)>
                        {{ $pathology->translation()?->name ?? $pathology->slug }}
                    </option>
                    @endforeach
                </select>
            </div>
        </form>

        <h2 class="h4 fw-bold text-dark-blue mb-3">{{ __('messages.protocol_stats') }}</h2>
        <div class="table-responsive mb-5">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>{{ __('messages.questionnaire') }}</th>
                        <th>{{ __('messages.protocol') }}</th>
                        <th>{{ __('messages.phase_pre') }} (n)</th>
                        <th>{{ __('messages.phase_post') }} (n)</th>
                        <th>{{ __('messages.phase_control') }} (n)</th>
                        <th>{{ __('messages.delta_avg') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($protocolStats as $stat)
                    <tr>
                        <td>{{ $stat['questionnaire']->name }}</td>
                        <td>{{ $stat['questionnaire']->pathology?->translation()?->name ?? $stat['questionnaire']->pathology?->slug }}</td>
                        <td>{{ $stat['pre_avg'] !== null ? $stat['pre_avg'].' ('.$stat['pre_n'].')' : '—' }}</td>
                        <td>{{ $stat['post_avg'] !== null ? $stat['post_avg'].' ('.$stat['post_n'].')' : '—' }}</td>
                        <td>{{ $stat['control_avg'] !== null ? $stat['control_avg'].' ('.$stat['control_n'].')' : '—' }}</td>
                        <td>
                            @if($stat['delta_avg'] !== null)
                                <span class="fw-semibold {{ $stat['delta_avg'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $stat['delta_avg'] > 0 ? '+' : '' }}{{ $stat['delta_avg'] }}
                                </span>
                                <small class="text-muted">(n={{ $stat['delta_n'] }})</small>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted">{{ __('messages.no_report_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h2 class="h4 fw-bold text-dark-blue mb-3">{{ __('messages.patient_evolution') }}</h2>
        <div class="list-group">
            @forelse($patients as $patient)
            <a href="{{ route('clinical.reports.patient', $patient) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <span>
                    <strong>{{ $patient->anonymousCode() }}</strong>
                    <span class="text-muted">— {{ $patient->pathology?->translation()?->name ?? $patient->pathology?->slug }}</span>
                </span>
                <i class="fas fa-chevron-right text-muted"></i>
            </a>
            @empty
            <div class="text-muted">{{ __('messages.no_patients') }}</div>
            @endforelse
        </div>
    </div>
</x-app-layout>
