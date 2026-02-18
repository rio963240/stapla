<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 今回はサービス登録なし
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 認証処理をカスタマイズ（メール小文字化・無効ユーザー排除）
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::query()->where('email', Str::lower($request->input(Fortify::username())))->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return null;
            }
            if (!$user->is_active) {
                return null;
            }
            return $user;
        });

        // Fortifyのアクションを登録
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        // パスワード再設定メールの文面を差し替え
        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            $expire = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');
            $name = $notifiable->name ?? null;
            $greeting = $name ? "{$name} 様" : 'ご利用者様';

            return (new MailMessage)
                ->subject('【スタプラ】パスワード再設定のご案内')
                ->markdown('emails.password-reset', [
                    'greeting' => $greeting,
                    'actionUrl' => $url,
                    'expire' => $expire,
                ]);
        });

        // ログイン試行のレート制限
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        // 二要素認証のレート制限
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
