@auth
@if(Auth::user()->isAdmin())
@php
    $pendingSuggestions = \App\Models\Suggestion::pending()
        ->with(['step.pathology', 'pathology'])
        ->latest()
        ->limit(5)
        ->get();
    $pendingSuggestionsCount = \App\Models\Suggestion::pending()->count();
@endphp
<li class="nav-item dropdown">
    <a class="nav-link position-relative d-flex align-items-center py-1"
       href="#"
       id="suggestionsNotifDropdown"
       role="button"
       data-bs-toggle="dropdown"
       aria-expanded="false"
       title="{{ __('messages.suggestion_notifications') }}">
        <i class="fas fa-bell"></i>
        @if($pendingSuggestionsCount > 0)
        <span class="navbar-notif-badge">{{ $pendingSuggestionsCount > 9 ? '9+' : $pendingSuggestionsCount }}</span>
        @endif
    </a>
    <ul class="dropdown-menu dropdown-menu-end navbar-notif-menu" aria-labelledby="suggestionsNotifDropdown">
        <li class="dropdown-header d-flex justify-content-between align-items-center gap-2">
            <span>{{ __('messages.suggestion_notifications') }}</span>
            @if($pendingSuggestionsCount > 0)
            <span class="badge bg-warning text-dark">{{ $pendingSuggestionsCount }}</span>
            @endif
        </li>
        <li><hr class="dropdown-divider"></li>
        @forelse($pendingSuggestions as $suggestion)
        <li>
            <a class="dropdown-item navbar-notif-item" href="{{ route('admin.suggestions.index') }}">
                <div class="fw-semibold text-truncate">{{ $suggestion->user_name }}</div>
                <div class="small text-muted text-truncate">{{ \Illuminate\Support\Str::limit($suggestion->message, 60) }}</div>
                <div class="small text-muted">
                    {{ $suggestion->created_at?->diffForHumans() }}
                    @if($suggestion->user_email)
                        · {{ $suggestion->user_email }}
                    @endif
                </div>
            </a>
        </li>
        @empty
        <li><span class="dropdown-item-text text-muted small">{{ __('messages.no_pending_suggestions') }}</span></li>
        @endforelse
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item text-center fw-semibold" href="{{ route('admin.suggestions.index') }}">
                {{ __('messages.view_all_suggestions') }}
            </a>
        </li>
    </ul>
</li>
@endif
@endauth
