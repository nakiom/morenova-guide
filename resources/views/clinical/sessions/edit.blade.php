<x-app-layout>
    <div class="container py-5">
        <a href="{{ route('clinical.patients.show', $patient) }}" class="btn btn-outline-primary mb-3">
            <i class="fas fa-arrow-left me-2"></i>{{ __('messages.back') }}
        </a>
        <h1 class="h2 fw-bold text-dark-blue mb-4">{{ __('messages.edit_session') }} — {{ $patient->name }}</h1>

        @if($pathology?->hasTreatmentParameters())
        <div class="alert alert-light border mb-3 small">
            {{ __('messages.protocol_defaults') }}:
            @if($pathology->energy_kv) {{ __('messages.param_energy') }} {{ $pathology->energy_kv }} kV @endif
            @if($pathology->frequency_hz) · {{ __('messages.param_frequency') }} {{ $pathology->frequency_hz }} Hz @endif
        </div>
        @endif

        <form action="{{ route('clinical.sessions.update', [$patient, $session]) }}" method="POST" class="card border-0 shadow-sm">
            @csrf
            @method('PUT')
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
                        <input type="number" id="session_number" class="form-control" value="{{ $session->session_number }}" min="1" readonly>
                        <small class="text-muted">{{ __('messages.session_number_auto') }}</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="session_date">{{ __('messages.session_date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="session_date" id="session_date" class="form-control" value="{{ old('session_date', $session->session_date->toDateString()) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="shocks">{{ __('messages.shocks') }} <span class="text-danger">*</span></label>
                        <input type="number" name="shocks" id="shocks" class="form-control" value="{{ old('shocks', $session->shocks) }}" min="0" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="energy_kv">{{ __('messages.param_energy') }} (kV)</label>
                        <input type="number" step="0.01" name="energy_kv" id="energy_kv" class="form-control"
                               value="{{ old('energy_kv', $session->energy_kv) }}" min="0" max="99.99">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="frequency_hz">{{ __('messages.param_frequency') }} (Hz)</label>
                        <input type="number" step="0.01" name="frequency_hz" id="frequency_hz" class="form-control"
                               value="{{ old('frequency_hz', $session->frequency_hz) }}" min="0" max="999.99">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label" for="notes">{{ __('messages.notes') }}</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes', $session->notes) }}</textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
