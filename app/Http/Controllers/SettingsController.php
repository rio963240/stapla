<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingsDestroyRequest;
use App\Models\LineAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class SettingsController extends Controller
{
    // 設定画面の表示
    public function index()
    {
        $lineAccount = Auth::user()?->lineAccount;
        $lineLinkToken = session('line_link_token');

        return view('settings', [
            'lineAccount' => $lineAccount,
            'lineLinkToken' => $lineLinkToken,
        ]);
    }

    // LINE連携を開始（連携コード発行・QR表示用）
    public function startLineLink(Request $request)
    {
        $user = $request->user();
        $token = Str::upper(Str::random(8));

        try {
            DB::transaction(function () use ($user, $token) {
                $account = LineAccount::firstOrNew(['user_id' => $user->id]);
                $account->line_link_token = $token;
                $account->line_user_id = null;
                $account->is_linked = false;
                $account->save();
            });
        } catch (\Throwable $e) {
            Log::error('LINE link start failed.', ['user_id' => $user->id, 'exception' => $e->getMessage()]);

            return redirect()
                ->route('settings')
                ->with('error', 'LINE連携の準備に失敗しました。しばらく経ってから再度お試しください。');
        }

        return redirect()
            ->route('settings')
            ->with('line_link_token', $token);
    }

    // 通知設定（LINE通知のON/OFF・朝・夜の時間）を保存
    public function updateNotifications(Request $request)
    {
        $request->validate([
            'line_notify_enabled' => 'nullable|boolean',
            'line_morning_at' => 'required|string|regex:/^\d{2}:\d{2}$/',
            'line_evening_at' => 'required|string|regex:/^\d{2}:\d{2}$/',
        ]);

        $user = $request->user();
        $morning = $request->input('line_morning_at', '07:00');
        $evening = $request->input('line_evening_at', '21:00');

        try {
            $account = LineAccount::firstOrNew(['user_id' => $user->id]);
            $account->notification_morning_at = $morning;
            $account->notification_evening_at = $evening;
            if (!$account->exists) {
                $account->line_link_token = null;
                $account->line_user_id = null;
                $account->is_linked = false;
            }
            $account->save();
        } catch (\Throwable $e) {
            Log::error('Notification settings save failed.', ['user_id' => $user->id, 'exception' => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json(['message' => '保存に失敗しました'], 422);
            }
            return redirect()->route('settings')->with('error', '通知設定の保存に失敗しました。');
        }

        if ($request->expectsJson()) {
            return response()->json(['status' => 'notifications-updated']);
        }

        return redirect()
            ->route('settings')
            ->with('status', 'notifications-updated');
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
