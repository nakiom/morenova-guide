<?php

declare(strict_types=1);

namespace App\Support;

final class SuggestionCaptcha
{
    public static function ensure(): void
    {
        if (session()->has('suggestion_captcha_a') && session()->has('suggestion_captcha_b')) {
            return;
        }

        self::refresh();
    }

    public static function refresh(): void
    {
        session([
            'suggestion_captcha_a' => random_int(1, 9),
            'suggestion_captcha_b' => random_int(1, 9),
        ]);
    }

    public static function question(): string
    {
        self::ensure();

        return (string) session('suggestion_captcha_a').' + '.(string) session('suggestion_captcha_b');
    }

    public static function expected(): int
    {
        self::ensure();

        return (int) session('suggestion_captcha_a') + (int) session('suggestion_captcha_b');
    }

    public static function check(mixed $answer): bool
    {
        if ($answer === null || $answer === '') {
            return false;
        }

        return (int) $answer === self::expected();
    }
}
