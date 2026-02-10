const initAdminUsers = () => {
    const listEl = document.getElementById('admin-users-list');
    const editEl = document.getElementById('admin-users-edit');
    const confirmEl = document.getElementById('admin-users-confirm');
    const editForm = document.querySelector('[data-admin-users-edit-form]');
    const toast = document.querySelector('[data-admin-users-toast]');
    const toastLabel = toast?.querySelector('.admin-toast-label');

    const showView = (view) => {
        listEl?.classList.toggle('is-hidden', view !== 'list');
        editEl?.classList.toggle('is-hidden', view !== 'edit');
        confirmEl?.classList.toggle('is-hidden', view !== 'confirm');
    };

    const showToast = (message, status = 'success') => {
        if (!toast || !toastLabel) return;
        toastLabel.textContent = message;
        toast.classList.toggle('is-error', status === 'error');
        toast.classList.add('is-visible');
        window.setTimeout(() => toast.classList.remove('is-visible'), 3500);
    };

    const getCsrfToken = () =>
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // 編集ボタン
    document.querySelectorAll('[data-admin-users-edit]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const row = btn.closest('tr');
            if (!row?.dataset?.userId) return;

            const userId = row.dataset.userId;
            const name = row.dataset.userName || '';
            const email = row.dataset.userEmail || '';
            const role = row.dataset.userRole || 'general';
            const status = row.dataset.userStatus || 'active';

            document.querySelector('[data-admin-edit-name]').textContent = name;
            document.querySelector('[data-admin-edit-email]').textContent = email;
            document.querySelector('[data-admin-edit-user-id]').value = userId;
            document.querySelector('[data-admin-edit-role]').value = role === 'admin' ? '1' : '0';
            document.querySelector('[data-admin-edit-status]').value = status === 'active' ? '1' : '0';
            document.querySelector('[data-admin-edit-password]').value = '';

            showView('edit');
        });
    });

    // キャンセル
    document.querySelector('[data-admin-users-cancel]')?.addEventListener('click', () => {
        showView('list');
    });

    // 確認
    editForm?.addEventListener('submit', (e) => {
        e.preventDefault();
        const name = document.querySelector('[data-admin-edit-name]').textContent;
        const email = document.querySelector('[data-admin-edit-email]').textContent;
        const roleSelect = document.querySelector('[data-admin-edit-role]');
        const statusSelect = document.querySelector('[data-admin-edit-status]');
        const passwordInput = document.querySelector('[data-admin-edit-password]');

        const roleLabel = roleSelect?.selectedOptions?.[0]?.text ?? '';
        const statusLabel = statusSelect?.selectedOptions?.[0]?.text ?? '';
        const passwordVal = passwordInput?.value?.trim() ?? '';
        const passwordDisplay = passwordVal ? '○○○' : '（変更なし）';

        document.querySelector('[data-admin-confirm-name]').textContent = name;
        document.querySelector('[data-admin-confirm-email]').textContent = email;
        document.querySelector('[data-admin-confirm-role]').textContent = roleLabel;
        document.querySelector('[data-admin-confirm-status]').textContent = statusLabel;
        document.querySelector('[data-admin-confirm-password]').textContent = passwordDisplay;

        confirmEl.dataset.pendingUserId = document.querySelector('[data-admin-edit-user-id]').value;
        confirmEl.dataset.pendingData = JSON.stringify({
            is_admin: roleSelect?.value === '1',
            is_active: statusSelect?.value === '1',
            password: passwordVal,
        });

        showView('confirm');
    });

    // 戻る
    document.querySelector('[data-admin-users-back]')?.addEventListener('click', () => {
        showView('edit');
    });

    // 更新
    document.querySelector('[data-admin-users-update]')?.addEventListener('click', async () => {
        const userId = confirmEl?.dataset?.pendingUserId;
        const pendingJson = confirmEl?.dataset?.pendingData;
        if (!userId || !pendingJson) return;

        const data = JSON.parse(pendingJson);
        const baseUrl = window.ADMIN_USERS_UPDATE_URL || '/admin/users';
        const url = `${baseUrl}/${userId}`;
        const updateBtn = document.querySelector('[data-admin-users-update]');
        const originalText = updateBtn?.textContent;

        if (updateBtn) {
            updateBtn.disabled = true;
            updateBtn.textContent = '更新中...';
        }

        try {
            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('_token', getCsrfToken());
            formData.append('is_admin', data.is_admin ? '1' : '0');
            formData.append('is_active', data.is_active ? '1' : '0');
            if (data.password) {
                formData.append('password', data.password);
            }

            const res = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'application/json',
                },
            });

            const json = await res.json().catch(() => ({}));

            if (res.ok && json.status === 'success') {
                showToast(json.message || '更新しました');
                showView('list');
                window.location.reload();
            } else {
                showToast(json.message || '更新に失敗しました', 'error');
            }
        } catch (err) {
            showToast('更新に失敗しました', 'error');
        } finally {
            if (updateBtn) {
                updateBtn.disabled = false;
                updateBtn.textContent = originalText;
            }
        }
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAdminUsers);
} else {
    initAdminUsers();
}
