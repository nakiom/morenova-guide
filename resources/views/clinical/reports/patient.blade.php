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
        @php $questionnaire = $scores->first()->questionnaire; @endphp
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h5 fw-bold text-dark-blue mb-3">{{ $questionnaire?->name }}</h2>
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
</x-app-layout>
