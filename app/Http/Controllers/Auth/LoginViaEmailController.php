<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LoginViaEmailController extends Controller
{
    /**
     * メール内のワンクリックログインリンクからログインする。
     * 署名付きURL・有効期限・1回限り使用を検証する。
     */
    public function __invoke(Request $request, User $user): RedirectResponse
    {
        $signature = $request->query('signature');
        if (! $signature) {
            return redirect()->route('login')->with('error', '無効なリンクです。');
        }

        $cacheKey = 'login_via_email_used_'.md5($signature);
        if (Cache::has($cacheKey)) {
            return redirect()->route('login')->with('error', 'このログインリンクは既に使用されています。');
        }

        Auth::login($user, true);
        Cache::put($cacheKey, true, now()->addDays(7));

        return redirect()->intended(route('home'));
    }
}
