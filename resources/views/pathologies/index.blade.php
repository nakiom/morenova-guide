<x-app-layout>
    <div class="container py-5">
        <div class="row mb-4 page-header">
            <div class="col-12">
                <a href="{{ route('home') }}" class="btn btn-outline-primary btn-back">
                    <i class="fas fa-arrow-left me-2"></i>{{ __('messages.back') }}
                </a>
                <h1 class="h2 h1-md display-5 fw-bold text-dark-blue">
                    @if($category->slug === 'womens-health')
                        {{ __('messages.womens_health') }}
                    @else
                        {{ __('messages.mens_health') }}
                    @endif
                </h1>
                <p class="lead text-muted">{{ __('messages.pathologies') }}</p>
            </div>
        </div>

        <div class="row g-4" data-stagger>
            @forelse($pathologies as $pathology)
            @php
                $translation = $pathology->translation($locale);
            @endphp
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card pathology-card h-100 text-center p-4 position-relative overflow-hidden {{ $pathology->background_image ? 'has-bg' : '' }}"
                     @if($pathology->background_image) style="background-image: url('{{ asset('storage/' . $pathology->background_image) }}');" @endif>
                    @if($pathology->background_image)
                        <div class="pathology-card-overlay"></div>
                    @endif
                    <div class="card-body d-flex flex-column justify-content-center align-items-center position-relative w-100">
                        <x-pathology-icon :pathology="$pathology" />
                        <h4 class="card-title text-dark-blue mb-3">
                            {{ $translation ? $translation->name : $pathology->slug }}
                        </h4>
                        <a href="{{ route('pathologies.show', [$category->slug, $pathology->slug]) }}" class="btn btn-primary">
                            {{ __('messages.steps') }} <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <p class="mb-0">{{ __('messages.no_pathologies') }}</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
