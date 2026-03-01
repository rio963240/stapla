<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = count($guards) > 0 ? $guards : [null];

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // ログイン済みでも /register だけはそのまま表示させる
                if ($request->is('register')) {
                    return $next($request);
                }

                // それ以外は従来どおりホームへリダイレクト
                return redirect()->route('home');
            }
        }

        return $next($request);
    }
}

