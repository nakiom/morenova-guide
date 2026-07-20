<x-app-layout>
    <div class="container py-5">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h1 class="h2 fw-bold text-dark-blue mb-1">{{ __('messages.equipment') }}</h1>
                <p class="text-muted mb-0">{{ __('messages.equipment_hint') }}</p>
            </div>
            <a href="{{ route('admin.equipment.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>{{ __('messages.create_equipment') }}
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
                        <th>{{ __('messages.serial_number') }}</th>
                        <th>{{ __('messages.equipment_model') }}</th>
                        <th>{{ __('messages.center') }}</th>
                        <th>{{ __('messages.assigned_user') }}</th>
                        <th>{{ __('messages.location') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($equipment as $item)
                    <tr>
                        <td>{{ $item->serial_number }}</td>
                        <td>{{ $item->model }}</td>
                        <td>{{ $item->center?->name }}</td>
                        <td>{{ $item->user?->name ?: '—' }}</td>
                        <td>{{ $item->location ?: '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.equipment.edit', $item) }}" class="btn btn-sm btn-outline-primary">{{ __('messages.edit') }}</a>
                            <form action="{{ route('admin.equipment.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted">{{ __('messages.no_equipment') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $equipment->links() }}
    </div>
</x-app-layout>
