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
                    · {{ __('messages.sex') }}: {{ $patient->sexLabel() ?? '—' }}
                    · {{ __('messages.protocol') }}: {{ $pathologyName }}
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @can('update', $patient)
                <a href="{{ route('clinical.scores.create', $patient) }}" class="btn btn-primary">
                    <i class="fas fa-clipboard-check me-1"></i>{{ __('messages.complete_questionnaire') }}
                </a>
                <a href="{{ route('clinical.invites.create', $patient) }}" class="btn btn-outline-primary">
                    <i class="fas fa-paper-plane me-1"></i>{{ __('messages.send_questionnaires_by_email') }}
                </a>
                <a href="{{ route('clinical.patients.edit', $patient) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-edit me-1"></i>{{ __('messages.edit') }}
                </a>
                @endcan
                <a href="{{ route('clinical.reports.patient', $patient) }}" class="btn btn-outline-primary">
                    <i class="fas fa-chart-line me-1"></i>{{ __('messages.evolution') }}
                </a>
                @can('delete', $patient)
                <form action="{{ route('clinical.patients.destroy', $patient) }}" method="POST" class="d-inline"
                      onsubmit="return confirm(@json(__('messages.patient_delete_confirm')))">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="fas fa-trash me-1"></i>{{ __('messages.delete') }}
                    </button>
                </form>
                @endcan
            </div>
        </div>

        @if($patient->trashed())
        <div class="alert alert-warning">
            {{ __('messages.patient_deleted_banner', ['date' => $patient->deleted_at?->timezone(config('app.timezone'))->format('d/m/Y H:i')]) }}
        </div>
        @endif

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
            @if(auth()->user()->isAdmin() || auth()->user()->center_id)
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-2">{{ __('messages.doctor') }}</div>
                        <x-doctor-chip :user="$patient->user" size="md" show-center />
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
                    <div class="col-md-6">
                        <div class="text-muted small">{{ __('messages.visit_reminder_enabled') }}</div>
                        @if($patient->visitReminderActive())
                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">
                                {{ __('messages.visit_reminder_active') }}
                            </span>
                            <div class="small text-muted mt-1">
                                {{ trans_choice('messages.visit_reminder_days_option', (int) $patient->visit_reminder_days_before, ['count' => (int) $patient->visit_reminder_days_before]) }}
                            </div>
                        @else
                            <span class="text-muted">{{ __('messages.visit_reminder_inactive') }}</span>
                        @endif
                        @if($patient->expected_next_visit_at)
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <a href="{{ route('clinical.reminders.preview', $patient) }}"
                                   class="btn btn-outline-primary btn-sm"
                                   target="_blank"
                                   rel="noopener">
                                    <i class="fas fa-eye me-1"></i>{{ __('messages.mail_preview_reminder') }}
                                </a>
                                @can('update', $patient)
                                    @if(filled($patient->email))
                                        <form method="POST" action="{{ route('clinical.reminders.send', $patient) }}" class="d-inline"
                                              onsubmit="return confirm(@json(__('messages.visit_reminder_send_confirm')));">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-paper-plane me-1"></i>{{ __('messages.visit_reminder_send_now') }}
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                            @if($patient->visit_reminder_sent_for?->toDateString() === $patient->expected_next_visit_at?->toDateString())
                                <div class="small text-muted mt-1">
                                    {{ __('messages.visit_reminder_already_sent', ['date' => $patient->visit_reminder_sent_for->format('d/m/Y')]) }}
                                </div>
                            @elseif(! filled($patient->email))
                                <div class="small text-warning mt-1">{{ __('messages.visit_reminder_needs_email') }}</div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($patient->notes)
        <div class="alert alert-light border mb-4">{{ $patient->notes }}</div>
        @endif

        <div class="card border-0 shadow-sm mb-5">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h2 class="h4 fw-bold text-dark-blue mb-1">{{ __('messages.patient_tags') }}</h2>
                        <p class="text-muted small mb-0">{{ __('messages.patient_tags_hint') }}</p>
                    </div>
                </div>

                @if($canEditTags)
                <form method="POST" action="{{ route('clinical.tags.store', $patient) }}" class="row g-2 align-items-end mb-4 pb-3 border-bottom">
                    @csrf
                    <div class="col-md-3">
                        <label class="form-label small mb-1" for="tag_type">{{ __('messages.patient_tag_type') }}</label>
                        <select name="type" id="tag_type" class="form-select form-select-sm" required>
                            @foreach(\App\Models\PatientTag::TYPES as $type)
                                <option value="{{ $type }}" @selected(old('type') === $type)>{{ __('messages.patient_tag_type_'.$type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label small mb-1" for="tag_content">{{ __('messages.patient_tag_content') }}</label>
                        <input type="text" name="content" id="tag_content" class="form-control form-control-sm"
                               value="{{ old('content') }}" maxlength="500" required
                               placeholder="{{ __('messages.patient_tag_content_placeholder') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-plus me-1"></i>{{ __('messages.add') }}
                        </button>
                    </div>
                </form>
                @endif

                @php
                    $typeOrder = \App\Models\PatientTag::TYPES;
                @endphp
                @if($patient->tags->isEmpty())
                    <p class="text-muted mb-0">{{ __('messages.patient_tags_empty') }}</p>
                @else
                    <div class="row g-3">
                        @foreach($typeOrder as $type)
                            @php $typeTags = $tagsByType->get($type, collect()); @endphp
                            @if($typeTags->isNotEmpty())
                            <div class="col-md-4">
                                <div class="small text-uppercase fw-semibold text-primary mb-2" style="letter-spacing:.04em;">
                                    {{ __('messages.patient_tag_type_'.$type) }}
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    @foreach($typeTags as $tag)
                                    <div class="border rounded-3 p-2 bg-light bg-opacity-50">
                                        <div class="d-flex justify-content-between align-items-start gap-2">
                                            <div class="flex-grow-1">
                                                <div class="fw-medium text-dark-blue">{{ $tag->content }}</div>
                                                <div class="small text-muted">
                                                    {{ $tag->user?->name }}
                                                    · {{ $tag->updated_at?->timezone(config('app.timezone'))->format('d/m/Y') }}
                                                </div>
                                            </div>
                                            @if($canEditTags)
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                        data-bs-toggle="collapse" data-bs-target="#edit-tag-{{ $tag->id }}"
                                                        aria-expanded="false" title="{{ __('messages.edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form method="POST" action="{{ route('clinical.tags.destroy', [$patient, $tag]) }}"
                                                      onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('messages.delete') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            @endif
                                        </div>
                                        @if($canEditTags)
                                        <div class="collapse mt-2" id="edit-tag-{{ $tag->id }}">
                                            <form method="POST" action="{{ route('clinical.tags.update', [$patient, $tag]) }}" class="row g-2">
                                                @csrf
                                                @method('PUT')
                                                <div class="col-12">
                                                    <select name="type" class="form-select form-select-sm" required>
                                                        @foreach(\App\Models\PatientTag::TYPES as $editType)
                                                            <option value="{{ $editType }}" @selected($tag->type === $editType)>
                                                                {{ __('messages.patient_tag_type_'.$editType) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <input type="text" name="content" class="form-control form-control-sm"
                                                           value="{{ $tag->content }}" maxlength="500" required>
                                                </div>
                                                <div class="col-12">
                                                    <button type="submit" class="btn btn-sm btn-primary">{{ __('messages.save') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 fw-bold text-dark-blue mb-0">{{ __('messages.treatment_sessions') }}</h2>
            @can('update', $patient)
            <a href="{{ route('clinical.sessions.create', $patient) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>{{ __('messages.add_session') }}
            </a>
            @endcan
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
                            @can('update', $patient)
                            <a href="{{ route('clinical.sessions.edit', [$patient, $session]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('clinical.sessions.destroy', [$patient, $session]) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted">{{ __('messages.no_sessions') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($patient->questionnaireInvites->isNotEmpty())
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 fw-bold text-dark-blue mb-0">{{ __('messages.questionnaire_invites') }}</h2>
        </div>
        <div class="table-responsive mb-5">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ __('messages.sent_at') }}</th>
                        <th>{{ __('messages.email') }}</th>
                        <th>{{ __('messages.phase') }}</th>
                        <th>{{ __('messages.questionnaires_to_send') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.mail_visited_at') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patient->questionnaireInvites as $invite)
                    <tr>
                        <td>{{ $invite->created_at?->format('d/m/Y H:i') }}</td>
                        <td>{{ $invite->email }}</td>
                        <td><span class="badge bg-secondary">{{ __('messages.phase_'.$invite->phase) }}</span></td>
                        <td>
                            {{ $invite->completedCount() }}/{{ $invite->items->count() }}
                            <div class="small text-muted">
                                {{ $invite->items->pluck('questionnaire.name')->filter()->implode(', ') }}
                            </div>
                        </td>
                        <td>
                            @php $status = $invite->statusKey(); @endphp
                            <span class="badge
                                {{ $status === 'completed' ? 'bg-success' : ($status === 'visited' ? 'bg-info text-dark' : ($status === 'expired' ? 'bg-secondary' : 'bg-warning text-dark')) }}">
                                {{ __('messages.questionnaire_invite_status_'.$status) }}
                            </span>
                        </td>
                        <td class="small">
                            @if($invite->visited_at)
                                {{ $invite->visited_at->format('d/m/Y H:i') }}
                                <div class="text-muted">{{ __('messages.mail_visit_count', ['count' => $invite->visit_count]) }}</div>
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-end">
                            @if($invite->isOpen())
                            @can('update', $patient)
                            <form action="{{ route('clinical.invites.resend', [$patient, $invite]) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-outline-primary" type="submit">
                                    <i class="fas fa-redo me-1"></i>{{ __('messages.resend_invite') }}
                                </button>
                            </form>
                            @endcan
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 fw-bold text-dark-blue mb-0">{{ __('messages.questionnaire_scores') }}</h2>
            @can('update', $patient)
            <a href="{{ route('clinical.scores.create', $patient) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>{{ __('messages.complete_questionnaire') }}
            </a>
            @endcan
        </div>
        <p class="text-muted small mb-3">{{ __('messages.questionnaire_multivalue_hint') }}</p>

        @if(!empty($scoreCharts))
        <div class="mb-4">
            <h3 class="h5 fw-bold text-dark-blue mb-1">{{ __('messages.questionnaire_score_charts') }}</h3>
            <p class="text-muted small mb-3">{{ __('messages.questionnaire_score_charts_hint') }}</p>
            <div class="row g-3">
                @foreach($scoreCharts as $chart)
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <h4 class="h6 fw-bold text-dark-blue mb-0">{{ $chart['name'] }}</h4>
                                <span class="badge bg-primary-subtle text-primary">{{ count($chart['points']) }}</span>
                            </div>
                            <div class="patient-score-chart-wrap">
                                <canvas
                                    id="score-chart-{{ $chart['id'] }}"
                                    class="patient-score-chart"
                                    aria-label="{{ __('messages.questionnaire_score_charts') }}: {{ $chart['name'] }}"
                                    role="img"
                                ></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ __('messages.scored_at') }}</th>
                        <th>{{ __('messages.questionnaire') }}</th>
                        <th>{{ __('messages.phase') }}</th>
                        <th>{{ __('messages.score') }}</th>
                        <th>{{ __('messages.notes') }}</th>
                        <th>{{ __('messages.questionnaire_patient_message') }}</th>
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
                        <td>
                            @if($score->notes)
                                <span class="d-inline-block text-break" style="max-width: 14rem;">{{ $score->notes }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($score->patient_message)
                                <span class="d-inline-block text-break" style="max-width: 16rem;">{{ $score->patient_message }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-end">
                            @can('update', $patient)
                            <a href="{{ route('clinical.scores.edit', [$patient, $score]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('clinical.scores.destroy', [$patient, $score]) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted">{{ __('messages.no_scores') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(!empty($scoreCharts))
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Chart === 'undefined') return;

            const charts = @json($scoreCharts);
            const brand = {
                line: '#124C78',
                fill: 'rgba(82, 224, 215, 0.18)',
                point: '#52E0D7',
                grid: 'rgba(18, 76, 120, 0.08)',
                text: '#54595F',
            };
            const phaseColors = {
                pre: '#6EC1E4',
                post: '#124C78',
                control: '#52E0D7',
            };

            charts.forEach(function (chart) {
                const canvas = document.getElementById('score-chart-' + chart.id);
                if (!canvas) return;

                const labels = chart.points.map(function (p) { return p.date; });
                const values = chart.points.map(function (p) { return p.score; });
                const pointColors = chart.points.map(function (p) {
                    return phaseColors[p.phase] || brand.point;
                });

                new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: @json(__('messages.score')),
                            data: values,
                            borderColor: brand.line,
                            backgroundColor: brand.fill,
                            pointBackgroundColor: pointColors,
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            borderWidth: 2.5,
                            tension: 0.25,
                            fill: true,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    afterLabel: function (ctx) {
                                        const point = chart.points[ctx.dataIndex];
                                        return point ? point.phase_label : '';
                                    },
                                },
                            },
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: @json(__('messages.scored_at')),
                                    color: brand.text,
                                },
                                ticks: { color: brand.text, maxRotation: 45, minRotation: 0 },
                                grid: { color: brand.grid },
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: @json(__('messages.score')),
                                    color: brand.text,
                                },
                                ticks: { color: brand.text },
                                grid: { color: brand.grid },
                                beginAtZero: false,
                            },
                        },
                    },
                });
            });
        });
    </script>
    <style>
        .patient-score-chart-wrap {
            position: relative;
            height: 240px;
        }
    </style>
    @endif
</x-app-layout>
