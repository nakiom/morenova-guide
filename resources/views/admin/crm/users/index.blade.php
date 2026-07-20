<x-app-layout>
    <div class="container py-5">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h1 class="h2 fw-bold text-dark-blue mb-1">{{ __('messages.crm_users') }}</h1>
                <p class="text-muted mb-0">{{ __('messages.crm_users_hint') }}</p>
            </div>
            <a href="{{ route('admin.crm-users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>{{ __('messages.create_crm_user') }}
            </a>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-5">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('messages.search') }}">
            </div>
            <div class="col-md-5">
                <select name="center_id" class="form-select" onchange="this.form.submit()">
                    <option value="">{{ __('messages.all_centers') }}</option>
                    @foreach($centers as $center)
                    <option value="{{ $center->id }}" @selected(request('center_id') == $center->id)>{{ $center->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100">{{ __('messages.search') }}</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th></th>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.email') }}</th>
                        <th>{{ __('messages.center') }}</th>
                        <th>{{ __('messages.role_type') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            @if($user->photoUrl())
                            <img src="{{ $user->photoUrl() }}" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;" alt="">
                            @else
                            <span class="badge bg-light text-dark rounded-circle">{{ strtoupper(substr($user->name,0,1)) }}</span>
                            @endif
                        </td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->center?->name ?: '—' }}</td>
                        <td>{{ $user->parent_user_id ? __('messages.sub_user') : __('messages.primary_user') }}</td>
                        <td>
                            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $user->is_active ? __('messages.active') : __('messages.inactive') }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                                <a href="{{ route('admin.crm-users.password.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-key me-1"></i>{{ __('messages.change_password') }}
                                </a>
                                <a href="{{ route('admin.crm-users.show', $user) }}" class="btn btn-sm btn-primary">
                                    {{ __('messages.view') }}
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted">{{ __('messages.no_crm_users') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $users->links() }}
    </div>
</x-app-layout>
