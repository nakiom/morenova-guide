@php
    $locales = [
        ['code' => 'es', 'label' => 'Español'],
        ['code' => 'en', 'label' => 'English'],
        ['code' => 'pt', 'label' => 'Português'],
        ['code' => 'zh', 'label' => '中文'],
    ];
@endphp

<x-app-layout>
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('admin.session-checklist.index', $pathologyId ? ['pathology_id' => $pathologyId] : []) }}" class="btn btn-outline-primary mb-3">
                    <i class="fas fa-arrow-left me-2"></i>{{ __('messages.back') }}
                </a>
                <h1 class="display-5 fw-bold text-dark-blue">{{ __('messages.create') }} — {{ __('messages.session_checklist') }}</h1>
            </div>
        </div>

        <form action="{{ route('admin.session-checklist.store') }}" method="POST">
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
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label for="order" class="form-label">Orden</label>
                    <input type="number" name="order" id="order" class="form-control" value="1" min="1">
                </div>
            </div>

            <h3 class="mt-4 mb-3">Traducciones</h3>
            <div class="row">
                @foreach($locales as $index => $locale)
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <h5>{{ $locale['label'] }}</h5>
                    <input type="hidden" name="translations[{{ $index }}][locale]" value="{{ $locale['code'] }}">
                    <textarea name="translations[{{ $index }}][text]" class="form-control" rows="4" required></textarea>
                </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary mt-3">{{ __('messages.save') }}</button>
        </form>
    </div>
</x-app-layout>
