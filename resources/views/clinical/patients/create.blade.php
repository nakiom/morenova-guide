<x-app-layout>
    <div class="container py-5">
        <a href="{{ route('clinical.patients.index') }}" class="btn btn-outline-primary mb-3">
            <i class="fas fa-arrow-left me-2"></i>{{ __('messages.back') }}
        </a>
        <h1 class="h2 fw-bold text-dark-blue mb-4">{{ __('messages.create_patient') }}</h1>

        <form action="{{ route('clinical.patients.store') }}" method="POST" class="card border-0 shadow-sm">
            @csrf
            <div class="card-body">
                @include('clinical.patients._form', ['patient' => null])
                <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
