// 管理画面（資格/分野/サブ分野）モーダルの初期化
const initAdminQualifications = () => {
    const modal = document.querySelector('[data-admin-modal]');
    const deleteModal = document.querySelector('[data-admin-delete-modal]');
    const toast = document.getElementById('admin-toast');

    const csvForm = document.querySelector('[data-admin-csv-form]');
    const csvInput = document.querySelector('[data-admin-csv-input]');
    const csvTrigger = document.querySelector('[data-admin-csv-trigger]');

    // CSVアップロードのファイル選択と自動送信
    if (csvForm && csvInput && csvTrigger) {
        csvTrigger.addEventListener('click', () => {
            const message =
                'CSVのヘッダーは "qualification_name,domain_name,subdomain_name" が必須です。続けますか？';
            if (window.confirm(message)) {
                csvInput.click();
            }
        });

        csvInput.addEventListener('change', () => {
            if (csvInput.files && csvInput.files.length > 0) {
                csvForm.submit();
            }
        });
    }

    if (!modal || !deleteModal) return;

    // 編集/削除モーダル内の要素取得
    const editPanel = modal.querySelector('[data-admin-edit-panel]');
    const confirmPanel = modal.querySelector('[data-admin-confirm-panel]');
    const editForm = modal.querySelector('[data-admin-edit-form]');
    const deleteForm = deleteModal.querySelector('[data-admin-delete-form]');
    const modalTitle = modal.querySelector('[data-admin-modal-title]');
    const nameLabel = modal.querySelector('[data-admin-name-label]');
    const nameInput = modal.querySelector('[data-admin-name-input]');
    const qualificationRow = modal.querySelector('[data-admin-qualification-row]');
    const qualificationInput = modal.querySelector('[data-admin-qualification-input]');
    const domainRow = modal.querySelector('[data-admin-domain-row]');
    const domainInput = modal.querySelector('[data-admin-domain-input]');

    const confirmNameLabel = modal.querySelector('[data-admin-confirm-name-label]');
    const confirmNameInput = modal.querySelector('[data-admin-confirm-name]');
    const confirmQualificationRow = modal.querySelector('[data-admin-confirm-qualification-row]');
    const confirmQualificationInput = modal.querySelector('[data-admin-confirm-qualification]');
    const confirmDomainRow = modal.querySelector('[data-admin-confirm-domain-row]');
    const confirmDomainInput = modal.querySelector('[data-admin-confirm-domain]');

    const deleteNameLabel = deleteModal.querySelector('[data-admin-delete-name-label]');
    const deleteNameInput = deleteModal.querySelector('[data-admin-delete-name]');
    const deleteQualificationRow = deleteModal.querySelector('[data-admin-delete-qualification-row]');
    const deleteQualificationInput = deleteModal.querySelector('[data-admin-delete-qualification]');
    const deleteDomainRow = deleteModal.querySelector('[data-admin-delete-domain-row]');
    const deleteDomainInput = deleteModal.querySelector('[data-admin-delete-domain]');

    const confirmOpenButton = modal.querySelector('[data-admin-confirm-open]');
    const confirmBackButton = modal.querySelector('[data-admin-confirm-back]');
    const submitButton = modal.querySelector('[data-admin-submit]');
    const deleteOpenButton = modal.querySelector('[data-admin-delete-open]');

    const createModal = document.querySelector('[data-admin-create-modal]');
    const createOpenButton = document.querySelector('[data-admin-create-open]');
    const createForm = createModal?.querySelector('[data-admin-create-form]');
    const createEditPanel = createModal?.querySelector('[data-admin-create-edit-panel]');
    const createConfirmPanel = createModal?.querySelector('[data-admin-create-confirm-panel]');
    const createNameInput = createModal?.querySelector('[data-admin-create-name-input]');
    const createQualificationInput = createModal?.querySelector('[data-admin-create-qualification]');
    const createDomainInput = createModal?.querySelector('[data-admin-create-domain]');
    const createConfirmNameInput = createModal?.querySelector('[data-admin-create-confirm-name]');
    const createConfirmQualificationInput = createModal?.querySelector('[data-admin-create-confirm-qualification]');
    const createConfirmDomainInput = createModal?.querySelector('[data-admin-create-confirm-domain]');
    const createConfirmOpenButton = createModal?.querySelector('[data-admin-create-confirm-open]');
    const createConfirmBackButton = createModal?.querySelector('[data-admin-create-confirm-back]');

    // 編集モーダルを閉じて初期状態に戻す
    const closeModal = () => {
        modal.classList.add('hidden');
        editPanel.classList.remove('hidden');
        confirmPanel.classList.add('hidden');
    };

    // 削除モーダルを閉じる
    const closeDeleteModal = () => {
        deleteModal.classList.add('hidden');
    };

    // 行の表示/非表示を制御
    const setRowVisibility = (row, isVisible) => {
        row.classList.toggle('hidden', !isVisible);
        row.style.display = isVisible ? '' : 'none';
    };

    // 入力値を安全にセット
    const setValue = (input, value) => {
        if (!input) return;
        input.value = value || '';
    };

    // 新規作成モーダルのイベント
    if (createModal && createOpenButton && createForm && createEditPanel && createConfirmPanel) {
        const closeCreateModal = () => {
            createModal.classList.add('hidden');
            createEditPanel.classList.remove('hidden');
            createConfirmPanel.classList.add('hidden');
        };

        // 新規作成モーダルを開く
        createOpenButton.addEventListener('click', () => {
            createModal.classList.remove('hidden');
            createEditPanel.classList.remove('hidden');
            createConfirmPanel.classList.add('hidden');
        });

        // 入力内容を確認画面に反映
        createConfirmOpenButton?.addEventListener('click', () => {
            setValue(createConfirmNameInput, createNameInput?.value);
            setValue(createConfirmQualificationInput, createQualificationInput?.value);
            setValue(createConfirmDomainInput, createDomainInput?.value);
            createEditPanel.classList.add('hidden');
            createConfirmPanel.classList.remove('hidden');
        });

        // 確認画面から入力画面へ戻る
        createConfirmBackButton?.addEventListener('click', () => {
            createConfirmPanel.classList.add('hidden');
            createEditPanel.classList.remove('hidden');
        });

        // 新規作成モーダルを閉じる
        createModal.querySelectorAll('[data-admin-create-close]').forEach((button) => {
            button.addEventListener('click', closeCreateModal);
        });
    }

    // 対象種別に応じたラベル更新
    const setLabels = (type) => {
        const labelMap = {
            qualification: '資格名',
            domain: '分野名',
            subdomain: 'サブ分野名',
        };
        const label = labelMap[type] || '名称';
        nameLabel.textContent = label;
        confirmNameLabel.textContent = label;
        deleteNameLabel.textContent = label;
    };

    // タイトルの種別プレフィックス
    const setTitlePrefix = (type) => {
        if (type === 'qualification') return '資格';
        if (type === 'domain') return '分野';
        if (type === 'subdomain') return 'サブ分野';
        return '';
    };

    // 編集モーダルをデータで開く
    const openEditModal = (data) => {
        const type = data.type;
        const name = data.name || '';
        const qualificationName = data.qualificationName || '';
        const domainName = data.domainName || '';
        const updateUrl = data.updateUrl || '';
        const deleteUrl = data.deleteUrl || '';

        editForm.action = updateUrl;
        deleteForm.action = deleteUrl;
        modalTitle.textContent = `${setTitlePrefix(type)}編集`;
        setLabels(type);

        setValue(nameInput, name);
        setValue(qualificationInput, qualificationName);
        setValue(domainInput, domainName);
        setValue(confirmNameInput, name);
        setValue(confirmQualificationInput, qualificationName);
        setValue(confirmDomainInput, domainName);
        setValue(deleteNameInput, name);
        setValue(deleteQualificationInput, qualificationName);
        setValue(deleteDomainInput, domainName);

        setRowVisibility(qualificationRow, type !== 'qualification');
        setRowVisibility(domainRow, type === 'subdomain');
        setRowVisibility(confirmQualificationRow, type !== 'qualification');
        setRowVisibility(confirmDomainRow, type === 'subdomain');
        setRowVisibility(deleteQualificationRow, type !== 'qualification');
        setRowVisibility(deleteDomainRow, type === 'subdomain');

        modal.classList.remove('hidden');
    };

    // 一覧の編集ボタンからモーダルを起動
    document.querySelectorAll('.js-admin-edit-trigger').forEach((button) => {
        button.addEventListener('click', () => {
            openEditModal({
                type: button.dataset.type,
                name: button.dataset.name,
                qualificationName: button.dataset.qualificationName,
                domainName: button.dataset.domainName,
                updateUrl: button.dataset.updateUrl,
                deleteUrl: button.dataset.deleteUrl,
            });
        });
    });

    // 編集内容の確認画面を開く
    confirmOpenButton.addEventListener('click', () => {
        setValue(confirmNameInput, nameInput.value);
        editPanel.classList.add('hidden');
        confirmPanel.classList.remove('hidden');
    });

    // 確認画面から入力画面へ戻る
    confirmBackButton.addEventListener('click', () => {
        confirmPanel.classList.add('hidden');
        editPanel.classList.remove('hidden');
    });

    // 編集フォーム送信
    submitButton.addEventListener('click', () => {
        editForm.submit();
    });

    // 削除モーダルを開く
    deleteOpenButton.addEventListener('click', () => {
        deleteModal.classList.remove('hidden');
    });

    // 編集モーダルの閉じるボタン
    modal.querySelectorAll('[data-admin-modal-close]').forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    // 削除モーダルの閉じるボタン
    deleteModal.querySelectorAll('[data-admin-delete-close]').forEach((button) => {
        button.addEventListener('click', closeDeleteModal);
    });

    // 完了/エラートーストの表示
    if (toast) {
        const label = toast.querySelector('.admin-toast-label');
        const showToast = (status, message) => {
            label.textContent = message;
            toast.classList.toggle('is-error', status === 'error');
            toast.classList.add('is-visible');
            window.setTimeout(() => {
                toast.classList.remove('is-visible');
            }, 4000);
        };

        if (toast.dataset.toastAutoshow === 'true') {
            showToast(toast.dataset.toastStatus || 'success', toast.dataset.toastMessage || '完了しました');
        }
    }
};

// 初期化実行
document.addEventListener('DOMContentLoaded', initAdminQualifications);
