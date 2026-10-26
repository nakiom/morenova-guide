<x-app-layout>
    <div class="container py-5">
        <a href="{{ route('admin.centers.index') }}" class="btn btn-outline-primary mb-3">
            <i class="fas fa-arrow-left me-1"></i>{{ __('messages.back') }}
        </a>

        <div class="admin-form-shell card border-0 shadow-sm mx-auto">
            <div class="admin-form-shell-header">
                <div>
                    <p class="admin-form-eyebrow mb-1">CRM</p>
                    <h1 class="h3 fw-bold text-dark-blue mb-1">{{ __('messages.create_center') }}</h1>
                    <p class="text-muted mb-0">{{ __('messages.create_center_hint') }}</p>
                </div>
                <span class="admin-form-badge">
                    <i class="fas fa-plus"></i>
                </span>
            </div>
            <form method="POST" action="{{ route('admin.centers.store') }}" enctype="multipart/form-data">
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
                    @include('admin.crm.centers._form', ['center' => null])
                    <div class="d-flex flex-wrap justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('admin.centers.index') }}" class="btn btn-outline-secondary">{{ __('messages.cancel') }}</a>
                        <button class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ __('messages.save') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
