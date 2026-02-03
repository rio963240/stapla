// 計画登録モーダル群の制御
const initPlanRegisterModals = () => {
    const trigger = document.getElementById('plan-register-trigger');
    const choiceDomainButton = document.getElementById('plan-register-choice-domain');
    const choiceSubdomainButton = document.getElementById('plan-register-choice-subdomain');
    const modalIds = [
        'plan-register-choice-modal',
        'plan-register-domain-modal',
        'plan-register-subdomain-modal',
    ];

    const modals = modalIds
        .map((id) => document.getElementById(id))
        .filter(Boolean);

    // 全モーダルを閉じる
    const closeAll = () => {
        modals.forEach((modal) => modal.classList.add('hidden'));
        document.body.classList.remove('overflow-hidden');
    };

    // 対象モーダルのみ開く
    const openModal = (targetId) => {
        modals.forEach((modal) => {
            modal.classList.toggle('hidden', modal.id !== targetId);
        });
        document.body.classList.add('overflow-hidden');
    };

    // モーダルを開くトリガー
    trigger?.addEventListener('click', () => openModal('plan-register-choice-modal'));
    choiceDomainButton?.addEventListener('click', () => openModal('plan-register-domain-modal'));
    choiceSubdomainButton?.addEventListener('click', () =>
        openModal('plan-register-subdomain-modal'),
    );

    // 閉じるボタン
    document.querySelectorAll('[data-modal-close]').forEach((button) => {
        button.addEventListener('click', closeAll);
    });

    // 背景クリックで閉じる
    modals.forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeAll();
            }
        });
    });
};

// 「勉強不可日」チップの追加・削除
const initNoStudyChips = () => {
    document.querySelectorAll('[data-no-study-add]').forEach((button) => {
        const key = button.dataset.noStudyAdd;
        const input = document.querySelector(`[data-no-study-input="${key}"]`);
        const list = document.querySelector(`[data-no-study-list="${key}"]`);
        if (!input || !list) return;

        // 入力値からチップを生成
        const addChip = () => {
            const value = input.value;
            if (!value) return;
            // 同じ値の重複追加はしない
            if (list.querySelector(`[data-no-study-chip="${value}"]`)) {
                input.value = '';
                return;
            }

            const chip = document.createElement('div');
            chip.className = 'modal-chip';
            chip.dataset.noStudyChip = value;
            chip.innerHTML = `<span>${value}</span><button type="button" aria-label="削除">×</button>`;
            // チップの削除
            chip.querySelector('button').addEventListener('click', () => chip.remove());
            list.appendChild(chip);
            input.value = '';
        };

        // ボタン押下で追加
        button.addEventListener('click', addChip);
        // Enterキーで追加
        input.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                addChip();
            }
        });
    });
};

// 繰り返し入力行の追加・削除
const initRepeatRows = () => {
    document.querySelectorAll('[data-repeat-add]').forEach((button) => {
        const key = button.dataset.repeatAdd;
        const container = button.closest('[data-repeat-container]');
        if (!key || !container) return;

        // 行を複製して追加
        button.addEventListener('click', () => {
            const row = button.closest('[data-repeat-row]');
            if (!row) return;

            const clone = row.cloneNode(true);
            const addButton = clone.querySelector('[data-repeat-add]');
            if (addButton) {
                // 複製行は削除ボタンに変更
                addButton.removeAttribute('data-repeat-add');
                addButton.setAttribute('data-repeat-remove', key);
                addButton.setAttribute('aria-label', '入力欄を削除');
                addButton.textContent = '-';
            }

            // 値は初期化
            clone.querySelectorAll('input').forEach((input) => {
                input.value = '';
            });
            clone.querySelectorAll('select').forEach((select) => {
                select.selectedIndex = 0;
            });

            container.appendChild(clone);
        });
    });

    // 削除ボタンのイベントは委譲で処理
    document.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) return;
        if (!target.matches('[data-repeat-remove]')) return;
        const row = target.closest('[data-repeat-row]');
        if (row) row.remove();
    });
};

// 資格名に応じて分野を取得・反映
const initQualificationDomains = () => {
    const qualificationSelect = document.querySelector('[data-qualification-select="domain"]');
    if (!qualificationSelect) return;

    const getDomainSelects = () => document.querySelectorAll('[data-domain-select="domain"]');

    const buildOptions = (domains) => {
        const fragment = document.createDocumentFragment();
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = '選択してください';
        fragment.appendChild(placeholder);

        domains.forEach((domain) => {
            const option = document.createElement('option');
            option.value = String(domain.id);
            option.textContent = domain.name;
            fragment.appendChild(option);
        });

        return fragment;
    };

    const applyOptions = (domains) => {
        getDomainSelects().forEach((select) => {
            select.innerHTML = '';
            select.appendChild(buildOptions(domains));
        });
    };

    const fetchDomains = async (qualificationId) => {
        if (!qualificationId) {
            applyOptions([]);
            return;
        }

        try {
            const response = await fetch(`/qualifications/${qualificationId}/domains`, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });
            if (!response.ok) throw new Error('failed to fetch domains');
            const domains = await response.json();
            applyOptions(Array.isArray(domains) ? domains : []);
        } catch (error) {
            applyOptions([]);
        }
    };

    qualificationSelect.addEventListener('change', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLSelectElement)) return;
        fetchDomains(target.value);
    });
};

// 余裕確保の説明ポップオーバー
const initHelpPopovers = () => {
    const triggers = Array.from(document.querySelectorAll('[data-popover-target]'));
    if (!triggers.length) return;

    const popoverMap = new Map();
    triggers.forEach((trigger) => {
        const key = trigger.dataset.popoverTarget;
        if (!key) return;
        const popover = document.getElementById(`${key}-popover`);
        if (!popover) return;
        popoverMap.set(trigger, popover);
    });

    const closeAll = () => {
        popoverMap.forEach((popover, trigger) => {
            popover.classList.add('hidden');
            trigger.setAttribute('aria-expanded', 'false');
        });
    };

    popoverMap.forEach((popover, trigger) => {
        trigger.addEventListener('click', (event) => {
            event.stopPropagation();
            const isOpen = !popover.classList.contains('hidden');
            closeAll();
            if (!isOpen) {
                popover.classList.remove('hidden');
                trigger.setAttribute('aria-expanded', 'true');
            }
        });
    });

    document.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) return;
        for (const [trigger, popover] of popoverMap.entries()) {
            if (trigger.contains(target) || popover.contains(target)) {
                return;
            }
        }
        closeAll();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeAll();
    });
};

export const initPlanRegister = () => {
    initPlanRegisterModals();
    initNoStudyChips();
    initRepeatRows();
    initQualificationDomains();
    initHelpPopovers();
};
