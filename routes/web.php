<?php

use App\Http\Controllers\Admin\ApplicationAreaController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CenterCasesReportController;
use App\Http\Controllers\Admin\CenterController;
use App\Http\Controllers\Admin\CenterSuccessReportController;
use App\Http\Controllers\Admin\CriteriaController;
use App\Http\Controllers\Admin\CrmUserController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\FollowUpController;
use App\Http\Controllers\Admin\MailSettingsController;
use App\Http\Controllers\Admin\PathologyController;
use App\Http\Controllers\Admin\ProtocolSummaryController;
use App\Http\Controllers\Admin\SessionChecklistController;
use App\Http\Controllers\Admin\StepController;
use App\Http\Controllers\Admin\SuggestionController;
use App\Http\Controllers\Center\CredentialController;
use App\Http\Controllers\Clinical\PatientController;
use App\Http\Controllers\Clinical\PatientTagController;
use App\Http\Controllers\Clinical\QuestionnaireInviteController;
use App\Http\Controllers\Clinical\QuestionnaireScoreController;
use App\Http\Controllers\Clinical\ReportController;
use App\Http\Controllers\Clinical\TreatmentSessionController;
use App\Http\Controllers\Clinical\UpcomingVisitController;
use App\Http\Controllers\Clinical\VisitReminderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PathologyViewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicQuestionnaireController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::view('/presentacion', 'presentation.index')
    ->middleware(['auth', 'admin'])
    ->name('presentation');

Route::get('/locale/{locale}', LocaleController::class)
    ->whereIn('locale', ['es', 'en', 'pt', 'zh'])
    ->name('locale.switch');

Route::get('/category/{categorySlug}', [PathologyViewController::class, 'index'])->name('pathologies.index');
Route::get('/category/{categorySlug}/{pathologySlug}', [PathologyViewController::class, 'show'])->name('pathologies.show');

Route::post('/suggestions', [SuggestionController::class, 'store'])
    ->middleware('throttle:8,1')
    ->name('suggestions.store');

Route::middleware('throttle:30,1')->group(function () {
    Route::get('/q/{token}', [PublicQuestionnaireController::class, 'show'])
        ->where('token', '[A-Za-z0-9]{64}')
        ->name('public.questionnaires.show');
    Route::post('/q/{token}', [PublicQuestionnaireController::class, 'store'])
        ->where('token', '[A-Za-z0-9]{64}')
        ->name('public.questionnaires.store');
    Route::get('/q/{token}/done', [PublicQuestionnaireController::class, 'done'])
        ->where('token', '[A-Za-z0-9]{64}')
        ->name('public.questionnaires.done');
});

Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'verified', 'onboarding'])->name('dashboard');

Route::middleware(['auth', 'onboarding'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Catálogos y CRM: solo administradores. Los usuarios envían sugerencias vía POST /suggestions.
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('protocol-summary', ProtocolSummaryController::class)->name('protocol-summary');
        Route::get('reports/center-success', CenterSuccessReportController::class)->name('reports.center-success');
        Route::get('reports/center-cases', CenterCasesReportController::class)->name('reports.center-cases');
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
        Route::post('crm-users/{crm_user}/force-onboarding', [CrmUserController::class, 'forceOnboarding'])
            ->name('crm-users.force-onboarding');
        Route::resource('crm-users', CrmUserController::class);
        Route::resource('equipment', EquipmentController::class)->except(['show']);

        Route::get('mail', [MailSettingsController::class, 'edit'])->name('mail.edit');
        Route::put('mail', [MailSettingsController::class, 'update'])->name('mail.update');
        Route::post('mail/test', [MailSettingsController::class, 'sendTest'])->name('mail.test');
    });
});

Route::middleware(['auth', 'verified', 'onboarding'])->prefix('clinical')->name('clinical.')->group(function () {
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/patients/{patient}', [ReportController::class, 'patient'])->name('reports.patient');

    Route::get('upcoming', [UpcomingVisitController::class, 'index'])->name('upcoming.index');

    Route::resource('patients', PatientController::class);

    Route::post('patients/{patient}/tags', [PatientTagController::class, 'store'])->name('tags.store');
    Route::put('patients/{patient}/tags/{tag}', [PatientTagController::class, 'update'])->name('tags.update');
    Route::delete('patients/{patient}/tags/{tag}', [PatientTagController::class, 'destroy'])->name('tags.destroy');

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

    Route::get('patients/{patient}/questionnaire-invites/create', [QuestionnaireInviteController::class, 'create'])->name('invites.create');
    Route::post('patients/{patient}/questionnaire-invites', [QuestionnaireInviteController::class, 'store'])->name('invites.store');
    Route::post('patients/{patient}/questionnaire-invites/{invite}/resend', [QuestionnaireInviteController::class, 'resend'])->name('invites.resend');

    Route::get('patients/{patient}/visit-reminder/preview', [VisitReminderController::class, 'preview'])->name('reminders.preview');
    Route::post('patients/{patient}/visit-reminder/send', [VisitReminderController::class, 'send'])->name('reminders.send');
});

Route::middleware(['auth', 'verified', 'onboarding'])->prefix('center')->name('center.')->group(function () {
    Route::get('credentials', [CredentialController::class, 'index'])->name('credentials.index');
    Route::get('credentials/{user}/edit', [CredentialController::class, 'edit'])->name('credentials.edit');
    Route::put('credentials/{user}', [CredentialController::class, 'update'])->name('credentials.update');
});

require __DIR__.'/auth.php';

