<?php

use App\Http\Controllers\Admin\ApplicationAreaController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CenterController;
use App\Http\Controllers\Admin\CriteriaController;
use App\Http\Controllers\Admin\CrmUserController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\FollowUpController;
use App\Http\Controllers\Admin\PathologyController;
use App\Http\Controllers\Admin\ProtocolSummaryController;
use App\Http\Controllers\Admin\SessionChecklistController;
use App\Http\Controllers\Admin\StepController;
use App\Http\Controllers\Admin\SuggestionController;
use App\Http\Controllers\Center\CredentialController;
use App\Http\Controllers\Clinical\PatientController;
use App\Http\Controllers\Clinical\QuestionnaireScoreController;
use App\Http\Controllers\Clinical\ReportController;
use App\Http\Controllers\Clinical\TreatmentSessionController;
use App\Http\Controllers\Clinical\UpcomingVisitController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PathologyViewController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/locale/{locale}', LocaleController::class)
    ->whereIn('locale', ['es', 'en', 'pt', 'zh'])
    ->name('locale.switch');

Route::get('/category/{categorySlug}', [PathologyViewController::class, 'index'])->name('pathologies.index');
Route::get('/category/{categorySlug}/{pathologySlug}', [PathologyViewController::class, 'show'])->name('pathologies.show');

Route::post('/suggestions', [SuggestionController::class, 'store'])
    ->middleware('throttle:8,1')
    ->name('suggestions.store');

Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Catálogos y CRM: solo administradores. Los usuarios envían sugerencias vía POST /suggestions.
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('protocol-summary', ProtocolSummaryController::class)->name('protocol-summary');
        Route::resource('categories', CategoryController::class);
        Route::resource('pathologies', PathologyController::class);
        Route::resource('steps', StepController::class);
        Route::resource('session-checklist', SessionChecklistController::class)->except(['show']);
        Route::resource('follow-up', FollowUpController::class)->except(['show']);
        Route::resource('application-areas', ApplicationAreaController::class)->except(['show']);
        Route::resource('criteria', CriteriaController::class);
        Route::get('suggestions', [SuggestionController::class, 'index'])->name('suggestions.index');
        Route::patch('suggestions/{suggestion}', [SuggestionController::class, 'update'])->name('suggestions.update');

        Route::resource('centers', CenterController::class);
        Route::get('crm-users/{crm_user}/password', [CrmUserController::class, 'editPassword'])
            ->name('crm-users.password.edit');
        Route::put('crm-users/{crm_user}/password', [CrmUserController::class, 'updatePassword'])
            ->name('crm-users.password.update');
        Route::resource('crm-users', CrmUserController::class);
        Route::resource('equipment', EquipmentController::class)->except(['show']);
    });
});

Route::middleware(['auth', 'verified'])->prefix('clinical')->name('clinical.')->group(function () {
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/patients/{patient}', [ReportController::class, 'patient'])->name('reports.patient');

    Route::get('upcoming', [UpcomingVisitController::class, 'index'])->name('upcoming.index');

    Route::resource('patients', PatientController::class);

    Route::get('patients/{patient}/sessions/create', [TreatmentSessionController::class, 'create'])->name('sessions.create');
    Route::post('patients/{patient}/sessions', [TreatmentSessionController::class, 'store'])->name('sessions.store');
    Route::get('patients/{patient}/sessions/{session}/edit', [TreatmentSessionController::class, 'edit'])->name('sessions.edit');
    Route::put('patients/{patient}/sessions/{session}', [TreatmentSessionController::class, 'update'])->name('sessions.update');
    Route::delete('patients/{patient}/sessions/{session}', [TreatmentSessionController::class, 'destroy'])->name('sessions.destroy');

    Route::get('patients/{patient}/scores/create', [QuestionnaireScoreController::class, 'create'])->name('scores.create');
    Route::post('patients/{patient}/scores', [QuestionnaireScoreController::class, 'store'])->name('scores.store');
    Route::get('patients/{patient}/scores/{score}/edit', [QuestionnaireScoreController::class, 'edit'])->name('scores.edit');
    Route::put('patients/{patient}/scores/{score}', [QuestionnaireScoreController::class, 'update'])->name('scores.update');
    Route::delete('patients/{patient}/scores/{score}', [QuestionnaireScoreController::class, 'destroy'])->name('scores.destroy');
});

Route::middleware(['auth', 'verified'])->prefix('center')->name('center.')->group(function () {
    Route::get('credentials', [CredentialController::class, 'index'])->name('credentials.index');
    Route::get('credentials/{user}/edit', [CredentialController::class, 'edit'])->name('credentials.edit');
    Route::put('credentials/{user}', [CredentialController::class, 'update'])->name('credentials.update');
});

require __DIR__.'/auth.php';

