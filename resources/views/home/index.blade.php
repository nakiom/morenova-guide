<x-app-layout>
    <div class="home-video-container position-relative">
        <video autoplay muted loop playsinline class="home-background-video">
            <source src="{{ asset('videos/Morenova-pro-turnable-3D-homepage-1.mp4') }}" type="video/mp4">
        </video>
        <div class="home-video-overlay"></div>
        <div class="container py-3 py-md-5 position-relative">
            <div class="row mb-4 mb-md-5">
                <div class="col-12 text-center">
                    <h1 class="h2 h1-md display-4 fw-bold text-dark-blue mb-2 mb-md-3">{{ __('messages.welcome') }}</h1>
                    <div class="home-guide-intro mx-auto">
                        <p class="home-guide-eyebrow">{{ __('messages.quick_guide') }}</p>
                        <p class="home-guide-prompt">{{ __('messages.select_category') }}</p>
                    </div>
                </div>
            </div>

            <div class="row g-4 justify-content-center" data-stagger>
                @foreach($categories as $category)
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="card pathology-card h-100 text-center p-4 home-category-card position-relative overflow-hidden {{ $category->background_image ? 'has-bg' : '' }}"
                         @if($category->background_image) style="background-image: url('{{ asset('storage/' . $category->background_image) }}');" @endif>
                        @if($category->background_image)
                            <div class="pathology-card-overlay"></div>
                        @endif
                        <div class="card-body d-flex flex-column justify-content-center align-items-center position-relative">
                            <i class="{{ $category->icon }} pathology-icon"></i>
                            <h3 class="card-title text-dark-blue mb-3">
                                @if($category->slug === 'womens-health')
                                    {{ __('messages.womens_health') }}
                                @else
                                    {{ __('messages.mens_health') }}
                                @endif
                            </h3>
                            <a href="{{ route('pathologies.index', $category->slug) }}" class="btn btn-primary btn-lg">
                                {{ __('messages.pathologies') }} <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
