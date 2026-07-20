<x-app-layout>
    <div class="container py-5">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h1 class="h2 fw-bold text-dark-blue mb-1">{{ __('messages.crm_centers') }}</h1>
                <p class="text-muted mb-0">{{ __('messages.crm_centers_hint') }}</p>
            </div>
            <a href="{{ route('admin.centers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>{{ __('messages.create_center') }}
            </a>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-8">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('messages.search') }}">
            </div>
            <div class="col-md-4">
                <button class="btn btn-outline-secondary w-100">{{ __('messages.search') }}</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.location') }}</th>
                        <th>{{ __('messages.contact') }}</th>
                        <th>{{ __('messages.users') }}</th>
                        <th>{{ __('messages.equipment') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($centers as $center)
                    <tr>
                        <td>{{ $center->name }}</td>
                        <td>{{ $center->location ?: '—' }}</td>
                        <td>{{ $center->contact_name ?: ($center->phone ?: '—') }}</td>
                        <td>{{ $center->users_count }}</td>
                        <td>{{ $center->equipment_count }}</td>
                        <td>
                            <span class="badge {{ $center->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $center->is_active ? __('messages.active') : __('messages.inactive') }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.centers.show', $center) }}" class="btn btn-sm btn-primary">{{ __('messages.view') }}</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted">{{ __('messages.no_centers') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $centers->links() }}
    </div>
</x-app-layout>
