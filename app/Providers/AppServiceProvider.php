<?php

namespace App\Providers;

use App\Models\Patient;
use App\Models\QuestionnaireScore;
use App\Models\TreatmentSession;
use App\Support\MailSettings;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::bind('patient', function (string $value) {
            $query = Patient::query();

            if (auth()->user()?->isAdmin()) {
                $query->withTrashed();
            }

            return $query->whereKey($value)->firstOrFail();
        });

        Route::bind('session', fn (string $value) => TreatmentSession::findOrFail($value));
        Route::bind('score', fn (string $value) => QuestionnaireScore::findOrFail($value));

        \Illuminate\Pagination\Paginator::useBootstrapFive();

        Password::defaults(fn () => Password::min(6));

        $appUrl = (string) config('app.url');
        if (config('app.force_https') || str_starts_with($appUrl, 'https://')) {
            URL::forceScheme('https');
        }

        MailSettings::apply();
    }
}
