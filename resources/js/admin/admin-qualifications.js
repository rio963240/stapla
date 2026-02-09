const initAdminQualifications = () => {
    const modal = document.querySelector('[data-admin-modal]');
    const deleteModal = document.querySelector('[data-admin-delete-modal]');
    const toast = document.getElementById('admin-toast');

    if (!modal || !deleteModal) return;

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

    const closeModal = () => {
        modal.classList.add('hidden');
        editPanel.classList.remove('hidden');
        confirmPanel.classList.add('hidden');
    };

    const closeDeleteModal = () => {
        deleteModal.classList.add('hidden');
    };

    const setRowVisibility = (row, isVisible) => {
        row.classList.toggle('hidden', !isVisible);
    };

    const setValue = (input, value) => {
        input.value = value || '';
    };

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

    const setTitlePrefix = (type) => {
        if (type === 'qualification') return '資格';
        if (type === 'domain') return '分野';
        if (type === 'subdomain') return 'サブ分野';
        return '';
    };

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

    confirmOpenButton.addEventListener('click', () => {
        setValue(confirmNameInput, nameInput.value);
        editPanel.classList.add('hidden');
        confirmPanel.classList.remove('hidden');
    });

    confirmBackButton.addEventListener('click', () => {
        confirmPanel.classList.add('hidden');
        editPanel.classList.remove('hidden');
    });

    submitButton.addEventListener('click', () => {
        editForm.submit();
    });

    deleteOpenButton.addEventListener('click', () => {
        deleteModal.classList.remove('hidden');
    });

    modal.querySelectorAll('[data-admin-modal-close]').forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    deleteModal.querySelectorAll('[data-admin-delete-close]').forEach((button) => {
        button.addEventListener('click', closeDeleteModal);
    });

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

document.addEventListener('DOMContentLoaded', initAdminQualifications);
