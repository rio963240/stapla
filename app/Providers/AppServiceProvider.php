<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
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
        // パスワードは半角英数字を含む8文字以上16文字以下（管理画面の更新などで使用）
        Password::defaults(function () {
            return Password::min(8)->max(16)->letters()->numbers();
        });

        Event::listen(Login::class, function (Login $event): void {
            User::where('id', $event->user->getAuthIdentifier())
                ->update(['last_login_at' => now()]);
        });

        // Render 等で SSL 終端時、ロードバランサー経由だとリクエストが HTTP になるため
        // Mixed Content を防ぐため HTTPS を強制
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
