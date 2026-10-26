<x-app-layout>
    <div class="container py-5">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm mb-2">
                    <i class="fas fa-arrow-left me-1"></i>{{ __('messages.back') }}
                </a>
                <h1 class="h2 fw-bold text-dark-blue mb-1">{{ __('messages.mail_settings_title') }}</h1>
                <p class="text-muted mb-0">{{ __('messages.mail_settings_hint') }}</p>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-warning alert-dismissible fade show">{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-2">
                <div class="card border-0 shadow-sm h-100"><div class="card-body">
                    <div class="text-muted small">{{ __('messages.mail_stat_sent') }}</div>
                    <div class="h3 mb-0 text-primary">{{ $stats['sent'] }}</div>
                </div></div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card border-0 shadow-sm h-100"><div class="card-body">
                    <div class="text-muted small">{{ __('messages.mail_stat_failed') }}</div>
                    <div class="h3 mb-0 text-danger">{{ $stats['failed'] }}</div>
                </div></div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card border-0 shadow-sm h-100"><div class="card-body">
                    <div class="text-muted small">{{ __('messages.mail_stat_invites') }}</div>
                    <div class="h3 mb-0 text-primary">{{ $stats['invites'] }}</div>
                </div></div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card border-0 shadow-sm h-100"><div class="card-body">
                    <div class="text-muted small">{{ __('messages.mail_stat_visited') }}</div>
                    <div class="h3 mb-0 text-success">{{ $stats['visited'] }}</div>
                </div></div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card border-0 shadow-sm h-100"><div class="card-body">
                    <div class="text-muted small">{{ __('messages.mail_stat_completed') }}</div>
                    <div class="h3 mb-0 text-success">{{ $stats['completed'] }}</div>
                </div></div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card border-0 shadow-sm h-100"><div class="card-body">
                    <div class="text-muted small">{{ __('messages.mail_stat_pending') }}</div>
                    <div class="h3 mb-0 text-warning">{{ $stats['pending'] }}</div>
                </div></div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card border-0 shadow-sm h-100"><div class="card-body">
                    <div class="text-muted small">{{ __('messages.mail_stat_reminders') }}</div>
                    <div class="h3 mb-0 text-primary">{{ $stats['reminders'] }}</div>
                    @if($stats['reminders_failed'] > 0)
                        <div class="small text-danger">{{ $stats['reminders_failed'] }} {{ __('messages.mail_stat_failed') }}</div>
                    @endif
                </div></div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-5">
                <form method="POST" action="{{ route('admin.mail.update') }}" class="card border-0 shadow-sm mb-4">
                    @csrf
                    @method('PUT')
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold text-dark-blue mb-3">{{ __('messages.mail_settings_config') }}</h2>

                        <div class="mb-3">
                            <label class="form-label" for="mailer">{{ __('messages.mail_mailer') }}</label>
                            <select name="mailer" id="mailer" class="form-select" required>
                                @foreach($mailers as $mailer)
                                <option value="{{ $mailer }}" @selected(old('mailer', $settings['mailer']) === $mailer)>{{ strtoupper($mailer) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="from_address">{{ __('messages.mail_from_address') }}</label>
                                <input type="email" name="from_address" id="from_address" class="form-control"
                                       value="{{ old('from_address', $settings['from_address']) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="from_name">{{ __('messages.mail_from_name') }}</label>
                                <input type="text" name="from_name" id="from_name" class="form-control"
                                       value="{{ old('from_name', $settings['from_name']) }}" required>
                            </div>
                        </div>

                        <div class="border rounded-3 p-3 mb-3 bg-light">
                            <h3 class="h6 fw-semibold mb-3">SMTP</h3>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label" for="smtp_host">Host</label>
                                    <input type="text" name="smtp_host" id="smtp_host" class="form-control"
                                           value="{{ old('smtp_host', $settings['smtp_host']) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="smtp_port">Port</label>
                                    <input type="text" name="smtp_port" id="smtp_port" class="form-control"
                                           value="{{ old('smtp_port', $settings['smtp_port']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="smtp_username">Username</label>
                                    <input type="text" name="smtp_username" id="smtp_username" class="form-control"
                                           value="{{ old('smtp_username', $settings['smtp_username']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="smtp_password">Password</label>
                                    <input type="password" name="smtp_password" id="smtp_password" class="form-control"
                                           placeholder="{{ $settings['smtp_password'] ? '••••••••' : '' }}" autocomplete="new-password">
                                    <small class="text-muted">{{ __('messages.mail_secret_keep_hint') }}</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="smtp_encryption">Encryption</label>
                                    <select name="smtp_encryption" id="smtp_encryption" class="form-select">
                                        <option value="" @selected(old('smtp_encryption', $settings['smtp_encryption']) === '')>—</option>
                                        <option value="tls" @selected(old('smtp_encryption', $settings['smtp_encryption']) === 'tls')>TLS</option>
                                        <option value="ssl" @selected(old('smtp_encryption', $settings['smtp_encryption']) === 'ssl')>SSL</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="border rounded-3 p-3 mb-3 bg-light">
                            <h3 class="h6 fw-semibold mb-3">Mailgun</h3>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label" for="mailgun_domain">Domain</label>
                                    <input type="text" name="mailgun_domain" id="mailgun_domain" class="form-control"
                                           value="{{ old('mailgun_domain', $settings['mailgun_domain']) }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="mailgun_secret">Secret</label>
                                    <input type="password" name="mailgun_secret" id="mailgun_secret" class="form-control"
                                           placeholder="{{ $settings['mailgun_secret'] ? '••••••••' : '' }}" autocomplete="new-password">
                                    <small class="text-muted">{{ __('messages.mail_secret_keep_hint') }}</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="mailgun_endpoint">Endpoint</label>
                                    <input type="text" name="mailgun_endpoint" id="mailgun_endpoint" class="form-control"
                                           value="{{ old('mailgun_endpoint', $settings['mailgun_endpoint']) }}">
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i>{{ __('messages.save') }}
                        </button>
                    </div>
                </form>

                <form method="POST" action="{{ route('admin.mail.test') }}" class="card border-0 shadow-sm">
                    @csrf
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold text-dark-blue mb-3">{{ __('messages.mail_test_title') }}</h2>
                        <div class="mb-3">
                            <label class="form-label" for="test_email">Email</label>
                            <input type="email" name="test_email" id="test_email" class="form-control"
                                   value="{{ old('test_email', auth()->user()->email) }}" required>
                        </div>
                        <button class="btn btn-outline-primary w-100">
                            <i class="fas fa-paper-plane me-1"></i>{{ __('messages.mail_test_send') }}
                        </button>
                    </div>
                </form>
            </div>

            <div class="col-lg-7">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold text-dark-blue mb-3">{{ __('messages.mail_recent_logs') }}</h2>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.sent_at') }}</th>
                                        <th>{{ __('messages.email') }}</th>
                                        <th>{{ __('messages.status') }}</th>
                                        <th>Mailer</th>
                                        <th>{{ __('messages.mail_log_type') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentLogs as $log)
                                    <tr>
                                        <td class="small">{{ $log->sent_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                        <td class="small">
                                            {{ $log->to_email }}
                                            @if($log->error)
                                            <div class="text-danger">{{ \Illuminate\Support\Str::limit($log->error, 80) }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $log->wasSent() ? 'bg-success' : 'bg-danger' }}">
                                                {{ $log->wasSent() ? __('messages.mail_status_sent') : __('messages.mail_status_failed') }}
                                            </span>
                                        </td>
                                        <td class="small">{{ $log->mailer ?: '—' }}</td>
                                        <td class="small">{{ $log->typeLabel() }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-muted text-center">{{ __('messages.mail_no_logs') }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold text-dark-blue mb-1">{{ __('messages.mail_recent_reminders') }}</h2>
                        <p class="text-muted small mb-3">{{ __('messages.mail_recent_reminders_hint') }}</p>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.sent_at') }}</th>
                                        <th>{{ __('messages.patient_name') }}</th>
                                        <th>{{ __('messages.doctor') }}</th>
                                        <th>{{ __('messages.expected_next_visit') }}</th>
                                        <th>{{ __('messages.email') }}</th>
                                        <th>{{ __('messages.status') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reminderLogs as $log)
                                    <tr>
                                        <td class="small">{{ $log->sent_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                        <td class="small">
                                            @if($log->patient)
                                                <a href="{{ route('clinical.patients.show', $log->patient) }}">{{ $log->patient->name }}</a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="small">{{ $log->user?->name ?? '—' }}</td>
                                        <td class="small">
                                            {{ ($log->patient?->visit_reminder_sent_for ?? $log->patient?->expected_next_visit_at)?->format('d/m/Y') ?? '—' }}
                                        </td>
                                        <td class="small">
                                            {{ $log->to_email }}
                                            @if($log->error)
                                                <div class="text-danger">{{ \Illuminate\Support\Str::limit($log->error, 60) }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $log->wasSent() ? 'bg-success' : 'bg-danger' }}">
                                                {{ $log->wasSent() ? __('messages.mail_status_sent') : __('messages.mail_status_failed') }}
                                            </span>
                                        </td>
                                        <td class="small text-end">
                                            @if($log->patient && $log->patient->expected_next_visit_at)
                                                <a href="{{ route('clinical.reminders.preview', $log->patient) }}"
                                                   class="btn btn-outline-primary btn-sm"
                                                   target="_blank"
                                                   rel="noopener">
                                                    <i class="fas fa-eye me-1"></i>{{ __('messages.mail_preview_reminder') }}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="7" class="text-muted text-center">{{ __('messages.mail_no_reminders') }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold text-dark-blue mb-3">{{ __('messages.mail_recent_invites') }}</h2>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.patient_name') }}</th>
                                        <th>{{ __('messages.email') }}</th>
                                        <th>{{ __('messages.status') }}</th>
                                        <th>{{ __('messages.mail_visited_at') }}</th>
                                        <th>{{ __('messages.mail_sent_count') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentInvites as $invite)
                                    <tr>
                                        <td class="small">
                                            @if($invite->patient)
                                            <a href="{{ route('clinical.patients.show', $invite->patient) }}">{{ $invite->patient->name }}</a>
                                            @else
                                            —
                                            @endif
                                        </td>
                                        <td class="small">{{ $invite->email }}</td>
                                        <td>
                                            @php $status = $invite->statusKey(); @endphp
                                            <span class="badge
                                                {{ $status === 'completed' ? 'bg-success' : ($status === 'visited' ? 'bg-info text-dark' : ($status === 'expired' ? 'bg-secondary' : 'bg-warning text-dark')) }}">
                                                {{ __('messages.questionnaire_invite_status_'.$status) }}
                                            </span>
                                        </td>
                                        <td class="small">{{ $invite->visited_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                        <td class="small">{{ $invite->sent_count }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-muted text-center">{{ __('messages.mail_no_invites') }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
