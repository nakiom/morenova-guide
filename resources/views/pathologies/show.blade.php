<x-app-layout>
    <div class="container py-5">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row mb-4 page-header">
            <div class="col-12">
                <a href="{{ route('pathologies.index', $category->slug) }}" class="btn btn-outline-primary btn-back">
                    <i class="fas fa-arrow-left me-2"></i>{{ __('messages.back') }}
                </a>
                @php
                    $pathologyTranslation = $pathology->translation($locale);
                @endphp
                <h1 class="h2 h1-md display-5 fw-bold text-dark-blue mb-2">
                    <x-pathology-icon :pathology="$pathology" class="pathology-icon-img pathology-icon-img--inline" size="2.5rem" />
                    {{ $pathologyTranslation ? $pathologyTranslation->name : $pathology->slug }}
                </h1>
                @if($pathologyTranslation && $pathologyTranslation->description)
                <p class="lead text-muted">{{ $pathologyTranslation->description }}</p>
                @endif
            </div>
        </div>

        <!-- Criterios de Inclusión y Exclusión -->
        <div class="row mb-4" data-stagger>
            <div class="col-12 col-md-6 mb-4">
                <div class="card criteria-card inclusion h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>{{ __('messages.inclusion_criteria') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            @forelse($pathology->inclusionCriteria as $criterion)
                            @php
                                $criterionTranslation = $criterion->translation($locale);
                            @endphp
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                {{ $criterionTranslation ? $criterionTranslation->text : '' }}
                            </li>
                            @empty
                            <li class="text-muted">{{ __('messages.no_criteria') }}</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 mb-4">
                <div class="card criteria-card exclusion h-100">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-times-circle me-2"></i>{{ __('messages.exclusion_criteria') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            @forelse($pathology->exclusionCriteria as $criterion)
                            @php
                                $criterionTranslation = $criterion->translation($locale);
                            @endphp
                            <li class="mb-2">
                                <i class="fas fa-times text-danger me-2"></i>
                                {{ $criterionTranslation ? $criterionTranslation->text : '' }}
                            </li>
                            @empty
                            <li class="text-muted">{{ __('messages.no_criteria') }}</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        @if($pathology->hasTreatmentParameters())
        <div class="row mb-4">
            <div class="col-12">
                <div class="card treatment-parameters-card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white d-flex align-items-center gap-2">
                        <i class="fas fa-sliders-h"></i>
                        <div>
                            <h2 class="h5 mb-0">{{ __('messages.treatment_parameters') }}</h2>
                            <small class="opacity-75">{{ __('messages.treatment_parameters_hint') }}</small>
                        </div>
                    </div>
                    @php
                        $hasTiles = $pathology->sessions_count || $pathology->shots_per_session || $pathology->applications_per_session || $pathology->session_duration_minutes;
                        $hasSecondary = $pathology->sessions_frequency || $pathology->energy_kv || $pathology->frequency_hz || $pathology->transducer;
                    @endphp
                    <div class="card-body">
                        @if($hasTiles)
                        <div class="row g-3">
                            @if($pathology->sessions_count)
                            <div class="col-6 col-md-3">
                                <div class="param-tile bg-light rounded p-3 h-100 text-center">
                                    <div class="param-label text-uppercase small text-muted fw-semibold">
                                        <i class="fas fa-calendar-alt me-1"></i>{{ __('messages.param_sessions') }}
                                    </div>
                                    <div class="param-value h3 fw-bold text-dark-blue mb-0 mt-2">{{ $pathology->sessions_count }}</div>
                                </div>
                            </div>
                            @endif
                            @if($pathology->shots_per_session)
                            <div class="col-6 col-md-3">
                                <div class="param-tile bg-light rounded p-3 h-100 text-center">
                                    <div class="param-label text-uppercase small text-muted fw-semibold">
                                        <i class="fas fa-bolt me-1"></i>{{ __('messages.param_shots_per_session') }}
                                    </div>
                                    <div class="param-value h3 fw-bold text-dark-blue mb-0 mt-2">
                                        {{ number_format($pathology->shots_per_session, 0, ',', '.') }}
                                        <span class="fs-6 fw-normal text-muted">{{ __('messages.unit_shots') }}</span>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if($pathology->applications_per_session && $pathology->shots_per_application)
                            <div class="col-6 col-md-3">
                                <div class="param-tile bg-light rounded p-3 h-100 text-center">
                                    <div class="param-label text-uppercase small text-muted fw-semibold">
                                        <i class="fas fa-crosshairs me-1"></i>{{ __('messages.param_applications_per_session') }}
                                    </div>
                                    <div class="param-value h3 fw-bold text-dark-blue mb-0 mt-2">
                                        {{ $pathology->applications_per_session }}
                                        <span class="fs-6 fw-normal text-muted">× {{ $pathology->shots_per_application }} {{ __('messages.unit_shots') }}</span>
                                    </div>
                                </div>
                            </div>
                            @elseif($pathology->applications_per_session)
                            <div class="col-6 col-md-3">
                                <div class="param-tile bg-light rounded p-3 h-100 text-center">
                                    <div class="param-label text-uppercase small text-muted fw-semibold">
                                        <i class="fas fa-crosshairs me-1"></i>{{ __('messages.param_applications_per_session') }}
                                    </div>
                                    <div class="param-value h3 fw-bold text-dark-blue mb-0 mt-2">{{ $pathology->applications_per_session }}</div>
                                </div>
                            </div>
                            @endif
                            @if($pathology->session_duration_minutes)
                            <div class="col-6 col-md-3">
                                <div class="param-tile bg-light rounded p-3 h-100 text-center">
                                    <div class="param-label text-uppercase small text-muted fw-semibold">
                                        <i class="fas fa-clock me-1"></i>{{ __('messages.param_session_duration') }}
                                    </div>
                                    <div class="param-value h3 fw-bold text-dark-blue mb-0 mt-2">
                                        {{ $pathology->session_duration_minutes }}
                                        <span class="fs-6 fw-normal text-muted">{{ __('messages.unit_minutes') }}</span>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif

                        @if($hasTiles)<hr class="my-3">@endif
                        <div class="treatment-parameters-body">
                            @if($hasSecondary)
                            <div class="treatment-parameters-meta">
                                <div class="row g-3 small">
                                    @if($pathology->sessions_frequency)
                                    <div class="col-6 col-md-4">
                                        <div class="text-muted text-uppercase fw-semibold">{{ __('messages.param_sessions_frequency') }}</div>
                                        <div class="fw-semibold">{{ $pathology->sessions_frequency }}</div>
                                    </div>
                                    @endif
                                    @if($pathology->energy_kv)
                                    <div class="col-6 col-md-4">
                                        <div class="text-muted text-uppercase fw-semibold">{{ __('messages.param_energy') }}</div>
                                        <div class="fw-semibold">{{ number_format((float) $pathology->energy_kv, 1, ',', '.') }} kV</div>
                                    </div>
                                    @endif
                                    @if($pathology->frequency_hz)
                                    <div class="col-6 col-md-4">
                                        <div class="text-muted text-uppercase fw-semibold">{{ __('messages.param_frequency') }}</div>
                                        <div class="fw-semibold">{{ number_format((float) $pathology->frequency_hz, 1, ',', '.') }} Hz</div>
                                    </div>
                                    @endif
                                    @if($pathology->transducer)
                                    <div class="col-12">
                                        <div class="text-muted text-uppercase fw-semibold mb-2">{{ __('messages.param_transducer') }}</div>
                                        <div class="d-flex flex-wrap align-items-center gap-3">
                                            <div class="fw-semibold fs-5">{{ $pathology->transducer }}</div>
                                            @if($pathology->transducerImageUrl())
                                            <img src="{{ $pathology->transducerImageUrl() }}"
                                                 alt="{{ $pathology->transducer }}"
                                                 class="transducer-image">
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                            <x-device-kv-hint class="treatment-parameters-lcd" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Áreas de aplicación -->
        @if($pathology->applicationAreas->isNotEmpty())
        <div class="row mb-4">
            <div class="col-12">
                <div class="card application-areas-card border-0 shadow-sm">
                    <div class="card-header bg-dark-blue text-white d-flex align-items-center gap-2">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h2 class="h5 mb-0">{{ __('messages.application_areas') }}</h2>
                            <small class="opacity-75">{{ __('messages.application_areas_hint') }}</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($pathology->applicationAreas as $area)
                            @php $areaTranslation = $area->translation($locale); @endphp
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="application-area-item h-100 p-3 rounded">
                                    <div class="d-flex align-items-start gap-3">
                                        <span class="badge bg-primary rounded-pill">{{ $area->order }}</span>
                                        <div>
                                            <div class="fw-semibold text-dark-blue">{{ $areaTranslation?->title }}</div>
                                            @if($areaTranslation?->description)
                                            <div class="small text-muted mt-1">{{ $areaTranslation->description }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Verificación previa a la sesión -->
        @php
            $checklistIds = $pathology->sessionChecklistItems->pluck('id')->values();
        @endphp
        <div class="row mb-2">
            <div class="col-12">
                <h2 class="h4 fw-bold text-dark-blue mb-1">
                    <i class="fas fa-clipboard-check me-2"></i>{{ __('messages.session_checklist') }}
                </h2>
                <p class="text-muted small mb-0">{{ __('messages.session_checklist_hint') }}</p>
            </div>
        </div>

        @if($pathology->sessionChecklistItems->isNotEmpty())
        <div class="row mb-4" data-pathology-progress data-pathology-id="{{ $pathology->id }}" data-checklist-ids='@json($checklistIds)'>
            <div class="col-12">
                <div class="card session-checklist-card border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                            <div class="d-flex align-items-center gap-3">
                                <span class="pathology-progress-percent h3 fw-bold text-primary mb-0">0%</span>
                                <span class="text-muted small pathology-progress-count">0 / {{ $checklistIds->count() }}</span>
                            </div>
                            <div class="d-flex flex-wrap gap-1">
                                <button type="button" class="btn btn-success btn-sm pathology-progress-check-all">
                                    <i class="fas fa-check-double me-1"></i>{{ __('messages.check_all') }}
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm pathology-progress-reset">
                                    <i class="fas fa-rotate-left me-1"></i>{{ __('messages.reset_progress') }}
                                </button>
                            </div>
                        </div>
                        <div class="progress pathology-progress-track mb-3" style="height: 8px;">
                            <div class="progress-bar pathology-progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                 role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>

                        <ul class="list-group list-group-flush session-checklist-list">
                            @foreach($pathology->sessionChecklistItems as $item)
                            @php $itemTranslation = $item->translation($locale); @endphp
                            <li class="list-group-item session-checklist-item px-2 py-2 border-0"
                                data-checklist-row="{{ $item->id }}">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary rounded-pill checklist-order-badge">{{ $item->order }}</span>
                                    <span class="flex-grow-1 small checklist-item-text">{{ $itemTranslation?->text }}</span>
                                    <button type="button"
                                            class="btn btn-outline-primary btn-sm pathology-checklist-done-btn flex-shrink-0"
                                            data-checklist-done="{{ $item->id }}"
                                            data-label-done="{{ __('messages.mark_done') }}"
                                            data-label-undo="{{ __('messages.mark_undo') }}">
                                        <i class="fas fa-circle me-1"></i>{{ __('messages.mark_done') }}
                                    </button>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info text-center py-2 mb-0">
                    <p class="mb-0 small">{{ __('messages.no_session_checklist') }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Pasos del Procedimiento -->
        <div class="row mb-3">
            <div class="col-12">
                <h2 class="fw-bold text-dark-blue mb-4">
                    <i class="fas fa-list-ol me-2"></i>{{ __('messages.steps') }}
                </h2>
            </div>
        </div>

        @forelse($pathology->steps as $step)
        @php
            $stepTranslation = $step->translation($locale);
        @endphp
        <div class="row mb-4">
            <div class="col-12">
                <div class="card step-card">
                    <div class="row g-0">
                        @if($step->image_path)
                        <div class="col-12 col-md-4">
                            <img src="{{ asset('storage/' . $step->image_path) }}" class="step-image w-100" alt="{{ $stepTranslation ? $stepTranslation->title : 'Step' }}">
                        </div>
                        @endif
                        <div class="col-12 col-md-{{ $step->image_path ? '8' : '12' }}">
                            <div class="card-body">
                                <h3 class="card-title text-dark-blue">
                                    {{ __('messages.step') }} {{ $step->order }}: {{ $stepTranslation ? $stepTranslation->title : '' }}
                                </h3>
                                <p class="card-text">{{ $stepTranslation ? $stepTranslation->description : '' }}</p>
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#suggestionModal{{ $step->id }}">
                                    <i class="fas fa-lightbulb me-2"></i>{{ __('messages.send_suggestion') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Sugerencia -->
        <div class="modal fade" id="suggestionModal{{ $step->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content feedback-modal">
                    <div class="modal-header feedback-modal-header">
                        <div>
                            <p class="feedback-eyebrow mb-1">{{ __('messages.step') }} {{ $step->order }}</p>
                            <h5 class="modal-title mb-0">{{ __('messages.send_suggestion') }}</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('suggestions.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="step_id" value="{{ $step->id }}">
                        <input type="hidden" name="pathology_id" value="{{ $pathology->id }}">
                        <div class="modal-body">
                            <div class="row g-3">
                                @auth
                                <input type="hidden" name="user_name" value="{{ Auth::user()->name }}">
                                <input type="hidden" name="user_email" value="{{ Auth::user()->email }}">
                                <div class="col-12">
                                    <div class="alert alert-light border small mb-0">
                                        {{ __('messages.suggestion_as_user', ['name' => Auth::user()->name]) }}
                                    </div>
                                </div>
                                @else
                                <div class="col-12 col-md-6">
                                    <label for="user_name{{ $step->id }}" class="form-label">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                                    <div class="input-icon-wrap">
                                        <i class="fas fa-user"></i>
                                        <input type="text" class="form-control" id="user_name{{ $step->id }}" name="user_name" value="{{ old('user_name') }}" required>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="contact{{ $step->id }}" class="form-label">{{ __('messages.contact_method') }} <span class="text-muted small">({{ __('messages.optional') }})</span></label>
                                    <div class="input-icon-wrap">
                                        <i class="fas fa-phone-alt"></i>
                                        <input type="text" class="form-control" id="contact{{ $step->id }}" name="contact" value="{{ old('contact') }}" placeholder="{{ __('messages.contact_method_placeholder') }}" autocomplete="tel">
                                    </div>
                                    <small class="text-muted">{{ __('messages.contact_method_hint') }}</small>
                                </div>
                                @endauth
                                <div class="col-12">
                                    <label for="doctor_name{{ $step->id }}" class="form-label">{{ __('messages.doctor_name') }}</label>
                                    <div class="input-icon-wrap">
                                        <i class="fas fa-user-md"></i>
                                        <input type="text" class="form-control" id="doctor_name{{ $step->id }}" name="doctor_name" value="{{ old('doctor_name') }}" placeholder="{{ __('messages.doctor_name_placeholder') }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="message{{ $step->id }}" class="form-label">{{ __('messages.suggestion') }} <span class="text-danger">*</span></label>
                                    <textarea class="form-control feedback-textarea" id="message{{ $step->id }}" name="message" rows="4" required placeholder="{{ __('messages.suggestion_placeholder') }}">{{ old('message') }}</textarea>
                                </div>
                                @guest
                                <div class="col-12 col-md-6">
                                    <label for="captcha{{ $step->id }}" class="form-label">{{ __('messages.suggestion_captcha', ['question' => \App\Support\SuggestionCaptcha::question()]) }} <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="captcha{{ $step->id }}" name="captcha_answer" inputmode="numeric" required autocomplete="off">
                                    <small class="text-muted">{{ __('messages.suggestion_captcha_hint') }}</small>
                                </div>
                                <div class="hp-field" aria-hidden="true">
                                    <label for="website{{ $step->id }}">Website</label>
                                    <input type="text" id="website{{ $step->id }}" name="website" tabindex="-1" autocomplete="off">
                                </div>
                                @endguest
                            </div>
                        </div>
                        <div class="modal-footer feedback-modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>{{ __('messages.submit') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <p class="mb-0">{{ __('messages.no_steps') }}</p>
                </div>
            </div>
        </div>
        @endforelse

        <!-- Seguimiento -->
        <div class="row mb-3 mt-5">
            <div class="col-12">
                <h2 class="fw-bold text-dark-blue mb-0">
                    <i class="fas fa-calendar-check me-2"></i>{{ __('messages.follow_up') }}
                </h2>
                <p class="text-muted mb-0">{{ __('messages.follow_up_hint') }}</p>
            </div>
        </div>

        @forelse($pathology->followUpItems as $followUp)
        @php
            $followUpTranslation = $followUp->translation($locale);
        @endphp
        <div class="row mb-3">
            <div class="col-12">
                <div class="card follow-up-item border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3">
                            <span class="badge bg-secondary rounded-pill">{{ $followUp->order }}</span>
                            <div>
                                <h3 class="h5 text-dark-blue mb-2">
                                    {{ $followUpTranslation ? $followUpTranslation->title : '' }}
                                </h3>
                                @if($followUpTranslation && $followUpTranslation->description)
                                <p class="mb-0 text-muted">{{ $followUpTranslation->description }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <p class="mb-0">{{ __('messages.no_follow_up') }}</p>
                </div>
            </div>
        </div>
        @endforelse

        <!-- Formulario de Feedback General -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card feedback-panel border-0">
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4 align-items-start">
                            <div class="col-12 col-lg-4">
                                <div class="feedback-panel-intro">
                                    <span class="feedback-icon-badge">
                                        <i class="fas fa-comment-dots"></i>
                                    </span>
                                    <h2 class="h4 fw-bold text-dark-blue mb-2">{{ __('messages.send_feedback') }}</h2>
                                    <p class="text-muted mb-0">{{ __('messages.feedback_panel_hint') }}</p>
                                </div>
                            </div>
                            <div class="col-12 col-lg-8">
                                <form action="{{ route('suggestions.store') }}" method="POST" class="feedback-form">
                                    @csrf
                                    <input type="hidden" name="pathology_id" value="{{ $pathology->id }}">
                                    <div class="row g-3">
                                        @auth
                                        <input type="hidden" name="user_name" value="{{ Auth::user()->name }}">
                                        <input type="hidden" name="user_email" value="{{ Auth::user()->email }}">
                                        <div class="col-12">
                                            <div class="alert alert-light border small mb-0">
                                                {{ __('messages.suggestion_as_user', ['name' => Auth::user()->name]) }}
                                            </div>
                                        </div>
                                        @else
                                        <div class="col-12 col-md-6">
                                            <label for="user_name" class="form-label">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                                            <div class="input-icon-wrap">
                                                <i class="fas fa-user"></i>
                                                <input type="text" class="form-control @error('user_name') is-invalid @enderror" id="user_name" name="user_name" value="{{ old('user_name') }}" required>
                                            </div>
                                            @error('user_name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label for="contact" class="form-label">{{ __('messages.contact_method') }} <span class="text-muted small">({{ __('messages.optional') }})</span></label>
                                            <div class="input-icon-wrap">
                                                <i class="fas fa-phone-alt"></i>
                                                <input type="text" class="form-control @error('contact') is-invalid @enderror @error('user_email') is-invalid @enderror" id="contact" name="contact" value="{{ old('contact') }}" placeholder="{{ __('messages.contact_method_placeholder') }}" autocomplete="tel">
                                            </div>
                                            <small class="text-muted">{{ __('messages.contact_method_hint') }}</small>
                                            @error('contact')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            @error('user_email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        @endauth
                                        <div class="col-12">
                                            <label for="doctor_name" class="form-label">{{ __('messages.doctor_name') }}</label>
                                            <div class="input-icon-wrap">
                                                <i class="fas fa-user-md"></i>
                                                <input type="text" class="form-control @error('doctor_name') is-invalid @enderror" id="doctor_name" name="doctor_name" value="{{ old('doctor_name') }}" placeholder="{{ __('messages.doctor_name_placeholder') }}">
                                            </div>
                                            @error('doctor_name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label for="message" class="form-label">{{ __('messages.suggestion') }} <span class="text-danger">*</span></label>
                                            <textarea class="form-control feedback-textarea @error('message') is-invalid @enderror" id="message" name="message" rows="4" required placeholder="{{ __('messages.suggestion_placeholder') }}">{{ old('message') }}</textarea>
                                            @error('message')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        @guest
                                        <div class="col-12 col-md-6">
                                            <label for="captcha_answer" class="form-label">{{ __('messages.suggestion_captcha', ['question' => \App\Support\SuggestionCaptcha::question()]) }} <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('captcha_answer') is-invalid @enderror" id="captcha_answer" name="captcha_answer" value="{{ old('captcha_answer') }}" inputmode="numeric" required autocomplete="off">
                                            <small class="text-muted">{{ __('messages.suggestion_captcha_hint') }}</small>
                                            @error('captcha_answer')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="hp-field" aria-hidden="true">
                                            <label for="website">Website</label>
                                            <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                                        </div>
                                        @endguest
                                        <div class="col-12 d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                                <i class="fas fa-paper-plane me-2"></i>{{ __('messages.submit') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <a href="{{ route('pathologies.index', $category->slug) }}" class="btn btn-outline-primary px-5">
                <i class="fas fa-arrow-left me-2"></i>{{ __('messages.back') }}
            </a>
        </div>
    </div>
</x-app-layout>
