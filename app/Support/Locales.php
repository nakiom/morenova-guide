<?php

declare(strict_types=1);

namespace App\Support;

final class Locales
{
    /** @var list<string> */
    public const SUPPORTED = ['es', 'en', 'pt', 'zh'];

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            'es' => 'Español',
            'en' => 'English',
            'pt' => 'Português',
            'zh' => '中文',
        ];
    }

    public static function isSupported(?string $locale): bool
    {
        return is_string($locale) && in_array($locale, self::SUPPORTED, true);
    }

    public static function normalize(?string $locale): ?string
    {
        if (! is_string($locale) || $locale === '') {
            return null;
        }

        $locale = strtolower(str_replace('_', '-', trim($locale)));
        $primary = explode('-', $locale)[0] ?? $locale;

        if (self::isSupported($locale)) {
            return $locale;
        }

        if (self::isSupported($primary)) {
            return $primary;
        }

        return null;
    }
}
