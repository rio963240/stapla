<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function toResponse($request)
    {
        if (! $request instanceof Request) {
            $request = Request::createFromBase($request);
        }

        $user = $request->user();
        $intended = $request->session()->pull('url.intended');

        if ($intended !== null) {
            $path = parse_url($intended, PHP_URL_PATH) ?? '';

            if (Str::startsWith($path, '/admin') && (! $user || ! $user->is_admin)) {
                return redirect()->route('home');
            }
        }

        return redirect()->intended(config('fortify.home'));
    }
}

