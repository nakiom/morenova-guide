<?php

namespace App\Providers;

use App\Models\QuestionnaireScore;
use App\Models\TreatmentSession;
use Illuminate\Support\Facades\Route;
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
        Route::bind('session', fn (string $value) => TreatmentSession::findOrFail($value));
        Route::bind('score', fn (string $value) => QuestionnaireScore::findOrFail($value));

        \Illuminate\Pagination\Paginator::useBootstrapFive();

        Password::defaults(fn () => Password::min(6));
    }
}
