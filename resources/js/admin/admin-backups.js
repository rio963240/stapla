// 管理画面（バックアップ）UIの初期化
const initAdminBackups = () => {
    const toast = document.querySelector('[data-backup-toast]');
    const toastLabel = toast?.querySelector('.admin-toast-label');
    const manualButton = document.querySelector('[data-backup-manual]');
    const lastUpdated = document.querySelector('[data-backup-last-updated]');
    const latestBackup = document.querySelector('[data-backup-latest]');

    const toggleInput = document.getElementById('auto-backup-toggle');
    const timeSelect = document.querySelector('[data-backup-time]');
    const saveButton = document.querySelector('[data-backup-save]');
    const settingLabel = document.querySelector('[data-backup-setting]');
    const autoSummary = document.querySelector('[data-backup-auto-summary]');

    const statusFilter = document.querySelector('[data-backup-sort-status]');
    const dateFilter = document.querySelector('[data-backup-sort-date]');
    const tableBody = document.querySelector('[data-backup-table-body]');

    // 表示用の日時フォーマット
    const formatDate = (date) => {
        const pad = (value) => String(value).padStart(2, '0');
        return `${date.getFullYear()}/${pad(date.getMonth() + 1)}/${pad(date.getDate())} ${pad(
            date.getHours(),
        )}:${pad(date.getMinutes())}`;
    };

    // CSRFトークン取得
    const getCsrfToken = () =>
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // トースト表示
    const showToast = (message, status = 'success') => {
        if (!toast || !toastLabel) return;
        toastLabel.textContent = message;
        toast.classList.toggle('is-error', status === 'error');
        toast.classList.add('is-visible');
        window.setTimeout(() => toast.classList.remove('is-visible'), 3500);
    };

    // 自動バックアップのON/OFF表示反映
    const syncAutoState = () => {
        if (!toggleInput || !timeSelect) return;
        const isOn = toggleInput.checked;
        timeSelect.disabled = !isOn;
        saveButton?.classList.toggle('is-disabled', !isOn);
        if (saveButton) {
            saveButton.disabled = !isOn;
        }
        const timeValue = timeSelect.value;
        const label = isOn ? `ON（毎日 ${timeValue} 実行）` : 'OFF';
        if (settingLabel) settingLabel.textContent = label;
        if (autoSummary) autoSummary.textContent = label;
    };

    // テーブルの日時ソート用パース
    const parseDateValue = (value) => {
        const normalized = value.replace(/\//g, '-');
        const parsed = new Date(normalized);
        if (Number.isNaN(parsed.getTime())) {
            return new Date(0);
        }
        return parsed;
    };

    // ステータス/日付フィルタを適用
    const applyTableFilter = () => {
        if (!tableBody) return;
        const statusValue = statusFilter?.value ?? 'all';
        const rows = Array.from(tableBody.querySelectorAll('tr'));

        rows.forEach((row) => {
            const rowStatus = row.dataset.status;
            const shouldShow = statusValue === 'all' || statusValue === rowStatus;
            row.classList.toggle('is-hidden', !shouldShow);
            row.style.display = shouldShow ? '' : 'none';
        });

        const order = dateFilter?.value ?? 'desc';
        const visibleRows = rows.filter((row) => row.style.display !== 'none');
        visibleRows
            .sort((a, b) => {
                const aDate = parseDateValue(a.dataset.created || '');
                const bDate = parseDateValue(b.dataset.created || '');
                return order === 'asc' ? aDate - bDate : bDate - aDate;
            })
            .forEach((row) => tableBody.appendChild(row));
    };

    // 最新バックアップ行をテーブル先頭に追加
    const appendBackupRow = (item) => {
        if (!tableBody || !item) return;
        const emptyRow = tableBody.querySelector('[data-backup-empty]');
        if (emptyRow) {
            emptyRow.remove();
        }
        const row = document.createElement('tr');
        row.dataset.status = item.status_key || 'failed';
        row.dataset.created = item.created_at || '';
        if (item.id) {
            row.dataset.backupId = item.id;
        }
        const actionButtons =
            item.status_key === 'success'
                ? `
            <span class="admin-backup-actions">
                <button type="button" class="admin-button-link" data-backup-action="download" data-backup-url="${
                    item.download_url || ''
                }">ダウンロード</button>
                <button type="button" class="admin-button-link" data-backup-action="delete" data-backup-url="${
                    item.delete_url || ''
                }">削除</button>
            </span>
        `
                : `
            <button type="button" class="admin-button-link" data-backup-action="retry" data-backup-url="${
                item.retry_url || ''
            }">再実行</button>
        `;
        row.innerHTML = `
            <td>${item.created_at || '-'}</td>
            <td>${item.type_label || '-'}</td>
            <td><span class="admin-backup-status admin-backup-status--${item.status_key || 'failed'}">${
            item.status_label || '失敗'
        }</span></td>
            <td class="admin-backup-file">${item.file_name || '-'}</td>
            <td>${item.size_label || '-'}</td>
            <td>${actionButtons}</td>
        `;
        tableBody.prepend(row);
    };

    // ポーリングでバックアップ一覧を更新（非同期バックアップ完了を検知）
    const pollBackupList = (listUrl, initialLatest, maxAttempts = 24) => {
        let attempts = 0;
        const interval = setInterval(async () => {
            attempts += 1;
            if (attempts > maxAttempts) {
                clearInterval(interval);
                return;
            }
            try {
                const res = await fetch(listUrl, { headers: { Accept: 'application/json' } });
                const json = await res.json();
                if (!json.backupItems?.length) return;
                const newest = json.backupItems[0];
                const newestDate = newest?.created_at || '';
                if (newestDate !== initialLatest) {
                    clearInterval(interval);
                    if (lastUpdated) lastUpdated.textContent = json.latestBackupAt || newestDate;
                    if (latestBackup) latestBackup.textContent = json.latestBackupAt || newestDate;
                    appendBackupRow(newest);
                    showToast('バックアップが完了しました');
                }
            } catch {
                // ポーリング失敗時は無視
            }
        }, 5000);
    };

    // 手動バックアップ実行ボタン
    if (manualButton) {
        manualButton.addEventListener('click', async () => {
            const url = manualButton.dataset.backupManualUrl;
            const listUrl = manualButton.dataset.backupListUrl;
            if (!url) return;
            manualButton.disabled = true;
            showToast('バックアップを開始しました');
            const initialLatest = latestBackup?.textContent?.trim() || '';
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        Accept: 'application/json',
                    },
                });
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data?.message || 'バックアップに失敗しました');
                }
                if (data.async && listUrl) {
                    showToast(data.message || 'バックアップを開始しました。完了までお待ちください。');
                    pollBackupList(listUrl, initialLatest);
                } else {
                    const formatted = data.latest_at || formatDate(new Date());
                    if (lastUpdated) lastUpdated.textContent = formatted;
                    if (latestBackup) latestBackup.textContent = formatted;
                    if (data.item) appendBackupRow(data.item);
                    showToast(data.message || 'バックアップを作成しました');
                }
            } catch (error) {
                showToast(error.message || 'バックアップに失敗しました', 'error');
            } finally {
                manualButton.disabled = false;
            }
        });
    }

    // 自動設定の切替
    toggleInput?.addEventListener('change', syncAutoState);
    timeSelect?.addEventListener('change', syncAutoState);
    saveButton?.addEventListener('click', async () => {
        const url = saveButton.dataset.backupSettingUrl;
        if (!url || !toggleInput || !timeSelect) return;
        try {
            const response = await fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    Accept: 'application/json',
                },
                body: JSON.stringify({
                    is_enabled: toggleInput.checked ? 1 : 0,
                    run_time: timeSelect.value,
                }),
            });
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data?.message || '設定の保存に失敗しました');
            }
            showToast(data.message || '自動バックアップ設定を保存しました');
        } catch (error) {
            showToast(error.message || '設定の保存に失敗しました', 'error');
        }
    });

    // フィルタ変更
    statusFilter?.addEventListener('change', applyTableFilter);
    dateFilter?.addEventListener('change', applyTableFilter);

    // 行のアクション（ダウンロード/削除/再実行）
    tableBody?.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-backup-action]');
        if (!button) return;
        const action = button.dataset.backupAction;
        const url = button.dataset.backupUrl;
        if (!action || !url) return;

        if (action === 'download') {
            window.location.href = url;
            return;
        }

        button.disabled = true;

        try {
            if (action === 'delete') {
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        Accept: 'application/json',
                    },
                });
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data?.message || '削除に失敗しました');
                }
                const row = button.closest('tr');
                row?.remove();
                if (tableBody && tableBody.querySelectorAll('tr').length === 0) {
                    const emptyRow = document.createElement('tr');
                    emptyRow.dataset.backupEmpty = 'true';
                    emptyRow.innerHTML =
                        '<td colspan="6" class="text-center text-gray-500 py-6">バックアップ履歴がありません。</td>';
                    tableBody.appendChild(emptyRow);
                }
                showToast(data.message || 'バックアップを削除しました');
            }

            if (action === 'retry') {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        Accept: 'application/json',
                    },
                });
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data?.message || '再実行に失敗しました');
                }
                if (data.async) {
                    const listUrl = tableBody?.closest('[data-backup-list-url]')?.dataset?.backupListUrl;
                    const initialLatest = latestBackup?.textContent?.trim() || '';
                    if (listUrl) {
                        showToast(data.message || 'バックアップを再実行しました。完了までお待ちください。');
                        pollBackupList(listUrl, initialLatest);
                    } else {
                        showToast(data.message || 'バックアップを再実行しました。ページを更新してご確認ください。');
                    }
                } else {
                    const formatted = data.latest_at || formatDate(new Date());
                    if (lastUpdated) lastUpdated.textContent = formatted;
                    if (latestBackup) latestBackup.textContent = formatted;
                    appendBackupRow(data.item);
                    showToast(data.message || 'バックアップを作成しました');
                }
            }
        } catch (error) {
            showToast(error.message || '操作に失敗しました', 'error');
        } finally {
            button.disabled = false;
        }
    });

    // 初期状態の反映
    syncAutoState();
    applyTableFilter();
};

// DOM読み込み後に初期化
document.addEventListener('DOMContentLoaded', initAdminBackups);
