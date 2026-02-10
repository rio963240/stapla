<x-app-layout :show-navigation="false">
    @php
        $users = $users ?? collect();
        $filters = $filters ?? ['search' => '', 'role' => 'all', 'status' => 'all', 'sort' => 'desc'];
    @endphp
    @push('styles')
        @vite('resources/css/admin/admin-users.css')
    @endpush

    <div class="h-screen bg-gray-100 overflow-hidden">
        <div class="mx-auto flex h-[calc(100vh-3rem)] max-w-7xl gap-8 px-6 py-6 sm:px-6 lg:px-8">
            @include('home.sidebar', ['context' => 'admin', 'isAdmin' => true])

            <section class="flex-1 rounded-lg bg-white p-6 shadow-sm overflow-auto">
                <div class="admin-users-header">
                    <div>
                        {{-- <p class="text-sm text-gray-500">ユーザー管理</p> --}}
                        <h1 class="text-xl font-semibold text-gray-800">ユーザー管理</h1>
                    </div>
                </div>

                {{-- 一覧画面 --}}
                <div id="admin-users-list" class="admin-users-list">
                    <form method="GET" action="{{ route('admin.users') }}" class="admin-users-filters"
                        data-admin-users-filters>
                        <div class="admin-users-search-row">
                            <label for="admin-users-search" class="admin-users-search-label">ユーザー検索:</label>
                            <input type="text" id="admin-users-search" name="search"
                                value="{{ old('search', $filters['search']) }}" placeholder="名前またはメールで検索"
                                class="admin-users-search-input">
                            <button type="submit" class="admin-button-primary">検索</button>
                        </div>
                        <div class="admin-users-filter-row">
                            <label class="admin-filter">
                                <span>権限:</span>
                                <select name="role" class="admin-filter-select" data-admin-users-role>
                                    <option value="all" {{ ($filters['role'] ?? 'all') === 'all' ? 'selected' : '' }}>全て</option>
                                    <option value="admin" {{ ($filters['role'] ?? '') === 'admin' ? 'selected' : '' }}>管理者</option>
                                    <option value="general" {{ ($filters['role'] ?? '') === 'general' ? 'selected' : '' }}>一般</option>
                                </select>
                            </label>
                            <label class="admin-filter">
                                <span>状態:</span>
                                <select name="status" class="admin-filter-select" data-admin-users-status>
                                    <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>全て</option>
                                    <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>有効</option>
                                    <option value="stopped" {{ ($filters['status'] ?? '') === 'stopped' ? 'selected' : '' }}>停止</option>
                                </select>
                            </label>
                            <label class="admin-filter">
                                <span>ログイン:</span>
                                <select name="sort" class="admin-filter-select" data-admin-users-sort>
                                    <option value="asc" {{ ($filters['sort'] ?? '') === 'asc' ? 'selected' : '' }}>昇順</option>
                                    <option value="desc" {{ ($filters['sort'] ?? 'desc') === 'desc' ? 'selected' : '' }}>降順</option>
                                </select>
                            </label>
                            <button type="submit" class="admin-button-secondary">適用</button>
                        </div>
                    </form>

                    <div class="admin-users-table-wrap">
                        <table class="admin-users-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>名前</th>
                                    <th>メールアドレス</th>
                                    <th>権限</th>
                                    <th>状態</th>
                                    <th>最終ログイン</th>
                                    <th>編集</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $u)
                                    <tr data-user-id="{{ $u['id'] }}"
                                        data-user-name="{{ e($u['name']) }}"
                                        data-user-email="{{ e($u['email']) }}"
                                        data-user-role="{{ $u['role'] }}"
                                        data-user-status="{{ $u['status'] }}">
                                        <td>{{ $u['id'] }}</td>
                                        <td>{{ $u['name'] }}</td>
                                        <td>{{ $u['email'] }}</td>
                                        <td>{{ $u['role_label'] }}</td>
                                        <td>
                                            <span
                                                class="admin-users-status admin-users-status--{{ $u['status'] }}">{{ $u['status_label'] }}</span>
                                        </td>
                                        <td>{{ $u['last_login_at'] }}</td>
                                        <td>
                                            <button type="button" class="admin-button-link" data-admin-users-edit>
                                                編集
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-gray-500 py-6">ユーザーがありません。</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @include('admin.users.edit')
                @include('admin.users.confirm')
            </section>
        </div>
    </div>

    <div class="admin-toast-stack" aria-live="polite" aria-atomic="true">
        <div class="admin-toast" role="status" data-admin-users-toast>
            <span class="admin-toast-label"></span>
        </div>
    </div>

    @push('scripts')
        <script>
            window.ADMIN_USERS_UPDATE_URL = "{{ url('admin/users') }}";
        </script>
        @vite('resources/js/admin/admin-users.js')
    @endpush
</x-app-layout>
