<x-app-layout>
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <h1 class="h2 h1-md display-5 fw-bold text-dark-blue mb-3 mb-md-0">{{ __('messages.pathologies') }}</h1>
                <a href="{{ route('admin.pathologies.create') }}" class="btn btn-primary">
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

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>{{ __('messages.categories') }}</th>
                        <th>Slug</th>
                        <th>Icono</th>
                        <th>Orden</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pathologies as $pathology)
                    <tr>
                        <td>{{ $pathology->id }}</td>
                        <td>{{ $pathology->category->slug }}</td>
                        <td>{{ $pathology->slug }}</td>
                        <td>
                            <x-pathology-icon :pathology="$pathology" class="pathology-icon-img pathology-icon-img--table" size="2rem" />
                        </td>
                        <td>{{ $pathology->order }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.pathologies.edit', $pathology) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.pathologies.destroy', $pathology) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Estás seguro?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">{{ __('messages.no_pathologies') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
