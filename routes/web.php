<?php

use App\Http\Controllers\Admin\AdminQualificationsController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlanRegisterController;
use App\Http\Controllers\PlanRegisterSubdomainController;
use App\Http\Controllers\PlanRescheduleController;
use App\Http\Controllers\QualificationController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StudyProgressController;
use App\Http\Controllers\StudyRecordController;
use Illuminate\Support\Facades\Route;

// トップ（welcomeビュー）
Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // 設定ページ（表示・更新・削除）
    // 設定ページ（SettingsController@index）
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    // 基本情報更新（SettingsController@updateBasic）
    Route::put('/settings/basic', [SettingsController::class, 'updateBasic'])
        ->name('settings.basic.update');
    // パスワード更新（SettingsController@updatePassword）
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])
        ->name('settings.password.update');
    // アカウント削除（SettingsController@destroy）
    Route::delete('/settings/account', [SettingsController::class, 'destroy'])
        ->name('settings.account.destroy');

    // ログイン後の遷移先
    // ログイン後ホーム（HomeController@index）
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // 資格ごとのドメイン一覧（QualificationController@domains）
    Route::get('/qualifications/{qualificationId}/domains', [QualificationController::class, 'domains'])
        ->name('qualifications.domains');
    // 資格ごとのサブドメイン一覧（QualificationController@subdomains）
    Route::get('/qualifications/{qualificationId}/subdomains', [QualificationController::class, 'subdomains'])
        ->name('qualifications.subdomains');

    // 学習計画：ドメイン登録（PlanRegisterController@storeDomain）
    Route::post('/plan-register/domain', [PlanRegisterController::class, 'storeDomain'])
        ->name('plan-register.domain');
    // 学習計画：サブドメイン登録（PlanRegisterSubdomainController@store）
    Route::post('/plan-register/subdomain', [PlanRegisterSubdomainController::class, 'store'])
        ->name('plan-register.subdomain');
    // リスケジュール（保存/初期データ取得）
    // リスケジュール保存（PlanRescheduleController@store）
    Route::post('/plan-reschedule', [PlanRescheduleController::class, 'store'])
        ->name('plan-reschedule.store');
    // リスケジュール対象データ取得（PlanRescheduleController@data）
    Route::get('/plan-reschedule/target', [PlanRescheduleController::class, 'data'])
        ->name('plan-reschedule.data');

    // カレンダーイベント取得（CalendarController@events）
    Route::get('/calendar/events', [CalendarController::class, 'events'])
        ->name('calendar.events');

    // 学習進捗ページ（StudyProgressController@index）
    Route::get('/study-progress', [StudyProgressController::class, 'index'])
        ->name('study-progress');
    // 学習進捗データ取得（StudyProgressController@data）
    Route::get('/study-progress/data', [StudyProgressController::class, 'data'])
        ->name('study-progress.data');

    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        // 管理画面ホーム（HomeController@adminIndex）
        Route::get('/home', [HomeController::class, 'adminIndex'])->name('home');

        // 管理画面：学習進捗（StudyProgressController@index）
        Route::get('/study-progress', [StudyProgressController::class, 'index'])
            ->name('study-progress');

        // 管理画面：資格一覧（AdminQualificationsController@index）
        Route::get('/qualifications', [AdminQualificationsController::class, 'index'])->name('qualifications');
        // 管理画面：資格更新（AdminQualificationsController@updateQualification）
        Route::patch('/qualifications/{qualification}', [AdminQualificationsController::class, 'updateQualification'])
            ->name('qualifications.update');
        // 管理画面：資格削除（AdminQualificationsController@destroyQualification）
        Route::delete('/qualifications/{qualification}', [AdminQualificationsController::class, 'destroyQualification'])
            ->name('qualifications.destroy');
        // 管理画面：ドメイン更新（AdminQualificationsController@updateDomain）
        Route::patch('/domains/{domain}', [AdminQualificationsController::class, 'updateDomain'])
            ->name('domains.update');
        // 管理画面：ドメイン削除（AdminQualificationsController@destroyDomain）
        Route::delete('/domains/{domain}', [AdminQualificationsController::class, 'destroyDomain'])
            ->name('domains.destroy');
        // 管理画面：サブドメイン更新（AdminQualificationsController@updateSubdomain）
        Route::patch('/subdomains/{subdomain}', [AdminQualificationsController::class, 'updateSubdomain'])
            ->name('subdomains.update');
        // 管理画面：サブドメイン削除（AdminQualificationsController@destroySubdomain）
        Route::delete('/subdomains/{subdomain}', [AdminQualificationsController::class, 'destroySubdomain'])
            ->name('subdomains.destroy');
        // 管理画面：バックアップ（admin.backupsビュー）
        Route::view('/backups', 'admin.backups')->name('backups');
        // 管理画面：ユーザー（admin.usersビュー）
        Route::view('/users', 'admin.users')->name('users');
    });

    // TODO単位の学習記録表示（StudyRecordController@showTodo）
    Route::get('/study-records/todo/{todoId}', [StudyRecordController::class, 'showTodo'])
        ->name('study-records.todo');
    // 学習記録登録（StudyRecordController@store）
    Route::post('/study-records', [StudyRecordController::class, 'store'])
        ->name('study-records.store');

    // ダッシュボード（DashboardController@__invoke）
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});
