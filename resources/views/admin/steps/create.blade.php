<x-app-layout>
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('admin.steps.index', $pathologyId ? ['pathology_id' => $pathologyId] : []) }}" class="btn btn-outline-primary mb-3">
                    <i class="fas fa-arrow-left me-2"></i>{{ __('messages.back') }}
                </a>
                <h1 class="display-5 fw-bold text-dark-blue">{{ __('messages.create') }} {{ __('messages.step') }}</h1>
            </div>
        </div>

        <form action="{{ route('admin.steps.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="pathology_id" class="form-label">Patología</label>
                    <select name="pathology_id" id="pathology_id" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        @foreach($pathologies as $pathology)
                        <option value="{{ $pathology->id }}" {{ ($pathologyId && $pathologyId == $pathology->id) ? 'selected' : '' }}>
                            {{ $pathology->slug }}
                        </option>
                        @endforeach
                    </select>
                    @error('pathology_id')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label for="order" class="form-label">Orden</label>
                    <input type="number" name="order" id="order" class="form-control" value="1" min="1">
                    @error('order')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label for="image" class="form-label">Imagen</label>
                    <input type="file" name="image" id="image" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                    <small class="form-text text-muted">Formatos permitidos: JPEG, PNG, JPG, WEBP. Tamaño máximo: 2MB</small>
                    @error('image')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <h3 class="mt-4 mb-3">Traducciones</h3>
            
            <div class="row mb-3">
                <div class="col-12 col-12 col-md-6 col-lg-3">
                    <h5>Español</h5>
                    <input type="hidden" name="translations[0][locale]" value="es">
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="translations[0][title]" class="form-control">
                        @error('translations.0.title')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="translations[0][description]" class="form-control" rows="5"></textarea>
                        @error('translations.0.description')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-12 col-12 col-md-6 col-lg-3">
                    <h5>English</h5>
                    <input type="hidden" name="translations[1][locale]" value="en">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="translations[1][title]" class="form-control">
                        @error('translations.1.title')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="translations[1][description]" class="form-control" rows="5"></textarea>
                        @error('translations.1.description')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-12 col-12 col-md-6 col-lg-3">
                    <h5>Português</h5>
                    <input type="hidden" name="translations[2][locale]" value="pt">
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="translations[2][title]" class="form-control">
                        @error('translations.2.title')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="translations[2][description]" class="form-control" rows="5"></textarea>
                        @error('translations.2.description')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-12 col-12 col-md-6 col-lg-3">
                    <h5>中文</h5>
                    <input type="hidden" name="translations[3][locale]" value="zh">
                    <div class="mb-3">
                        <label class="form-label">标题</label>
                        <input type="text" name="translations[3][title]" class="form-control">
                        @error('translations.3.title')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">描述</label>
                        <textarea name="translations[3][description]" class="form-control" rows="5"></textarea>
                        @error('translations.3.description')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                    <a href="{{ route('admin.steps.index', $pathologyId ? ['pathology_id' => $pathologyId] : []) }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
