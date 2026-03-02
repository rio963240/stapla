<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUserUpdateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AdminUsersController extends Controller
{
    public function index(Request $request)
    {
        // ユーザー一覧の取得クエリ
        $query = User::query()
            ->select(['id', 'name', 'email', 'is_admin', 'is_active', 'last_login_at']);

        // ユーザー検索（名前・メール）
        $search = $request->query('search');
        if ($search && is_string($search)) {
            $search = trim($search);
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            }
        }

        // 権限フィルター
        $role = $request->query('role');
        if ($role === 'admin') {
            $query->where('is_admin', true);
        } elseif ($role === 'general') {
            $query->where('is_admin', false);
        }

        // 状態フィルター
        $status = $request->query('status');
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'stopped') {
            $query->where('is_active', false);
        }

        // ログイン日時でソート
        $sort = $request->query('sort', 'desc');
        $query->orderBy('last_login_at', $sort === 'asc' ? 'asc' : 'desc');

        // 管理者が何人いるか（最後の1人を削除不可にするため）
        $adminCount = User::where('is_admin', true)->count();

        // 表示用に整形
        $users = $query->get()->map(fn (User $u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'role' => $u->is_admin ? 'admin' : 'general',
            'role_label' => $u->is_admin ? '管理者' : '一般',
            'status' => $u->is_active ? 'active' : 'stopped',
            'status_label' => $u->is_active ? '有効' : '停止',
            'last_login_at' => $u->last_login_at?->format('Y/n/j H:i:s') ?: '-',
            'is_only_admin' => $u->is_admin && $adminCount === 1,
        ]);

        return view('admin.users', [
            'users' => $users,
            'filters' => [
                'search' => $search ?? '',
                'role' => $role ?? 'all',
                'status' => $status ?? 'all',
                'sort' => $sort,
            ],
        ]);
    }

    public function update(AdminUserUpdateRequest $request, User $user): JsonResponse
    {
        // 名前/権限/状態/パスワードの更新
        $data = $request->validated();

        try {
            $user->name = $data['name'];
            $user->is_admin = filter_var($data['is_admin'], FILTER_VALIDATE_BOOLEAN);
            $user->is_active = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN);
            if (!empty($data['password'] ?? '')) {
                $user->password = $data['password'];
            }
            $user->save();
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => '更新に失敗しました',
            ], 500);
        }

        // 変更後のユーザー情報を返却
        return response()->json([
            'status' => 'success',
            'message' => '更新しました',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->is_admin ? 'admin' : 'general',
                'role_label' => $user->is_admin ? '管理者' : '一般',
                'status' => $user->is_active ? 'active' : 'stopped',
                'status_label' => $user->is_active ? '有効' : '停止',
                'last_login_at' => $user->last_login_at?->format('Y/n/j H:i:s') ?: '-',
            ],
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        // 自分自身は削除できない
        if (Auth::id() === $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => '自分自身のアカウントは削除できません',
            ], 403);
        }

        // 最後の1人である管理者は削除できない（少なくとも1人の管理者を残す）
        if ($user->is_admin) {
            $adminCount = User::where('is_admin', true)->count();
            if ($adminCount <= 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => '最後の管理者は削除できません。少なくとも1人の管理者が必要です。',
                ], 403);
            }
        }

        try {
            $user->delete();
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => '削除に失敗しました',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'ユーザーを削除しました',
        ]);
    }
}
