<x-app-layout>
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h2 h1-md display-5 fw-bold text-dark-blue">{{ __('messages.suggestions') }}</h1>
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
                        <th>Origen</th>
                        <th>Nombre</th>
                        <th>{{ __('messages.contact_method') }}</th>
                        <th>{{ __('messages.doctor_name') }}</th>
                        <th>Mensaje</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suggestions as $suggestion)
                    <tr>
                        <td>{{ $suggestion->id }}</td>
                        <td>
                            @if($suggestion->step)
                                Paso #{{ $suggestion->step->order }} - {{ $suggestion->step->pathology->slug ?? 'N/A' }}
                            @elseif($suggestion->pathology)
                                {{ __('messages.send_feedback') }} - {{ $suggestion->pathology->slug }}
                            @else
                                General
                            @endif
                        </td>
                        <td>{{ $suggestion->user_name }}</td>
                        <td>{{ $suggestion->user_email ?: '—' }}</td>
                        <td>{{ $suggestion->doctor_name ?? '-' }}</td>
                        <td>{{ Str::limit($suggestion->message, 50) }}</td>
                        <td>
                            <span class="badge bg-{{ $suggestion->status === 'approved' ? 'success' : ($suggestion->status === 'rejected' ? 'danger' : 'warning') }}">
                                {{ __('messages.' . $suggestion->status) }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('admin.suggestions.update', $suggestion) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.suggestions.update', $suggestion) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No hay sugerencias</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
