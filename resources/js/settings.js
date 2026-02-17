// 設定画面用トーストの表示制御
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

// 通知ON/OFFに応じた時間ブロックの表示切替
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

// 基本情報フォームのAjax保存・アバター即時プレビュー
const initBasicInfoForm = () => {
    const form = document.querySelector('form[action$="settings/basic"]');
    if (!form) return;

    const submitButton = form.querySelector('button[type="submit"]');
    const nameInput = form.querySelector('input[name="name"]');
    const photoInput = form.querySelector('input[name="photo"]');
    const photoTrigger = form.querySelector('[data-settings-photo-trigger]');
    const avatarImage = form.querySelector('[data-settings-avatar]');
    let previewUrl = null;
    let originalAvatarUrl = avatarImage?.getAttribute('src') || null;
    // フォーム内操作からの簡易トースト
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

    // Enter送信での誤送信を防止
    form.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') return;
        const target = event.target;
        if (target instanceof HTMLInputElement) {
            event.preventDefault();
        }
    });

    // 基本情報をAjaxで保存
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
                originalAvatarUrl = data.photo_url;
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
            if (avatarImage && originalAvatarUrl) {
                avatarImage.src = originalAvatarUrl;
            }
            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
                previewUrl = null;
            }
            if (photoInput) {
                photoInput.value = '';
            }
            showToast('error', '保存に失敗しました');
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = originalText || '保存';
            }
        }
    });
};

// パスワード変更フォームのAjax保存
const initPasswordForm = () => {
    const form = document.querySelector('form[action$="settings/password"]');
    if (!form) return;

    const submitButton = form.querySelector('button[type="submit"]');
    const currentInput = form.querySelector('input[name="current_password"]');
    const newInput = form.querySelector('input[name="password"]');
    const confirmInput = form.querySelector('input[name="password_confirmation"]');
    // フォーム内操作からの簡易トースト
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

    // Enter送信での誤送信を防止
    form.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') return;
        const target = event.target;
        if (target instanceof HTMLInputElement) {
            event.preventDefault();
        }
    });

    // パスワードをAjaxで保存
    form.addEventListener('submit', async (event) => {
        event.preventDefault();

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

            if (currentInput) currentInput.value = '';
            if (newInput) newInput.value = '';
            if (confirmInput) confirmInput.value = '';
            showToast('success', 'パスワードを変更しました');
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

// アカウント削除モーダルの開閉と削除実行（二段階確認）
const initDeleteAccountModal = () => {
    const openButton = document.querySelector('[data-delete-modal-open]');
    const confirmModal = document.querySelector('[data-delete-confirm-modal]');
    const modal = document.querySelector('[data-delete-modal]');
    if (!openButton || !modal) return;

    const closeButtons = modal.querySelectorAll('[data-delete-modal-close]');
    const form = modal.querySelector('[data-delete-form]');
    const passwordInput = modal.querySelector('[data-delete-password]');
    const errorLabel = modal.querySelector('[data-delete-error]');
    const submitButton = form?.querySelector('button[type="submit"]');

    // モーダルの入力状態を初期化
    const resetForm = () => {
        if (form) form.reset();
        if (errorLabel) errorLabel.textContent = '';
    };

    // 一段階目: 確認モーダルを表示
    const openConfirmModal = () => {
        if (confirmModal) confirmModal.classList.remove('is-hidden');
    };

    const closeConfirmModal = () => {
        if (confirmModal) confirmModal.classList.add('is-hidden');
    };

    // 二段階目: パスワード入力モーダルを表示
    const openModal = () => {
        closeConfirmModal();
        modal.classList.remove('is-hidden');
        resetForm();
        if (passwordInput) {
            passwordInput.focus();
        }
    };

    const closeModal = () => {
        modal.classList.add('is-hidden');
        resetForm();
    };

    // 「アカウントを削除」クリック → 一段階目の確認モーダルを表示
    openButton.addEventListener('click', openConfirmModal);

    // 一段階目: キャンセルで閉じる
    if (confirmModal) {
        confirmModal.querySelectorAll('[data-delete-confirm-close]').forEach((el) => {
            el.addEventListener('click', closeConfirmModal);
        });
        const proceedButton = confirmModal.querySelector('[data-delete-confirm-proceed]');
        if (proceedButton) {
            proceedButton.addEventListener('click', openModal);
        }
    }

    closeButtons.forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        if (confirmModal && !confirmModal.classList.contains('is-hidden')) {
            closeConfirmModal();
            return;
        }
        if (modal.classList.contains('is-hidden')) return;
        closeModal();
    });

    if (!form) return;

    // アカウント削除をAjaxで実行
    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const originalText = submitButton?.textContent;
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = '削除中...';
        }
        if (errorLabel) errorLabel.textContent = '';

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
                let message = '削除に失敗しました';
                // バリデーション（422）の場合はエラーメッセージを表示
                if (response.status === 422) {
                    const data = await response.json();
                    message = data?.errors?.password?.[0] || 'パスワードが正しくありません';
                }
                if (errorLabel) errorLabel.textContent = message;
                return;
            }

            // 成功時はログイン画面へリダイレクト
            const data = await response.json();
            if (data?.redirect) {
                window.location.href = data.redirect;
            } else {
                window.location.reload();
            }
        } catch (error) {
            if (errorLabel) errorLabel.textContent = '削除に失敗しました';
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = originalText || '削除する';
            }
        }
    });
};

// プロフィールメニューの開閉（サイドバー内のアイコン）
const initProfileMenu = () => {
    const triggers = document.querySelectorAll('.profile-menu-trigger');
    triggers.forEach((trigger) => {
        const menu = trigger.parentElement?.querySelector('.profile-menu');
        if (!menu) return;

        const closeMenu = () => {
            menu.classList.add('hidden');
            trigger.setAttribute('aria-expanded', 'false');
        };

        const openMenu = () => {
            menu.classList.remove('hidden');
            trigger.setAttribute('aria-expanded', 'true');
        };

        trigger.addEventListener('click', (event) => {
            event.stopPropagation();
            if (menu.classList.contains('hidden')) {
                openMenu();
            } else {
                closeMenu();
            }
        });

        document.addEventListener('click', (event) => {
            if (!menu.contains(event.target) && !trigger.contains(event.target)) {
                closeMenu();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeMenu();
            }
        });
    });
};

// DOM準備完了で初期化を実行
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
    initPasswordForm();
    initDeleteAccountModal();
    initProfileMenu();
});
