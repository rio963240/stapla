<?php

use App\Http\Controllers\SettingsController;
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
        return view('home');
    })->name('home');

    // ダッシュボードはホームにリダイレクト
    Route::get('/dashboard', function () {
        return redirect()->route('home');
    })->name('dashboard');
});
