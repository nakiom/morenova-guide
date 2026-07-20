<x-app-layout>
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <h1 class="h2 h1-md display-5 fw-bold text-dark-blue mb-3 mb-md-0">{{ __('messages.application_areas_management') }}</h1>
                <a href="{{ route('admin.application-areas.create', $pathologyId ? ['pathology_id' => $pathologyId] : []) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>{{ __('messages.create') }}
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row mb-4">
            <div class="col-12 col-md-4">
                <form method="GET" action="{{ route('admin.application-areas.index') }}">
                    <select name="pathology_id" class="form-select" onchange="this.form.submit()">
                        <option value="">{{ __('messages.all_pathologies') }}</option>
                        @foreach($pathologies as $pathology)
                        <option value="{{ $pathology->id }}" {{ ($pathologyId && $pathologyId == $pathology->id) ? 'selected' : '' }}>
                            {{ $pathology->slug }}
                        </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patología</th>
                        <th>Orden</th>
                        <th>Título (ES)</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    @php
                        $itemTranslation = $item->translation('es') ?? $item->translation('en');
                    @endphp
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->pathology?->slug }}</td>
                        <td>{{ $item->order }}</td>
                        <td>{{ $itemTranslation?->title ?? '—' }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.application-areas.edit', $item) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.application-areas.destroy', $item) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('messages.confirm_delete') }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">{{ __('messages.no_application_areas') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
