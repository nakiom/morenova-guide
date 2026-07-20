<x-app-layout>
    <div class="container py-5">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div class="d-flex gap-3 align-items-start">
                @if($user->photoUrl())
                <img src="{{ $user->photoUrl() }}" class="rounded" style="width:80px;height:80px;object-fit:cover;" alt="">
                @endif
                <div>
                    <a href="{{ route('admin.crm-users.index') }}" class="btn btn-outline-primary btn-sm mb-2">
                        <i class="fas fa-arrow-left me-1"></i>{{ __('messages.back') }}
                    </a>
                    <h1 class="h2 fw-bold text-dark-blue mb-1">{{ $user->name }}</h1>
                    <p class="text-muted mb-0">
                        {{ $user->email }} · {{ $user->center?->name }}
                        · {{ $user->parent_user_id ? __('messages.sub_user') : __('messages.primary_user') }}
                    </p>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.crm-users.password.edit', $user) }}" class="btn btn-outline-primary">
                    <i class="fas fa-key me-1"></i>{{ __('messages.change_password') }}
                </a>
                <a href="{{ route('admin.crm-users.edit', $user) }}" class="btn btn-primary">
                    <i class="fas fa-pen me-1"></i>{{ __('messages.edit') }}
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body">
                <div class="text-muted small">{{ __('messages.phone') }}</div>
                <div>{{ $user->phone ?: '—' }}</div>
            </div></div></div>
            <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body">
                <div class="text-muted small">{{ __('messages.location') }}</div>
                <div>{{ $user->location ?: '—' }}</div>
            </div></div></div>
            <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body">
                <div class="text-muted small">{{ __('messages.job_title') }}</div>
                <div>{{ $user->job_title ?: '—' }}</div>
            </div></div></div>
        </div>

        @if($user->parent)
        <div class="alert alert-light border mb-4">
            {{ __('messages.parent_user') }}:
            <a href="{{ route('admin.crm-users.show', $user->parent) }}">{{ $user->parent->name }}</a>
        </div>
        @endif

        @if($user->subUsers->isNotEmpty())
        <h2 class="h5 fw-bold text-dark-blue mb-3">{{ __('messages.sub_users') }}</h2>
        <ul class="list-group mb-4">
            @foreach($user->subUsers as $sub)
            <a href="{{ route('admin.crm-users.show', $sub) }}" class="list-group-item list-group-item-action">{{ $sub->name }} — {{ $sub->email }}</a>
            @endforeach
        </ul>
        @endif

        <h2 class="h5 fw-bold text-dark-blue mb-3">{{ __('messages.equipment') }}</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ __('messages.serial_number') }}</th>
                        <th>{{ __('messages.equipment_model') }}</th>
                        <th>{{ __('messages.location') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($user->equipment as $item)
                    <tr>
                        <td>{{ $item->serial_number }}</td>
                        <td>{{ $item->model }}</td>
                        <td>{{ $item->location ?: '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted">{{ __('messages.no_equipment') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
