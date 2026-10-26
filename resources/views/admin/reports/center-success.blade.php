<x-app-layout>
    <div class="container py-5">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-end gap-3 mb-4">
            <div>
                <p class="text-muted small text-uppercase fw-semibold mb-1" style="letter-spacing:.06em;">{{ __('messages.admin_reports') }}</p>
                <h1 class="h2 fw-bold text-dark-blue mb-2">{{ __('messages.center_success_ranking') }}</h1>
                <p class="text-muted mb-0">{{ __('messages.center_success_ranking_hint') }}</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>{{ __('messages.back') }}
            </a>
        </div>

        <form method="GET" class="row g-2 align-items-end mb-4">
            <div class="col-md-6 col-lg-4">
                <label class="form-label" for="pathology_id">{{ __('messages.pathology') }}</label>
                <select name="pathology_id" id="pathology_id" class="form-select" onchange="this.form.submit()">
                    <option value="">{{ __('messages.all_pathologies') }}</option>
                    @foreach($pathologies as $pathology)
                        <option value="{{ $pathology->id }}" @selected((int) $pathologyId === (int) $pathology->id)>
                            {{ $pathology->translation($locale)?->name ?? $pathology->slug }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        @forelse($rankings as $block)
            @php
                $pathologyName = $block['pathology']->translation($locale)?->name ?? $block['pathology']->slug;
                $rows = $block['rows'];
            @endphp
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h2 class="h5 fw-bold text-dark-blue mb-0">{{ $pathologyName }}</h2>
                        <span class="badge bg-primary-subtle text-primary">
                            {{ trans_choice('messages.centers_ranked', count($rows), ['count' => count($rows)]) }}
                        </span>
                    </div>

                    @if($rows === [])
                        <p class="text-muted mb-0">{{ __('messages.center_success_no_data') }}</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:4rem;">#</th>
                                        <th>{{ __('messages.center') }}</th>
                                        <th class="text-end">{{ __('messages.success_rate') }}</th>
                                        <th class="text-end">{{ __('messages.patients_improved') }}</th>
                                        <th class="text-end">{{ __('messages.patients_evaluated') }}</th>
                                        <th class="text-end">{{ __('messages.avg_improvement') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rows as $index => $row)
                                    <tr class="{{ $index < 3 ? 'table-success' : '' }}">
                                        <td>
                                            @if($index === 0)
                                                <span class="badge bg-warning text-dark">1</span>
                                            @elseif($index < 3)
                                                <span class="badge bg-primary">{{ $index + 1 }}</span>
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $row['center']->name }}</div>
                                            @if($row['center']->location)
                                                <div class="small text-muted">{{ $row['center']->location }}</div>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold text-dark-blue">{{ number_format($row['success_rate'], 1) }}%</td>
                                        <td class="text-end">{{ $row['patients_improved'] }}</td>
                                        <td class="text-end">{{ $row['patients_evaluated'] }}</td>
                                        <td class="text-end {{ ($row['avg_delta'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $row['avg_delta'] === null ? '—' : (($row['avg_delta'] > 0 ? '+' : '').number_format($row['avg_delta'], 2)) }}
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
            <div class="alert alert-light border">{{ __('messages.center_success_no_data') }}</div>
        @endforelse

        <p class="small text-muted mb-0">{{ __('messages.center_success_method_hint') }}</p>
    </div>
</x-app-layout>
