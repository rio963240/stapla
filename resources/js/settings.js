const initSettingsToast = () => {
    const toast = document.getElementById('settings-toast');
    if (!toast) return;

    const label = toast.querySelector('.settings-toast-label');
    let timer = null;

    const showToast = (status, message) => {
        if (label) {
            label.textContent = message;
        }
        toast.classList.toggle('is-error', status === 'error');
        toast.classList.add('is-visible');

        if (timer) {
            window.clearTimeout(timer);
        }
        timer = window.setTimeout(() => {
            toast.classList.remove('is-visible');
        }, 3000);
    };

    const shouldAutoShow =
        toast.dataset.toastAutoshow === 'true' || toast.classList.contains('is-visible');
    if (shouldAutoShow) {
        const status = toast.dataset.toastStatus || 'success';
        const message = toast.dataset.toastMessage || '保存しました';
        showToast(status, message);
    }

    document.querySelectorAll('button[data-toast-message]').forEach((button) => {
        button.addEventListener('click', () => {
            const status = button.dataset.toastStatus || 'success';
            const message = button.dataset.toastMessage || '保存しました';
            showToast(status, message);
        });
    });
};

const initNotifyTimeToggles = () => {
    const toggles = document.querySelectorAll('[data-time-toggle]');
    if (!toggles.length) return;

    const syncBlock = (toggle) => {
        const key = toggle.dataset.timeToggle;
        const block = document.querySelector(`[data-time-block="${key}"]`);
        if (!block) return;
        block.classList.toggle('is-hidden', !toggle.checked);
    };

    toggles.forEach((toggle) => {
        syncBlock(toggle);
        toggle.addEventListener('change', () => syncBlock(toggle));
    });
};

const initBasicInfoForm = () => {
    const form = document.querySelector('form[action$="settings/basic"]');
    if (!form) return;

    const submitButton = form.querySelector('button[type="submit"]');
    const nameInput = form.querySelector('input[name="name"]');
    const photoInput = form.querySelector('input[name="photo"]');
    const photoTrigger = form.querySelector('[data-settings-photo-trigger]');
    const avatarImage = form.querySelector('[data-settings-avatar]');
    let previewUrl = null;
    const showToast = (status, message) => {
        const toast = document.getElementById('settings-toast');
        if (!toast) return;
        const label = toast.querySelector('.settings-toast-label');
        if (label) {
            label.textContent = message;
        }
        toast.classList.toggle('is-error', status === 'error');
        toast.classList.add('is-visible');

        window.setTimeout(() => {
            toast.classList.remove('is-visible');
        }, 3000);
    };

    if (photoTrigger && photoInput) {
        photoTrigger.addEventListener('click', () => {
            photoInput.click();
        });
    }
    if (photoInput && avatarImage) {
        photoInput.addEventListener('change', () => {
            const file = photoInput.files?.[0];
            if (!file) return;
            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
            }
            previewUrl = URL.createObjectURL(file);
            avatarImage.src = previewUrl;
        });
    }

    form.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') return;
        const target = event.target;
        if (target instanceof HTMLInputElement) {
            event.preventDefault();
        }
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!nameInput) return;

        const originalText = submitButton?.textContent;
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = '保存中...';
        }

        try {
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = tokenMeta?.getAttribute('content') || '';
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'application/json',
                },
                body: formData,
            });

            if (!response.ok) {
                throw new Error('保存に失敗しました');
            }
            const data = await response.json();
            if (data?.name && nameInput) {
                nameInput.value = data.name;
            }
            if (data?.photo_url && avatarImage) {
                avatarImage.src = data.photo_url;
            }
            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
                previewUrl = null;
            }
            if (photoInput) {
                photoInput.value = '';
            }
            showToast('success', '基本情報を保存しました');
        } catch (error) {
            showToast('error', '保存に失敗しました');
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = originalText || '保存';
            }
        }
    });
};

const onReady = (callback) => {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback);
    } else {
        callback();
    }
};

onReady(() => {
    initSettingsToast();
    initNotifyTimeToggles();
    initBasicInfoForm();
});
