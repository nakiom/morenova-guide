<x-app-layout>
    <div class="container py-5">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('admin.pathologies.index') }}" class="btn btn-outline-primary mb-3">
                    <i class="fas fa-arrow-left me-2"></i>{{ __('messages.back') }}
                </a>
                <h1 class="display-5 fw-bold text-dark-blue">{{ __('messages.edit') }} {{ __('messages.pathologies') }}</h1>
            </div>
        </div>

        <form action="{{ route('admin.pathologies.update', $pathology) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="category_id" class="form-label">Categoría</label>
                    <select name="category_id" id="category_id" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $pathology->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->slug }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" name="slug" id="slug" class="form-control" value="{{ $pathology->slug }}" required>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label for="icon" class="form-label">Icono (Font Awesome)</label>
                    <input type="text" name="icon" id="icon" class="form-control" value="{{ $pathology->icon }}" placeholder="fas fa-heart">
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label for="order" class="form-label">Orden</label>
                    <input type="number" name="order" id="order" class="form-control" value="{{ $pathology->order }}">
                </div>
                <div class="col-12 mb-3">
                    <label for="background_image" class="form-label">Imagen de Fondo</label>
                    @if($pathology->background_image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $pathology->background_image) }}" alt="Imagen actual" style="max-width: 300px; max-height: 200px; object-fit: cover; border-radius: 0.5rem;" class="img-thumbnail">
                        </div>
                    @endif
                    <input type="file" name="background_image" id="background_image" class="form-control" accept="image/*">
                    <small class="form-text text-muted">Imagen de fondo para la tarjeta de la patología (máx. 2MB). Dejar vacío para mantener la imagen actual.</small>
                </div>
            </div>

            <h3 class="mt-4 mb-3">{{ __('messages.treatment_parameters') }}</h3>
            <p class="text-muted small">{{ __('messages.treatment_parameters_hint') }}</p>
            <div class="row">
                <div class="col-12 col-md-4 col-lg-3 mb-3">
                    <label for="transducer" class="form-label">{{ __('messages.param_transducer') }}</label>
                    <input type="text" name="transducer" id="transducer" class="form-control" value="{{ old('transducer', $pathology->transducer) }}" placeholder="Li50F">
                </div>
                <div class="col-12 col-md-4 col-lg-3 mb-3">
                    <label for="sessions_count" class="form-label">{{ __('messages.param_sessions') }}</label>
                    <input type="text" name="sessions_count" id="sessions_count" class="form-control" value="{{ old('sessions_count', $pathology->sessions_count) }}" placeholder="6 - 8">
                </div>
                <div class="col-12 col-md-4 col-lg-3 mb-3">
                    <label for="sessions_frequency" class="form-label">{{ __('messages.param_sessions_frequency') }}</label>
                    <input type="text" name="sessions_frequency" id="sessions_frequency" class="form-control" value="{{ old('sessions_frequency', $pathology->sessions_frequency) }}" placeholder="2 / semana">
                </div>
                <div class="col-12 col-md-4 col-lg-3 mb-3">
                    <label for="session_duration_minutes" class="form-label">{{ __('messages.param_session_duration') }} ({{ __('messages.unit_minutes') }})</label>
                    <input type="number" name="session_duration_minutes" id="session_duration_minutes" class="form-control" value="{{ old('session_duration_minutes', $pathology->session_duration_minutes) }}" min="0" max="600">
                </div>
                <div class="col-12 col-md-4 col-lg-3 mb-3">
                    <label for="applications_per_session" class="form-label">{{ __('messages.param_applications_per_session') }}</label>
                    <input type="number" name="applications_per_session" id="applications_per_session" class="form-control" value="{{ old('applications_per_session', $pathology->applications_per_session) }}" min="0" max="100">
                </div>
                <div class="col-12 col-md-4 col-lg-3 mb-3">
                    <label for="shots_per_application" class="form-label">{{ __('messages.param_shots_per_application') }}</label>
                    <input type="number" name="shots_per_application" id="shots_per_application" class="form-control" value="{{ old('shots_per_application', $pathology->shots_per_application) }}" min="0" max="10000">
                </div>
                <div class="col-12 col-md-4 col-lg-3 mb-3">
                    <label for="shots_per_session" class="form-label">{{ __('messages.param_shots_per_session') }}</label>
                    <input type="number" name="shots_per_session" id="shots_per_session" class="form-control" value="{{ old('shots_per_session', $pathology->shots_per_session) }}" min="0" max="100000">
                </div>
                <div class="col-12 col-md-4 col-lg-3 mb-3">
                    <label for="energy_kv" class="form-label">{{ __('messages.param_energy') }} (kV)</label>
                    <input type="number" step="0.01" name="energy_kv" id="energy_kv" class="form-control" value="{{ old('energy_kv', $pathology->energy_kv) }}" min="0" max="99.99">
                </div>
                <div class="col-12 col-md-4 col-lg-3 mb-3">
                    <label for="frequency_hz" class="form-label">{{ __('messages.param_frequency') }} (Hz)</label>
                    <input type="number" step="0.01" name="frequency_hz" id="frequency_hz" class="form-control" value="{{ old('frequency_hz', $pathology->frequency_hz) }}" min="0" max="999.99">
                </div>
            </div>

            <h3 class="mt-4 mb-3">Traducciones</h3>
            
            @php
                $esTranslation = $pathology->translations->where('locale', 'es')->first();
                $enTranslation = $pathology->translations->where('locale', 'en')->first();
                $ptTranslation = $pathology->translations->where('locale', 'pt')->first();
                $zhTranslation = $pathology->translations->where('locale', 'zh')->first();
            @endphp

            <div class="row mb-3">
                <div class="col-12 col-12 col-md-6 col-lg-3">
                    <h5>Español</h5>
                    <input type="hidden" name="translations[0][locale]" value="es">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="translations[0][name]" class="form-control" value="{{ $esTranslation?->name }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="translations[0][description]" class="form-control" rows="3">{{ $esTranslation?->description }}</textarea>
                    </div>
                </div>
                <div class="col-12 col-12 col-md-6 col-lg-3">
                    <h5>English</h5>
                    <input type="hidden" name="translations[1][locale]" value="en">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="translations[1][name]" class="form-control" value="{{ $enTranslation?->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="translations[1][description]" class="form-control" rows="3">{{ $enTranslation?->description }}</textarea>
                    </div>
                </div>
                <div class="col-12 col-12 col-md-6 col-lg-3">
                    <h5>Português</h5>
                    <input type="hidden" name="translations[2][locale]" value="pt">
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="translations[2][name]" class="form-control" value="{{ $ptTranslation?->name }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="translations[2][description]" class="form-control" rows="3">{{ $ptTranslation?->description }}</textarea>
                    </div>
                </div>
                <div class="col-12 col-12 col-md-6 col-lg-3">
                    <h5>中文</h5>
                    <input type="hidden" name="translations[3][locale]" value="zh">
                    <div class="mb-3">
                        <label class="form-label">名称</label>
                        <input type="text" name="translations[3][name]" class="form-control" value="{{ $zhTranslation?->name }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">描述</label>
                        <textarea name="translations[3][description]" class="form-control" rows="3">{{ $zhTranslation?->description }}</textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                    <a href="{{ route('admin.pathologies.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
                </div>
            </div>
        </form>

        <!-- Sección de Criterios de Inclusión -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>{{ __('messages.inclusion_criteria') }}
                        </h5>
                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#createInclusionModal">
                            <i class="fas fa-plus me-1"></i>{{ __('messages.add_inclusion_criteria') }}
                        </button>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            @forelse($pathology->inclusionCriteria->sortBy('order') as $criterion)
                            @php
                                $esCriterion = $criterion->translations->where('locale', 'es')->first();
                                $enCriterion = $criterion->translations->where('locale', 'en')->first();
                                $ptCriterion = $criterion->translations->where('locale', 'pt')->first();
                                $zhCriterion = $criterion->translations->where('locale', 'zh')->first();
                            @endphp
                            <li class="admin-criteria-item inclusion mb-2 p-3 rounded">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <strong>ES:</strong> {{ $esCriterion?->text ?? 'Sin texto' }}<br>
                                        <strong>EN:</strong> {{ $enCriterion?->text ?? 'Sin texto' }}<br>
                                        <strong>PT:</strong> {{ $ptCriterion?->text ?? 'Sem texto' }}<br>
                                        <strong>ZH:</strong> {{ $zhCriterion?->text ?? '无文本' }}
                                        @if($criterion->order)
                                        <small class="text-muted d-block mt-1">Orden: {{ $criterion->order }}</small>
                                        @endif
                                    </div>
                                    <div class="ms-3">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCriteriaModal{{ $criterion->id }}" data-type="inclusion">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.criteria.destroy', $criterion->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este criterio?')">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="type" value="inclusion">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                            @empty
                            <li class="text-muted">{{ __('messages.no_criteria') }}</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Criterios de Exclusión -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-times-circle me-2"></i>{{ __('messages.exclusion_criteria') }}
                        </h5>
                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#createExclusionModal">
                            <i class="fas fa-plus me-1"></i>{{ __('messages.add_exclusion_criteria') }}
                        </button>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            @forelse($pathology->exclusionCriteria->sortBy('order') as $criterion)
                            @php
                                $esCriterion = $criterion->translations->where('locale', 'es')->first();
                                $enCriterion = $criterion->translations->where('locale', 'en')->first();
                                $ptCriterion = $criterion->translations->where('locale', 'pt')->first();
                                $zhCriterion = $criterion->translations->where('locale', 'zh')->first();
                            @endphp
                            <li class="admin-criteria-item exclusion mb-2 p-3 rounded">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <strong>ES:</strong> {{ $esCriterion?->text ?? 'Sin texto' }}<br>
                                        <strong>EN:</strong> {{ $enCriterion?->text ?? 'Sin texto' }}<br>
                                        <strong>PT:</strong> {{ $ptCriterion?->text ?? 'Sem texto' }}<br>
                                        <strong>ZH:</strong> {{ $zhCriterion?->text ?? '无文本' }}
                                        @if($criterion->order)
                                        <small class="text-muted d-block mt-1">Orden: {{ $criterion->order }}</small>
                                        @endif
                                    </div>
                                    <div class="ms-3">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCriteriaModal{{ $criterion->id }}" data-type="exclusion">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.criteria.destroy', $criterion->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este criterio?')">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="type" value="exclusion">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                            @empty
                            <li class="text-muted">{{ __('messages.no_criteria') }}</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para crear criterio de inclusión -->
        <div class="modal fade" id="createInclusionModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">{{ __('messages.add_inclusion_criteria') }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.criteria.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="pathology_id" value="{{ $pathology->id }}">
                            <input type="hidden" name="type" value="inclusion">
                            <div class="mb-3">
                                <label for="inclusion_order" class="form-label">Orden (opcional)</label>
                                <input type="number" name="order" id="inclusion_order" class="form-control" min="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.criteria_text_es') }}</label>
                                <textarea name="translations[0][text]" class="form-control" rows="3"></textarea>
                                <input type="hidden" name="translations[0][locale]" value="es">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.criteria_text_en') }}</label>
                                <textarea name="translations[1][text]" class="form-control" rows="3"></textarea>
                                <input type="hidden" name="translations[1][locale]" value="en">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Texto do Critério (Português)</label>
                                <textarea name="translations[2][text]" class="form-control" rows="3"></textarea>
                                <input type="hidden" name="translations[2][locale]" value="pt">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">标准文本（中文）</label>
                                <textarea name="translations[3][text]" class="form-control" rows="3"></textarea>
                                <input type="hidden" name="translations[3][locale]" value="zh">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                            <button type="submit" class="btn btn-success">{{ __('messages.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal para crear criterio de exclusión -->
        <div class="modal fade" id="createExclusionModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">{{ __('messages.add_exclusion_criteria') }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.criteria.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="pathology_id" value="{{ $pathology->id }}">
                            <input type="hidden" name="type" value="exclusion">
                            <div class="mb-3">
                                <label for="exclusion_order" class="form-label">Orden (opcional)</label>
                                <input type="number" name="order" id="exclusion_order" class="form-control" min="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.criteria_text_es') }}</label>
                                <textarea name="translations[0][text]" class="form-control" rows="3"></textarea>
                                <input type="hidden" name="translations[0][locale]" value="es">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.criteria_text_en') }}</label>
                                <textarea name="translations[1][text]" class="form-control" rows="3"></textarea>
                                <input type="hidden" name="translations[1][locale]" value="en">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Texto do Critério (Português)</label>
                                <textarea name="translations[2][text]" class="form-control" rows="3"></textarea>
                                <input type="hidden" name="translations[2][locale]" value="pt">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">标准文本（中文）</label>
                                <textarea name="translations[3][text]" class="form-control" rows="3"></textarea>
                                <input type="hidden" name="translations[3][locale]" value="zh">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                            <button type="submit" class="btn btn-danger">{{ __('messages.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modales para editar criterios -->
        @foreach($pathology->inclusionCriteria as $criterion)
        @php
            $esCriterion = $criterion->translations->where('locale', 'es')->first();
            $enCriterion = $criterion->translations->where('locale', 'en')->first();
            $ptCriterion = $criterion->translations->where('locale', 'pt')->first();
            $zhCriterion = $criterion->translations->where('locale', 'zh')->first();
        @endphp
        <div class="modal fade" id="editCriteriaModal{{ $criterion->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">{{ __('messages.edit_criteria') }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.criteria.update', $criterion->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body">
                            <input type="hidden" name="pathology_id" value="{{ $pathology->id }}">
                            <input type="hidden" name="type" value="inclusion">
                            <div class="mb-3">
                                <label for="edit_inclusion_order{{ $criterion->id }}" class="form-label">Orden (opcional)</label>
                                <input type="number" name="order" id="edit_inclusion_order{{ $criterion->id }}" class="form-control" value="{{ $criterion->order }}" min="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.criteria_text_es') }}</label>
                                <textarea name="translations[0][text]" class="form-control" rows="3">{{ $esCriterion?->text }}</textarea>
                                <input type="hidden" name="translations[0][locale]" value="es">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.criteria_text_en') }}</label>
                                <textarea name="translations[1][text]" class="form-control" rows="3" required>{{ $enCriterion?->text }}</textarea>
                                <input type="hidden" name="translations[1][locale]" value="en">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Texto do Critério (Português)</label>
                                <textarea name="translations[2][text]" class="form-control" rows="3">{{ $ptCriterion?->text }}</textarea>
                                <input type="hidden" name="translations[2][locale]" value="pt">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">标准文本（中文）</label>
                                <textarea name="translations[3][text]" class="form-control" rows="3">{{ $zhCriterion?->text }}</textarea>
                                <input type="hidden" name="translations[3][locale]" value="zh">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                            <button type="submit" class="btn btn-success">{{ __('messages.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach

        @foreach($pathology->exclusionCriteria as $criterion)
        @php
            $esCriterion = $criterion->translations->where('locale', 'es')->first();
            $enCriterion = $criterion->translations->where('locale', 'en')->first();
            $ptCriterion = $criterion->translations->where('locale', 'pt')->first();
            $zhCriterion = $criterion->translations->where('locale', 'zh')->first();
        @endphp
        <div class="modal fade" id="editCriteriaModal{{ $criterion->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">{{ __('messages.edit_criteria') }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.criteria.update', $criterion->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body">
                            <input type="hidden" name="pathology_id" value="{{ $pathology->id }}">
                            <input type="hidden" name="type" value="exclusion">
                            <div class="mb-3">
                                <label for="edit_exclusion_order{{ $criterion->id }}" class="form-label">Orden (opcional)</label>
                                <input type="number" name="order" id="edit_exclusion_order{{ $criterion->id }}" class="form-control" value="{{ $criterion->order }}" min="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.criteria_text_es') }}</label>
                                <textarea name="translations[0][text]" class="form-control" rows="3">{{ $esCriterion?->text }}</textarea>
                                <input type="hidden" name="translations[0][locale]" value="es">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.criteria_text_en') }}</label>
                                <textarea name="translations[1][text]" class="form-control" rows="3" required>{{ $enCriterion?->text }}</textarea>
                                <input type="hidden" name="translations[1][locale]" value="en">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Texto do Critério (Português)</label>
                                <textarea name="translations[2][text]" class="form-control" rows="3">{{ $ptCriterion?->text }}</textarea>
                                <input type="hidden" name="translations[2][locale]" value="pt">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">标准文本（中文）</label>
                                <textarea name="translations[3][text]" class="form-control" rows="3">{{ $zhCriterion?->text }}</textarea>
                                <input type="hidden" name="translations[3][locale]" value="zh">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                            <button type="submit" class="btn btn-danger">{{ __('messages.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</x-app-layout>
