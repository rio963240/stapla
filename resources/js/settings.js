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

    document.querySelectorAll('[data-toast-message]').forEach((button) => {
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
});
