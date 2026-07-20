<x-app-layout>
    <div class="container py-5">
        <a href="{{ route('clinical.patients.show', $patient) }}" class="btn btn-outline-primary mb-3">
            <i class="fas fa-arrow-left me-2"></i>{{ __('messages.back') }}
        </a>

        <div class="admin-form-shell card border-0 shadow-sm mx-auto" style="max-width:52rem">
            <div class="admin-form-shell-header">
                <div>
                    <p class="admin-form-eyebrow mb-1">{{ __('messages.questionnaire') }}</p>
                    <h1 class="h3 fw-bold text-dark-blue mb-1">{{ __('messages.complete_questionnaire') }}</h1>
                    <p class="text-muted mb-0">{{ $patient->name }} · {{ $patient->anonymousCode() }}</p>
                </div>
                <span class="admin-form-badge"><i class="fas fa-clipboard-check"></i></span>
            </div>

            @if($questionnaires->isEmpty())
            <div class="card-body p-4">
                <div class="alert alert-warning mb-0">{{ __('messages.no_questionnaires_for_protocol') }}</div>
            </div>
            @else
            <form action="{{ route('clinical.scores.store', $patient) }}" method="POST">
                @csrf
                <div class="card-body p-4 p-md-5">
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    @include('clinical.scores._form')
                    <div class="d-flex flex-wrap justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('clinical.patients.show', $patient) }}" class="btn btn-outline-secondary">{{ __('messages.cancel') }}</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ __('messages.save_assessment') }}
                        </button>
                    </div>
                </div>
            </form>
            @endif
        </div>
    </div>
</x-app-layout>
