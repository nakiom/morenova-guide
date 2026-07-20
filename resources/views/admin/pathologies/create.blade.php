<x-app-layout>
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('admin.pathologies.index') }}" class="btn btn-outline-primary mb-3">
                    <i class="fas fa-arrow-left me-2"></i>{{ __('messages.back') }}
                </a>
                <h1 class="display-5 fw-bold text-dark-blue">{{ __('messages.create') }} {{ __('messages.pathologies') }}</h1>
            </div>
        </div>

        <form action="{{ route('admin.pathologies.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="category_id" class="form-label">Categoría</label>
                    <select name="category_id" id="category_id" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->slug }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" name="slug" id="slug" class="form-control" required>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label for="icon" class="form-label">Icono (Font Awesome)</label>
                    <input type="text" name="icon" id="icon" class="form-control" placeholder="fas fa-heart">
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label for="order" class="form-label">Orden</label>
                    <input type="number" name="order" id="order" class="form-control" value="0">
                </div>
                <div class="col-12 mb-3">
                    <label for="background_image" class="form-label">Imagen de Fondo</label>
                    <input type="file" name="background_image" id="background_image" class="form-control" accept="image/*">
                    <small class="form-text text-muted">Imagen de fondo para la tarjeta de la patología (máx. 2MB)</small>
                </div>
            </div>

            <h3 class="mt-4 mb-3">{{ __('messages.treatment_parameters') }}</h3>
            <p class="text-muted small">{{ __('messages.treatment_parameters_hint') }}</p>
            <div class="row">
                <div class="col-12 col-md-4 col-lg-3 mb-3">
                    <label for="transducer" class="form-label">{{ __('messages.param_transducer') }}</label>
                    <input type="text" name="transducer" id="transducer" class="form-control" value="{{ old('transducer') }}" placeholder="Li50F">
                </div>
                <div class="col-12 col-md-4 col-lg-3 mb-3">
                    <label for="sessions_count" class="form-label">{{ __('messages.param_sessions') }}</label>
                    <input type="text" name="sessions_count" id="sessions_count" class="form-control" value="{{ old('sessions_count') }}" placeholder="6 - 8">
                </div>
                <div class="col-12 col-md-4 col-lg-3 mb-3">
                    <label for="sessions_frequency" class="form-label">{{ __('messages.param_sessions_frequency') }}</label>
                    <input type="text" name="sessions_frequency" id="sessions_frequency" class="form-control" value="{{ old('sessions_frequency') }}" placeholder="2 / semana">
                </div>
                <div class="col-12 col-md-4 col-lg-3 mb-3">
                    <label for="session_duration_minutes" class="form-label">{{ __('messages.param_session_duration') }} ({{ __('messages.unit_minutes') }})</label>
                    <input type="number" name="session_duration_minutes" id="session_duration_minutes" class="form-control" value="{{ old('session_duration_minutes') }}" min="0" max="600">
                </div>
                <div class="col-12 col-md-4 col-lg-3 mb-3">
                    <label for="applications_per_session" class="form-label">{{ __('messages.param_applications_per_session') }}</label>
                    <input type="number" name="applications_per_session" id="applications_per_session" class="form-control" value="{{ old('applications_per_session') }}" min="0" max="100">
                </div>
                <div class="col-12 col-md-4 col-lg-3 mb-3">
                    <label for="shots_per_application" class="form-label">{{ __('messages.param_shots_per_application') }}</label>
                    <input type="number" name="shots_per_application" id="shots_per_application" class="form-control" value="{{ old('shots_per_application') }}" min="0" max="10000">
                </div>
                <div class="col-12 col-md-4 col-lg-3 mb-3">
                    <label for="shots_per_session" class="form-label">{{ __('messages.param_shots_per_session') }}</label>
                    <input type="number" name="shots_per_session" id="shots_per_session" class="form-control" value="{{ old('shots_per_session') }}" min="0" max="100000">
                </div>
                <div class="col-12 col-md-4 col-lg-3 mb-3">
                    <label for="energy_kv" class="form-label">{{ __('messages.param_energy') }} (kV)</label>
                    <input type="number" step="0.01" name="energy_kv" id="energy_kv" class="form-control" value="{{ old('energy_kv') }}" min="0" max="99.99">
                </div>
                <div class="col-12 col-md-4 col-lg-3 mb-3">
                    <label for="frequency_hz" class="form-label">{{ __('messages.param_frequency') }} (Hz)</label>
                    <input type="number" step="0.01" name="frequency_hz" id="frequency_hz" class="form-control" value="{{ old('frequency_hz') }}" min="0" max="999.99">
                </div>
            </div>

            <h3 class="mt-4 mb-3">Traducciones</h3>
            
            <div class="row mb-3">
                <div class="col-12 col-12 col-md-6 col-lg-3">
                    <h5>Español</h5>
                    <input type="hidden" name="translations[0][locale]" value="es">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="translations[0][name]" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="translations[0][description]" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="col-12 col-12 col-md-6 col-lg-3">
                    <h5>English</h5>
                    <input type="hidden" name="translations[1][locale]" value="en">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="translations[1][name]" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="translations[1][description]" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="col-12 col-12 col-md-6 col-lg-3">
                    <h5>Português</h5>
                    <input type="hidden" name="translations[2][locale]" value="pt">
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="translations[2][name]" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="translations[2][description]" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="col-12 col-12 col-md-6 col-lg-3">
                    <h5>中文</h5>
                    <input type="hidden" name="translations[3][locale]" value="zh">
                    <div class="mb-3">
                        <label class="form-label">名称</label>
                        <input type="text" name="translations[3][name]" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">描述</label>
                        <textarea name="translations[3][description]" class="form-control" rows="3"></textarea>
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
    </div>
</x-app-layout>
