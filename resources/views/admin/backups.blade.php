<x-app-layout :show-navigation="false">
    @php
        $backupItems = $backupItems ?? [];
        $setting = $setting ?? [
            'is_enabled' => false,
            'run_time' => '02:00',
            'frequency' => 'daily',
        ];
        $latestBackupAt = $latestBackupAt ?? '-';
        $autoSettingLabel = $setting['is_enabled']
            ? "ON（毎日 {$setting['run_time']} 実行）"
            : 'OFF';
    @endphp
    @push('styles')
        @vite('resources/css/admin/admin-backups.css')
    @endpush

    <div class="h-screen bg-gray-100 overflow-hidden">
        <div class="mx-auto flex h-[calc(100vh-3rem)] max-w-7xl gap-8 px-6 py-6 sm:px-6 lg:px-8">
            @include('home.sidebar', ['context' => 'admin', 'isAdmin' => true])

            <section class="flex-1 rounded-lg bg-white p-6 shadow-sm overflow-auto">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">バックアップ管理</p>
                        <h1 class="text-xl font-semibold text-gray-800">バックアップ</h1>
                    </div>
                    <div class="text-xs text-gray-400">最終更新: <span
                            data-backup-last-updated>{{ $latestBackupAt }}</span>
                    </div>
                </div>

                <div class="admin-backup-grid mt-6">
                    <section class="admin-backup-card">
                        <div>
                            <p class="text-xs text-gray-500">手動バックアップ管理</p>
                            <h2 class="admin-backup-title">手動バックアップ</h2>
                            <p class="admin-backup-desc">
                                今すぐバックアップを作成します。作成完了後は履歴に反映されます。
                            </p>
                        </div>
                        <div class="admin-backup-actions">
                            <button type="button" class="admin-button-primary" data-backup-manual
                                data-backup-manual-url="{{ route('admin.backups.manual') }}">
                                今すぐバックアップ作成
                            </button>
                            <p class="admin-backup-hint">バックアップ作成中は画面を開いたままにしてください。</p>
                        </div>
                    </section>

                    <section class="admin-backup-card">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500">自動バックアップ設定</p>
                                <h2 class="admin-backup-title">自動バックアップ</h2>
                                <p class="admin-backup-desc">
                                    自動バックアップの有効/無効と実行時刻を設定します。
                                </p>
                            </div>
                            <div class="admin-switch" data-backup-switch>
                                <input id="auto-backup-toggle" type="checkbox"
                                    {{ $setting['is_enabled'] ? 'checked' : '' }} />
                                <label for="auto-backup-toggle">
                                    <span class="admin-switch-track"></span>
                                    <span class="admin-switch-thumb"></span>
                                </label>
                            </div>
                        </div>

                        <div class="admin-backup-actions">
                            <div class="admin-backup-row">
                                <label class="admin-backup-label" for="auto-backup-time">実行時刻</label>
                                <select id="auto-backup-time" class="admin-backup-select" data-backup-time>
                                    @foreach (['00:00', '01:00', '02:00', '03:00', '04:00', '05:00'] as $time)
                                        <option value="{{ $time }}"
                                            {{ $setting['run_time'] === $time ? 'selected' : '' }}>
                                            {{ $time }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="admin-button-secondary" data-backup-save
                                    data-backup-setting-url="{{ route('admin.backups.settings') }}">
                                    保存
                                </button>
                            </div>
                            <p class="admin-backup-hint">
                                現在の設定: <span data-backup-setting>{{ $autoSettingLabel }}</span>
                            </p>
                        </div>
                    </section>
                </div>

                <section class="admin-backup-card mt-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="text-xs text-gray-500">バックアップ一覧</p>
                            <h2 class="admin-backup-title">バックアップ履歴</h2>
                        </div>
                        <div class="admin-backup-filters">
                            <label class="admin-filter">
                                <span>ステータス</span>
                                <select class="admin-filter-select" data-backup-sort-status>
                                    <option value="all" selected>全て</option>
                                    <option value="success">成功</option>
                                    <option value="failed">失敗</option>
                                </select>
                            </label>
                            <label class="admin-filter">
                                <span>作成日時</span>
                                <select class="admin-filter-select" data-backup-sort-date>
                                    <option value="desc" selected>新しい順</option>
                                    <option value="asc">古い順</option>
                                </select>
                            </label>
                        </div>
                    </div>

                    <div class="admin-backup-summary">
                        <div>最終バックアップ: <strong data-backup-latest>{{ $latestBackupAt }}</strong></div>
                        <div>自動バックアップ: <strong data-backup-auto-summary>{{ $autoSettingLabel }}</strong></div>
                    </div>

                    <div class="admin-backup-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>作成日時</th>
                                    <th>種別</th>
                                    <th>ステータス</th>
                                    <th>ファイル名</th>
                                    <th>サイズ</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody data-backup-table-body>
                                @forelse ($backupItems as $item)
                                    <tr data-status="{{ $item['status_key'] ?? 'failed' }}"
                                        data-created="{{ $item['created_at'] ?? '' }}">
                                        <td>{{ $item['created_at'] ?? '-' }}</td>
                                        <td>{{ $item['type_label'] ?? '-' }}</td>
                                        <td>
                                            <span
                                                class="admin-backup-status admin-backup-status--{{ $item['status_key'] ?? 'failed' }}">{{ $item['status_label'] ?? '失敗' }}</span>
                                        </td>
                                        <td class="admin-backup-file">{{ $item['file_name'] ?? '-' }}</td>
                                        <td>{{ $item['size_label'] ?? '-' }}</td>
                                        <td>
                                            <button type="button" class="admin-button-link"
                                                data-backup-action="{{ ($item['file_name'] ?? '-') !== '-' ? 'download' : 'retry' }}">
                                                {{ ($item['file_name'] ?? '-') !== '-' ? '削除' : '再実行' }}
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr data-backup-empty>
                                        <td colspan="6" class="text-center text-gray-500 py-6">バックアップ履歴がありません。</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </section>
        </div>
    </div>

    <div class="admin-toast-stack" aria-live="polite" aria-atomic="true">
        <div class="admin-toast" role="status" data-backup-toast>
            <span class="admin-toast-label"></span>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/admin/admin-backups.js')
    @endpush
</x-app-layout>
