<section class="admin-backup-card">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs text-gray-500">自動バックアップ設定</p>
            <h2 class="admin-backup-title">自動バックアップ</h2>
            <p class="admin-backup-desc">
                自動バックアップの有効/無効と実行時刻を設定します。
            </p>
        </div>
        <label class="admin-switch" for="auto-backup-toggle" data-backup-switch>
            <input id="auto-backup-toggle" type="checkbox"
                {{ $setting['is_enabled'] ? 'checked' : '' }} />
            <span class="admin-switch-slider"></span>
        </label>
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
