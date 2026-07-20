<x-app-layout>
    <div class="container py-5">
        <a href="{{ route('clinical.patients.show', $patient) }}" class="btn btn-outline-primary mb-3">
            <i class="fas fa-arrow-left me-2"></i>{{ __('messages.back') }}
        </a>
        <h1 class="h2 fw-bold text-dark-blue mb-4">{{ __('messages.add_session') }} — {{ $patient->name }}</h1>

        @if(session('error'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if($pathology?->hasTreatmentParameters())
        <div class="card border-0 shadow-sm mb-4 protocol-defaults-card">
            <div class="card-body py-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                    <h2 class="h6 fw-bold text-dark-blue mb-0">
                        <i class="fas fa-sliders-h me-2 text-primary"></i>{{ __('messages.protocol_defaults') }}
                    </h2>
                    <span class="badge bg-primary-subtle text-primary">
                        {{ $pathology->translation()?->name ?? $pathology->slug }}
                    </span>
                </div>
                <div class="row g-2 small">
                    @if($pathology->shots_per_session)
                    <div class="col-6 col-md-3">
                        <div class="text-muted">{{ __('messages.param_shots_per_session') }}</div>
                        <div class="fw-semibold">{{ number_format($pathology->shots_per_session, 0, ',', '.') }}</div>
                    </div>
                    @endif
                    @if($pathology->energy_kv)
                    <div class="col-6 col-md-3">
                        <div class="text-muted">{{ __('messages.param_energy') }}</div>
                        <div class="fw-semibold">{{ $pathology->energy_kv }} kV</div>
                    </div>
                    @endif
                    @if($pathology->frequency_hz)
                    <div class="col-6 col-md-3">
                        <div class="text-muted">{{ __('messages.param_frequency') }}</div>
                        <div class="fw-semibold">{{ $pathology->frequency_hz }} Hz</div>
                    </div>
                    @endif
                    @if($pathology->transducer)
                    <div class="col-6 col-md-3">
                        <div class="text-muted">{{ __('messages.param_transducer') }}</div>
                        <div class="fw-semibold">{{ $pathology->transducer }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <form action="{{ route('clinical.sessions.store', $patient) }}" method="POST" class="card border-0 shadow-sm">
            @csrf
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="session_number">{{ __('messages.session_number') }}</label>
                        <input type="number" id="session_number" class="form-control" value="{{ $nextNumber }}" min="1" readonly>
                        <small class="text-muted">{{ __('messages.session_number_auto') }}</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="session_date">{{ __('messages.session_date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="session_date" id="session_date" class="form-control" value="{{ old('session_date', now()->toDateString()) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="shocks">{{ __('messages.shocks') }} <span class="text-danger">*</span></label>
                        <input type="number" name="shocks" id="shocks" class="form-control" value="{{ old('shocks', $defaultShocks) }}" min="0" required>
                        @if($defaultShocks > 0)
                        <small class="text-muted">{{ __('messages.protocol_default_shocks_hint', ['value' => number_format($defaultShocks, 0, ',', '.')]) }}</small>
                        @endif
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="energy_kv">{{ __('messages.param_energy') }} (kV)</label>
                        <input type="number" step="0.01" name="energy_kv" id="energy_kv" class="form-control"
                               value="{{ old('energy_kv', $defaultEnergy) }}" min="0" max="99.99">
                        @if($defaultEnergy !== null)
                        <small class="text-muted">{{ __('messages.protocol_default_value_hint', ['value' => $defaultEnergy.' kV']) }}</small>
                        @endif
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="frequency_hz">{{ __('messages.param_frequency') }} (Hz)</label>
                        <input type="number" step="0.01" name="frequency_hz" id="frequency_hz" class="form-control"
                               value="{{ old('frequency_hz', $defaultFrequency) }}" min="0" max="999.99">
                        @if($defaultFrequency !== null)
                        <small class="text-muted">{{ __('messages.protocol_default_value_hint', ['value' => $defaultFrequency.' Hz']) }}</small>
                        @endif
                    </div>
                    <div class="col-12 mb-2">
                        <p class="small text-muted mb-0">{{ __('messages.session_params_override_hint') }}</p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="notes">{{ __('messages.notes') }}</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="expected_next_visit_at">{{ __('messages.expected_next_visit') }}</label>
                        <input type="date" name="expected_next_visit_at" id="expected_next_visit_at" class="form-control"
                               value="{{ old('expected_next_visit_at', $patient->expected_next_visit_at?->toDateString()) }}">
                        <small class="text-muted">{{ __('messages.after_session_next_visit_hint') }}</small>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
