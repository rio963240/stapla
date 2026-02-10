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

// CSRFトークン取得（Laravel用）
const getCsrfToken = () => {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : '';
};

// 計画登録（分野単位）の送信
const initPlanRegisterSubmit = () => {
    const submitButton = document.querySelector('[data-plan-submit="domain"]');
    if (!submitButton) return;

    submitButton.addEventListener('click', async () => {
        const startInput = document.querySelector('[data-plan-start="domain"]');
        const examInput = document.querySelector('[data-plan-exam="domain"]');
        const qualificationSelect = document.querySelector('[data-qualification-select="domain"]');
        const dailyInput = document.querySelector('[data-daily-study="domain"]');
        const bufferInput = document.querySelector('[data-buffer-rate="domain"]');
        const noStudyList = document.querySelector('[data-no-study-list="domain"]');

        if (!startInput || !examInput || !qualificationSelect || !dailyInput || !bufferInput) return;

        // 入力された分野と重みを収集
        const domains = Array.from(
            document.querySelectorAll('#plan-register-domain-modal [data-repeat-row]'),
        )
            .map((row) => {
                const domainSelect = row.querySelector('[data-domain-select="domain"]');
                const weightInput = row.querySelector('[data-domain-weight="domain"]');
                if (!(domainSelect instanceof HTMLSelectElement)) return null;
                if (!(weightInput instanceof HTMLInputElement)) return null;
                const id = Number.parseInt(domainSelect.value, 10);
                const weight = Number.parseInt(weightInput.value, 10);
                if (!Number.isFinite(id) || !Number.isFinite(weight)) return null;
                return { id, weight };
            })
            .filter(Boolean);

        // 勉強不可日を収集
        const noStudyDays = noStudyList
            ? Array.from(noStudyList.querySelectorAll('[data-no-study-chip]'))
                  .map((chip) => chip.dataset.noStudyChip)
                  .filter(Boolean)
            : [];

        const qualificationId = Number.parseInt(qualificationSelect.value, 10);
        const dailyStudyTime = Number.parseInt(dailyInput.value, 10);
        const bufferRate = Number.parseInt(bufferInput.value, 10);

        // 最低限のフロントバリデーション
        const errors = [];
        if (!startInput.value) errors.push('勉強開始日を入力してください。');
        if (!examInput.value) errors.push('受験日を入力してください。');
        if (!Number.isFinite(qualificationId)) errors.push('資格を選択してください。');
        if (!Number.isFinite(dailyStudyTime) || dailyStudyTime <= 0) {
            errors.push('1日の学習時間を入力してください。');
        }
        if (!Number.isFinite(bufferRate) || bufferRate < 0 || bufferRate > 99) {
            errors.push('余裕確保率は0〜99で入力してください。');
        }
        if (domains.length === 0) errors.push('分野と重みを入力してください。');

        if (errors.length > 0) {
            alert(errors[0]);
            return;
        }

        // 送信ペイロード
        const payload = {
            start_date: startInput.value,
            exam_date: examInput.value,
            qualification_id: qualificationId,
            daily_study_time: dailyStudyTime,
            buffer_rate: bufferRate,
            domains,
            no_study_days: noStudyDays,
        };

        const originalLabel = submitButton.textContent;
        submitButton.setAttribute('disabled', 'true');
        submitButton.textContent = '送信中...';

        try {
            const response = await fetch('/plan-register/domain', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload),
            });

            if (response.ok) {
                alert('計画を生成しました。');
                window.location.reload();
                return;
            }

            const data = await response.json().catch(() => ({}));
            const message =
                (data?.errors && Object.values(data.errors).flat()[0]) ||
                data?.message ||
                '登録に失敗しました。';
            alert(message);
        } catch (error) {
            alert('通信に失敗しました。');
        } finally {
            submitButton.removeAttribute('disabled');
            submitButton.textContent = originalLabel;
        }
    });
};

export const initPlanRegister = () => {
    initPlanRegisterModals();
    initNoStudyChips();
    initRepeatRows();
    initQualificationDomains();
    initHelpPopovers();
    initPlanRegisterSubmit();
};
