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
        <div>ストレージ使用量: <strong>{{ $storageUsageLabel }}</strong></div>
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
                        data-created="{{ $item['created_at'] ?? '' }}"
                        data-backup-id="{{ $item['id'] ?? '' }}">
                        <td>{{ $item['created_at'] ?? '-' }}</td>
                        <td>{{ $item['type_label'] ?? '-' }}</td>
                        <td>
                            <span
                                class="admin-backup-status admin-backup-status--{{ $item['status_key'] ?? 'failed' }}">{{ $item['status_label'] ?? '失敗' }}</span>
                        </td>
                        <td class="admin-backup-file">{{ $item['file_name'] ?? '-' }}</td>
                        <td>{{ $item['size_label'] ?? '-' }}</td>
                        <td>
                            @if (($item['status_key'] ?? '') === 'success')
                                <span class="admin-backup-actions">
                                    <button type="button" class="admin-button-link"
                                        data-backup-action="download"
                                        data-backup-url="{{ $item['download_url'] ?? '' }}">
                                        ダウンロード
                                    </button>
                                    <button type="button" class="admin-button-link"
                                        data-backup-action="delete"
                                        data-backup-url="{{ $item['delete_url'] ?? '' }}">
                                        削除
                                    </button>
                                </span>
                            @else
                                <button type="button" class="admin-button-link"
                                    data-backup-action="retry"
                                    data-backup-url="{{ $item['retry_url'] ?? '' }}">
                                    再実行
                                </button>
                            @endif
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
