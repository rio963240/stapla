<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        // 管理者は管理画面へ、それ以外はホームへリダイレクト
        if ($request->user()?->is_admin) {
            return redirect()->route('admin.home');
        }

        return redirect()->route('home');
    }
}
