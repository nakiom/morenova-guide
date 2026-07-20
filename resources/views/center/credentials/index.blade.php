<x-app-layout>
    <div class="container py-5">
        <div class="mb-4">
            <h1 class="h2 fw-bold text-dark-blue mb-1">{{ __('messages.credentials_management') }}</h1>
            <p class="text-muted mb-0">{{ __('messages.credentials_management_hint') }}</p>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.email') }}</th>
                            <th>{{ __('messages.role_type') }}</th>
                            <th class="text-end">{{ __('messages.password') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->is($manager) ? __('messages.primary_user') : __('messages.sub_user') }}</td>
                            <td class="text-end">
                                <a href="{{ route('center.credentials.edit', $user) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-key me-1"></i>{{ __('messages.change_password') }}
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
