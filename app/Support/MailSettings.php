<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;

final class MailSettings
{
    public const MAILERS = ['log', 'smtp', 'mailgun'];

    /**
     * @return array<string, mixed>
     */
    public static function current(): array
    {
        return [
            'mailer' => (string) Setting::getValue('mail.mailer', config('mail.default', 'log')),
            'from_address' => (string) Setting::getValue('mail.from.address', config('mail.from.address')),
            'from_name' => (string) Setting::getValue('mail.from.name', config('mail.from.name')),
            'smtp_host' => (string) Setting::getValue('mail.smtp.host', config('mail.mailers.smtp.host')),
            'smtp_port' => (string) Setting::getValue('mail.smtp.port', (string) config('mail.mailers.smtp.port')),
            'smtp_username' => (string) Setting::getValue('mail.smtp.username', config('mail.mailers.smtp.username')),
            'smtp_password' => (string) Setting::getValue('mail.smtp.password', config('mail.mailers.smtp.password')),
            'smtp_encryption' => (string) Setting::getValue('mail.smtp.encryption', config('mail.mailers.smtp.scheme') ?? ''),
            'mailgun_domain' => (string) Setting::getValue('mail.mailgun.domain', config('services.mailgun.domain')),
            'mailgun_secret' => (string) Setting::getValue('mail.mailgun.secret', config('services.mailgun.secret')),
            'mailgun_endpoint' => (string) Setting::getValue('mail.mailgun.endpoint', config('services.mailgun.endpoint', 'api.mailgun.net')),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function save(array $data): void
    {
        $current = self::current();

        // No sobrescribir secretos si vienen vacíos (placeholder de edición).
        if (($data['smtp_password'] ?? '') === '') {
            $data['smtp_password'] = $current['smtp_password'];
        }
        if (($data['mailgun_secret'] ?? '') === '') {
            $data['mailgun_secret'] = $current['mailgun_secret'];
        }

        Setting::setMany([
            'mail.mailer' => $data['mailer'] ?? 'log',
            'mail.from.address' => $data['from_address'] ?? '',
            'mail.from.name' => $data['from_name'] ?? '',
            'mail.smtp.host' => $data['smtp_host'] ?? '',
            'mail.smtp.port' => $data['smtp_port'] ?? '',
            'mail.smtp.username' => $data['smtp_username'] ?? '',
            'mail.smtp.password' => $data['smtp_password'] ?? '',
            'mail.smtp.encryption' => $data['smtp_encryption'] ?? '',
            'mail.mailgun.domain' => $data['mailgun_domain'] ?? '',
            'mail.mailgun.secret' => $data['mailgun_secret'] ?? '',
            'mail.mailgun.endpoint' => $data['mailgun_endpoint'] ?? 'api.mailgun.net',
        ]);

        self::apply();
    }

    public static function apply(): void
    {
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('settings')) {
                return;
            }
        } catch (\Throwable) {
            return;
        }

        $settings = self::current();
        $mailer = in_array($settings['mailer'], self::MAILERS, true) ? $settings['mailer'] : 'log';

        Config::set('mail.default', $mailer);
        Config::set('mail.from.address', $settings['from_address'] ?: config('mail.from.address'));
        Config::set('mail.from.name', $settings['from_name'] ?: config('mail.from.name'));

        Config::set('mail.mailers.smtp.host', $settings['smtp_host'] ?: config('mail.mailers.smtp.host'));
        Config::set('mail.mailers.smtp.port', $settings['smtp_port'] !== '' ? (int) $settings['smtp_port'] : config('mail.mailers.smtp.port'));
        Config::set('mail.mailers.smtp.username', $settings['smtp_username'] ?: null);
        Config::set('mail.mailers.smtp.password', $settings['smtp_password'] ?: null);

        $encryption = $settings['smtp_encryption'] ?: null;
        if (in_array($encryption, ['tls', 'ssl'], true)) {
            Config::set('mail.mailers.smtp.scheme', $encryption);
        } elseif ($encryption === 'null' || $encryption === '') {
            Config::set('mail.mailers.smtp.scheme', null);
        }

        Config::set('services.mailgun.domain', $settings['mailgun_domain'] ?: config('services.mailgun.domain'));
        Config::set('services.mailgun.secret', $settings['mailgun_secret'] ?: config('services.mailgun.secret'));
        Config::set('services.mailgun.endpoint', $settings['mailgun_endpoint'] ?: config('services.mailgun.endpoint'));
    }
}
