<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingsDestroyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class SettingsController extends Controller
{
    // 設定画面の表示
    public function index()
    {
        return view('settings');
    }

    // 基本情報（名前・アイコン）更新
    public function updateBasic(Request $request, UpdatesUserProfileInformation $updater)
    {
        $user = $request->user();
        $input = [
            'name' => $request->input('name'),
            'email' => $user->email,
        ];

        if ($request->hasFile('photo')) {
            $input['photo'] = $request->file('photo');
        }

        $updater->update($user, $input);

        // フロントのAjax更新向けにJSONを返す
        if ($request->expectsJson()) {
            $freshUser = $user->fresh();

            return response()->json([
                'status' => 'basic-info-updated',
                'name' => $freshUser?->name,
                'photo_url' => $freshUser?->profile_photo_url,
            ]);
        }

        // 通常のフォーム送信時は設定画面へ戻す
        return redirect()
            ->route('settings')
            ->with('status', 'basic-info-updated');
    }

    // パスワード更新
    public function updatePassword(Request $request, UpdatesUserPasswords $updater)
    {
        $user = $request->user();
        $input = $request->only([
            'current_password',
            'password',
            'password_confirmation',
        ]);

        $updater->update($user, $input);

        // フロントのAjax更新向けにJSONを返す
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'password-updated',
            ]);
        }

        // 通常のフォーム送信時は設定画面へ戻す
        return redirect()
            ->route('settings')
            ->with('status', 'password-updated');
    }

    // アカウント削除
    public function destroy(SettingsDestroyRequest $request)
    {
        $user = $request->user();
        // パスワード未設定ユーザーは削除確認を通せないため明示的に返す
        if (!$user || !$user->password) {
            $message = 'このアカウントにはパスワードが設定されていません。先にパスワードを設定してください。';
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'password' => [$message],
                    ],
                ], 422);
            }

            return back()->withErrors([
                'password' => $message,
            ]);
        }

        $request->validated();

        // Webガードでログアウトしてからアカウント削除
        Auth::guard('web')->logout();
        $user->delete();

        // セッションを完全に破棄してCSRFも更新
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // モーダルからのAjax削除時はリダイレクト先のみ返す
        if ($request->expectsJson()) {
            return response()->json([
                'redirect' => route('login'),
            ]);
        }

        // 通常のフォーム送信時はログインへ
        return redirect()->route('login');
    }
}
