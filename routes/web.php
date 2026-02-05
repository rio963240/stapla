<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\PlanRegisterController;
use App\Http\Controllers\PlanRegisterSubdomainController;
use App\Http\Controllers\QualificationController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StudyRecordController;
use App\Http\Controllers\StudyProgressController;
use App\Models\Qualification;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // 設定ページ（表示・更新・削除）
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings/basic', [SettingsController::class, 'updateBasic'])
        ->name('settings.basic.update');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])
        ->name('settings.password.update');
    Route::delete('/settings/account', [SettingsController::class, 'destroy'])
        ->name('settings.account.destroy');

    // ログイン後の遷移先
    Route::get('/home', function () {
        $qualifications = Qualification::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['qualification_id', 'name']);

        return view('home', compact('qualifications'));
    })->name('home');

    Route::get('/qualifications/{qualificationId}/domains', [QualificationController::class, 'domains'])
        ->name('qualifications.domains');
    Route::get('/qualifications/{qualificationId}/subdomains', [QualificationController::class, 'subdomains'])
        ->name('qualifications.subdomains');

    Route::post('/plan-register/domain', [PlanRegisterController::class, 'storeDomain'])
        ->name('plan-register.domain');
    Route::post('/plan-register/subdomain', [PlanRegisterSubdomainController::class, 'store'])
        ->name('plan-register.subdomain');

    Route::get('/calendar/events', [CalendarController::class, 'events'])
        ->name('calendar.events');

    Route::get('/study-progress', [StudyProgressController::class, 'index'])
        ->name('study-progress');
    Route::get('/study-progress/data', [StudyProgressController::class, 'data'])
        ->name('study-progress.data');

    Route::get('/study-records/todo/{todoId}', [StudyRecordController::class, 'showTodo'])
        ->name('study-records.todo');
    Route::post('/study-records', [StudyRecordController::class, 'store'])
        ->name('study-records.store');

    // ダッシュボードはホームにリダイレクト
    Route::get('/dashboard', function () {
        return redirect()->route('home');
    })->name('dashboard');
});
