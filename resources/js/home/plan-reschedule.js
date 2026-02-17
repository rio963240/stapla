// CSRFトークン取得（Laravel用）
const getCsrfToken = () => {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : '';
};

// 日付入力用の yyyy-mm-dd 形式を作る
const formatDateInputValue = (date) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

// フォームを開いた日の「翌日」
const getTomorrowValue = () => {
    const date = new Date();
    date.setDate(date.getDate() + 1);
    return formatDateInputValue(date);
};

// 対象資格の切替時にフォームを初期化
const resetRescheduleForm = (modal) => {
    modal.querySelector('[data-reschedule-qualification]')?.setAttribute('value', '');
    modal.querySelector('[data-reschedule-exam]')?.setAttribute('value', '');
    const startInput = modal.querySelector('[data-reschedule-start-date]');
    if (startInput) startInput.value = getTomorrowValue();
    const dailyInput = modal.querySelector('[data-reschedule-daily]');
    if (dailyInput) dailyInput.value = '';
    const bufferInput = modal.querySelector('[data-reschedule-buffer]');
    if (bufferInput) bufferInput.value = '';
    const noStudyList = modal.querySelector('[data-reschedule-no-study-list]');
    if (noStudyList) noStudyList.innerHTML = '';
    const weightList = modal.querySelector('[data-reschedule-weights]');
    if (weightList) weightList.innerHTML = '';
    const weightTypeLabel = modal.querySelector('[data-reschedule-weight-type-label]');
    if (weightTypeLabel) weightTypeLabel.textContent = '分野';
    modal.dataset.weightType = '';
};

// 勉強不可日のチップを描画
const renderNoStudyChips = (modal, dates) => {
    const list = modal.querySelector('[data-reschedule-no-study-list]');
    if (!list) return;
    list.innerHTML = '';
    dates.forEach((date) => {
        const chip = document.createElement('div');
        chip.className = 'modal-chip';
        chip.dataset.noStudyChip = date;
        chip.innerHTML = `<span>${date}</span><button type="button" aria-label="削除">×</button>`;
        chip.querySelector('button')?.addEventListener('click', () => chip.remove());
        list.appendChild(chip);
    });
};

// 分野/サブ分野の重みを描画
const renderWeights = (modal, weightType, weights) => {
    const list = modal.querySelector('[data-reschedule-weights]');
    if (!list) return;
    list.innerHTML = '';
    const label = modal.querySelector('[data-reschedule-weight-type-label]');
    if (label) {
        label.textContent = weightType === 'subdomain' ? 'サブ分野' : '分野';
    }
    modal.dataset.weightType = weightType;

    weights.forEach((weight) => {
        const row = document.createElement('div');
        row.className = 'modal-inline';
        row.innerHTML = `
            <input type="text" class="modal-input" value="${weight.name}" readonly />
            <input
                type="number"
                class="modal-input modal-input-short"
                value="${weight.weight}"
                data-reschedule-weight-id="${weight.id}"
                aria-label="重み"
            />
        `;
        list.appendChild(row);
    });
};

// 選択した対象の初期データを取得
const fetchRescheduleData = async (targetId) => {
    const params = new URLSearchParams({ target_id: String(targetId) });
    const response = await fetch(`/plan-reschedule/target?${params.toString()}`, {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
    });
    if (!response.ok) {
        const data = await response.json().catch(() => ({}));
        const message =
            (data?.errors && Object.values(data.errors).flat()[0]) ||
            data?.message ||
            '情報の取得に失敗しました。';
        throw new Error(message);
    }
    return response.json();
};

// モーダルの開閉と対象資格変更時の反映（PC・スマホの両方のサイドバーに対応）
const initPlanRescheduleModal = () => {
    const triggers = document.querySelectorAll('.plan-reschedule-trigger');
    const modal = document.getElementById('plan-reschedule-modal');
    if (!modal) return;
    const targetSelect = modal.querySelector('[data-reschedule-target]');

    const open = () => {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    const close = () => {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', open);
    });
    modal.querySelectorAll('[data-reschedule-close]').forEach((button) => {
        button.addEventListener('click', close);
    });
    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            close();
        }
    });

    if (targetSelect instanceof HTMLSelectElement) {
        targetSelect.addEventListener('change', async () => {
            const targetId = Number.parseInt(targetSelect.value, 10);
            if (!Number.isFinite(targetId)) {
                resetRescheduleForm(modal);
                return;
            }
            resetRescheduleForm(modal);
            try {
                const data = await fetchRescheduleData(targetId);
                modal.querySelector('[data-reschedule-qualification]')?.setAttribute(
                    'value',
                    data?.qualification_name ?? '',
                );
                modal.querySelector('[data-reschedule-exam]')?.setAttribute(
                    'value',
                    data?.exam_date ?? '',
                );
                const startInput = modal.querySelector('[data-reschedule-start-date]');
                if (startInput) startInput.value = getTomorrowValue();
                const dailyInput = modal.querySelector('[data-reschedule-daily]');
                if (dailyInput) dailyInput.value = String(data?.daily_study_time ?? '');
                const bufferInput = modal.querySelector('[data-reschedule-buffer]');
                if (bufferInput) bufferInput.value = String(data?.buffer_rate ?? '');
                renderNoStudyChips(modal, Array.isArray(data?.no_study_days) ? data.no_study_days : []);
                renderWeights(
                    modal,
                    data?.weight_type ?? 'domain',
                    Array.isArray(data?.weights) ? data.weights : [],
                );
            } catch (error) {
                alert(error instanceof Error ? error.message : '情報の取得に失敗しました。');
            }
        });
    }
};

// 勉強不可日のチップ追加
const initRescheduleNoStudyChips = () => {
    const modal = document.getElementById('plan-reschedule-modal');
    if (!modal) return;
    const addButton = modal.querySelector('[data-reschedule-no-study-add]');
    const input = modal.querySelector('[data-reschedule-no-study-input]');
    const list = modal.querySelector('[data-reschedule-no-study-list]');
    if (!(addButton instanceof HTMLElement) || !(input instanceof HTMLInputElement) || !list) return;

    const addChip = () => {
        const value = input.value;
        if (!value) return;
        if (list.querySelector(`[data-no-study-chip="${value}"]`)) {
            input.value = '';
            return;
        }
        const chip = document.createElement('div');
        chip.className = 'modal-chip';
        chip.dataset.noStudyChip = value;
        chip.innerHTML = `<span>${value}</span><button type="button" aria-label="削除">×</button>`;
        chip.querySelector('button')?.addEventListener('click', () => chip.remove());
        list.appendChild(chip);
        input.value = '';
    };

    addButton.addEventListener('click', addChip);
    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            addChip();
        }
    });
};

// リスケ実行の送信処理
const initPlanRescheduleSubmit = () => {
    const submitButton = document.querySelector('[data-reschedule-submit]');
    if (!submitButton) return;

    submitButton.addEventListener('click', async () => {
        const modal = document.getElementById('plan-reschedule-modal');
        if (!modal) return;
        const targetSelect = modal.querySelector('[data-reschedule-target]');
        if (!(targetSelect instanceof HTMLSelectElement)) return;

        const targetId = Number.parseInt(targetSelect.value, 10);
        if (!Number.isFinite(targetId)) {
            alert('対象資格を選択してください。');
            return;
        }

        const dailyInput = modal.querySelector('[data-reschedule-daily]');
        const bufferInput = modal.querySelector('[data-reschedule-buffer]');
        const dailyStudyTime = Number.parseInt(dailyInput?.value ?? '', 10);
        const bufferRate = Number.parseInt(bufferInput?.value ?? '', 10);
        if (!Number.isFinite(dailyStudyTime) || dailyStudyTime <= 0) {
            alert('1日の学習時間を入力してください。');
            return;
        }
        const startDateInput = modal.querySelector('[data-reschedule-start-date]');
        const rescheduleStartDate = startDateInput?.value ?? '';
        if (!rescheduleStartDate) {
            alert('リスケ開始日を入力してください。');
            return;
        }
        if (!Number.isFinite(bufferRate) || bufferRate < 0 || bufferRate > 99) {
            alert('余裕確保率は0〜99で入力してください。');
            return;
        }

        const weightType = modal.dataset.weightType || 'domain';
        const weightInputs = Array.from(
            modal.querySelectorAll('[data-reschedule-weight-id]'),
        );
        const weights = weightInputs
            .map((input) => {
                if (!(input instanceof HTMLInputElement)) return null;
                const id = Number.parseInt(input.dataset.rescheduleWeightId ?? '', 10);
                const weight = Number.parseInt(input.value, 10);
                if (!Number.isFinite(id) || !Number.isFinite(weight)) return null;
                return { id, weight };
            })
            .filter(Boolean);
        if (weights.length === 0) {
            alert('分野の重みが取得できませんでした。');
            return;
        }

        const noStudyList = modal.querySelector('[data-reschedule-no-study-list]');
        const noStudyDays = noStudyList
            ? Array.from(noStudyList.querySelectorAll('[data-no-study-chip]'))
                  .map((chip) => chip.dataset.noStudyChip)
                  .filter(Boolean)
            : [];

        const ok = window.confirm('明日以降の計画を削除して再生成します。よろしいですか？');
        if (!ok) return;

        const originalLabel = submitButton.textContent;
        submitButton.setAttribute('disabled', 'true');
        submitButton.textContent = '処理中...';

        try {
            const response = await fetch('/plan-reschedule', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    target_id: targetId,
                    reschedule_start_date: rescheduleStartDate,
                    daily_study_time: dailyStudyTime,
                    buffer_rate: bufferRate,
                    no_study_days: noStudyDays,
                    weight_type: weightType,
                    weights,
                }),
            });

            if (response.ok) {
                alert('リスケジュールが完了しました。');
                window.location.reload();
                return;
            }

            const data = await response.json().catch(() => ({}));
            const message =
                (data?.errors && Object.values(data.errors).flat()[0]) ||
                data?.message ||
                'リスケジュールに失敗しました。';
            alert(message);
        } catch (error) {
            alert('通信に失敗しました。');
        } finally {
            submitButton.removeAttribute('disabled');
            submitButton.textContent = originalLabel;
        }
    });
};

// リスケジュール機能の初期化
export const initPlanReschedule = () => {
    initPlanRescheduleModal();
    initRescheduleNoStudyChips();
    initPlanRescheduleSubmit();
};
