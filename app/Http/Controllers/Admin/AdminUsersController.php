<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUserUpdateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class AdminUsersController extends Controller
{
    public function index(Request $request)
    {
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

        $users = $query->get()->map(fn (User $u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'role' => $u->is_admin ? 'admin' : 'general',
            'role_label' => $u->is_admin ? '管理者' : '一般',
            'status' => $u->is_active ? 'active' : 'stopped',
            'status_label' => $u->is_active ? '有効' : '停止',
            'last_login_at' => $u->last_login_at?->format('Y/n/j') ?: '-',
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
        $data = $request->validated();

        try {
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
                'last_login_at' => $user->last_login_at?->format('Y/n/j') ?: '-',
            ],
        ]);
    }
}
