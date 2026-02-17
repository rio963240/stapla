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
            data-backup-manual-url="{{ route('admin.backups.manual') }}"
            data-backup-list-url="{{ route('admin.backups.list') }}">
            今すぐバックアップ作成
        </button>
        <p class="admin-backup-hint">バックアップ作成中は画面を開いたままにしてください。</p>
    </div>
</section>
