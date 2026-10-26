@php
    $pathologyName = $patient->pathology?->translation($locale)?->name ?? $patient->pathology?->slug;
@endphp
<x-app-layout>
    <div class="container py-5">
        <a href="{{ route('clinical.reports.index') }}" class="btn btn-outline-primary mb-3">
            <i class="fas fa-arrow-left me-2"></i>{{ __('messages.back') }}
        </a>
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h1 class="h2 fw-bold text-dark-blue mb-1">{{ __('messages.evolution') }}: {{ $patient->anonymousCode() }}</h1>
                <p class="text-muted mb-0">{{ $pathologyName }} · {{ __('messages.reports_anonymous_hint') }}</p>
            </div>
            <a href="{{ route('clinical.patients.show', $patient) }}" class="btn btn-outline-secondary">{{ __('messages.view_patient') }}</a>
        </div>

        @forelse($scoresByQuestionnaire as $questionnaireId => $scores)
        @php
            $questionnaire = $scores->first()->questionnaire;
            $chartPoints = $scores->sortBy('scored_at')->values()->map(fn ($score) => [
                'date' => $score->scored_at?->timezone(config('app.timezone'))->format('Y-m-d') ?? '',
                'score' => $score->score !== null ? (float) $score->score : null,
                'phase' => $score->phase,
                'phase_label' => __('messages.phase_'.$score->phase),
            ])->filter(fn (array $p) => $p['date'] !== '' && $p['score'] !== null)->values();
        @endphp
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h5 fw-bold text-dark-blue mb-3">{{ $questionnaire?->name }}</h2>
                @if($chartPoints->isNotEmpty())
                <div class="patient-score-chart-wrap mb-3">
                    <canvas
                        id="evolution-chart-{{ $questionnaireId }}"
                        class="patient-score-chart"
                        data-chart='@json($chartPoints)'
                        aria-label="{{ $questionnaire?->name }}"
                        role="img"
                    ></canvas>
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('messages.scored_at') }}</th>
                                <th>{{ __('messages.phase') }}</th>
                                <th>{{ __('messages.score') }}</th>
                                <th>{{ __('messages.delta_vs_previous') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $prev = null; @endphp
                            @foreach($scores->sortBy('scored_at') as $score)
                            @php
                                $delta = $prev !== null ? (float) $score->score - (float) $prev : null;
                                $prev = (float) $score->score;
                            @endphp
                            <tr>
                                <td>{{ $score->scored_at->format('Y-m-d') }}</td>
                                <td>{{ __('messages.phase_'.$score->phase) }}</td>
                                <td class="fw-semibold">{{ $score->score }}</td>
                                <td>
                                    @if($delta !== null)
                                        <span class="{{ $delta >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $delta > 0 ? '+' : '' }}{{ number_format($delta, 2) }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @empty
        <div class="alert alert-info">{{ __('messages.no_scores') }}</div>
        @endforelse
    </div>

    @if($scoresByQuestionnaire->isNotEmpty())
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Chart === 'undefined') return;
            const brand = {
                line: '#124C78',
                fill: 'rgba(82, 224, 215, 0.18)',
                grid: 'rgba(18, 76, 120, 0.08)',
                text: '#54595F',
            };
            const phaseColors = { pre: '#6EC1E4', post: '#124C78', control: '#52E0D7' };

            document.querySelectorAll('[id^="evolution-chart-"]').forEach(function (canvas) {
                let points = [];
                try { points = JSON.parse(canvas.getAttribute('data-chart') || '[]'); } catch (e) { return; }
                if (!points.length) return;

                new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: points.map(function (p) { return p.date; }),
                        datasets: [{
                            label: @json(__('messages.score')),
                            data: points.map(function (p) { return p.score; }),
                            borderColor: brand.line,
                            backgroundColor: brand.fill,
                            pointBackgroundColor: points.map(function (p) { return phaseColors[p.phase] || brand.line; }),
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            borderWidth: 2.5,
                            tension: 0.25,
                            fill: true,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    afterLabel: function (ctx) {
                                        return points[ctx.dataIndex]?.phase_label || '';
                                    },
                                },
                            },
                        },
                        scales: {
                            x: {
                                title: { display: true, text: @json(__('messages.scored_at')), color: brand.text },
                                ticks: { color: brand.text },
                                grid: { color: brand.grid },
                            },
                            y: {
                                title: { display: true, text: @json(__('messages.score')), color: brand.text },
                                ticks: { color: brand.text },
                                grid: { color: brand.grid },
                            },
                        },
                    },
                });
            });
        });
    </script>
    <style>.patient-score-chart-wrap{position:relative;height:240px;}</style>
    @endif
</x-app-layout>
