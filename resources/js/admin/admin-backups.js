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

    const formatDate = (date) => {
        const pad = (value) => String(value).padStart(2, '0');
        return `${date.getFullYear()}/${pad(date.getMonth() + 1)}/${pad(date.getDate())} ${pad(
            date.getHours(),
        )}:${pad(date.getMinutes())}`;
    };

    const getCsrfToken = () =>
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const showToast = (message, status = 'success') => {
        if (!toast || !toastLabel) return;
        toastLabel.textContent = message;
        toast.classList.toggle('is-error', status === 'error');
        toast.classList.add('is-visible');
        window.setTimeout(() => toast.classList.remove('is-visible'), 3500);
    };

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

    const parseDateValue = (value) => {
        const normalized = value.replace(/\//g, '-');
        const parsed = new Date(normalized);
        if (Number.isNaN(parsed.getTime())) {
            return new Date(0);
        }
        return parsed;
    };

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

    const appendBackupRow = (item) => {
        if (!tableBody || !item) return;
        const emptyRow = tableBody.querySelector('[data-backup-empty]');
        if (emptyRow) {
            emptyRow.remove();
        }
        const row = document.createElement('tr');
        row.dataset.status = item.status_key || 'failed';
        row.dataset.created = item.created_at || '';
        row.innerHTML = `
            <td>${item.created_at || '-'}</td>
            <td>${item.type_label || '-'}</td>
            <td><span class="admin-backup-status admin-backup-status--${item.status_key || 'failed'}">${
            item.status_label || '失敗'
        }</span></td>
            <td class="admin-backup-file">${item.file_name || '-'}</td>
            <td>${item.size_label || '-'}</td>
            <td><button type="button" class="admin-button-link" data-backup-action="download">削除</button></td>
        `;
        tableBody.prepend(row);
    };

    if (manualButton) {
        manualButton.addEventListener('click', async () => {
            const url = manualButton.dataset.backupManualUrl;
            if (!url) return;
            manualButton.disabled = true;
            showToast('バックアップを開始しました');
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
                const formatted = data.latest_at || formatDate(new Date());
                if (lastUpdated) lastUpdated.textContent = formatted;
                if (latestBackup) latestBackup.textContent = formatted;
                appendBackupRow(data.item);
                showToast(data.message || 'バックアップを作成しました');
            } catch (error) {
                showToast(error.message || 'バックアップに失敗しました', 'error');
            } finally {
                manualButton.disabled = false;
            }
        });
    }

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

    statusFilter?.addEventListener('change', applyTableFilter);
    dateFilter?.addEventListener('change', applyTableFilter);

    syncAutoState();
    applyTableFilter();
};

document.addEventListener('DOMContentLoaded', initAdminBackups);
