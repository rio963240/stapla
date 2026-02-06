// metaタグからCSRFトークンを取得
const getCsrfToken = () => {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : '';
};

// 日付ラベルの日本語表記
const formatDateLabel = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    if (Number.isNaN(date.getTime())) return dateString;
    return date.toLocaleDateString('ja-JP', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        weekday: 'short',
    });
};

// データなし表示の行
const buildEmptyRow = () => {
    const row = document.createElement('div');
    row.className = 'study-record-empty';
    row.textContent = '表示する項目がありません。';
    return row;
};

// 読み込み中表示の行
const buildLoadingRow = () => {
    const row = document.createElement('div');
    row.className = 'study-record-empty';
    row.textContent = '読み込み中...';
    return row;
};

// サマリー表示用の行（予定分数のみ）
const buildSummaryRow = (item) => {
    const row = document.createElement('div');
    row.className = 'study-record-row';

    const label = document.createElement('div');
    label.className = 'study-record-label';
    label.textContent = item.domain_name || '（名称未設定）';

    const value = document.createElement('div');
    value.className = 'study-record-inline';
    const minutes = document.createElement('span');
    minutes.textContent = `${item.planned_minutes ?? 0}分`;
    value.append(minutes);

    row.append(label, value);
    return row;
};

// 入力フォーム用の行（実績分数）
const buildFormRow = (item) => {
    const row = document.createElement('div');
    row.className = 'study-record-row';

    const label = document.createElement('div');
    label.className = 'study-record-label';
    label.textContent = item.domain_name || '（名称未設定）';

    const field = document.createElement('div');
    field.className = 'study-record-inline';

    const input = document.createElement('input');
    input.type = 'number';
    input.min = '0';
    input.max = '1440';
    input.className = 'modal-input modal-input-short';
    input.placeholder = String(item.planned_minutes ?? '');
    input.value = Number.isFinite(item.actual_minutes) ? String(item.actual_minutes) : '';
    input.dataset.studyRecordInput = 'true';
    input.dataset.studyPlanItemId = String(item.study_plan_items_id);

    const unit = document.createElement('span');
    unit.className = 'modal-unit';
    unit.textContent = '分';

    const note = document.createElement('span');
    note.className = 'study-record-note';
    note.textContent = `予定: ${item.planned_minutes ?? 0}分`;

    field.append(input, unit, note);
    row.append(label, field);
    return row;
};

export const initStudyRecordModal = () => {
    // モーダル内のDOM取得
    const modal = document.getElementById('study-record-modal');
    if (!modal) return null;

    const panel = modal.querySelector('.study-record-popover-panel');
    const titleNodes = modal.querySelectorAll('[data-study-record-title]');
    const dateNodes = modal.querySelectorAll('[data-study-record-date]');
    const summaryList = modal.querySelector('[data-study-record-summary-list]');
    const formList = modal.querySelector('[data-study-record-form-list]');
    const memoInput = modal.querySelector('[data-study-record-memo]');
    const nextButton = modal.querySelector('[data-study-record-next]');
    const backButton = modal.querySelector('[data-study-record-back]');
    const saveButton = modal.querySelector('[data-study-record-save]');
    const closeButtons = modal.querySelectorAll('[data-study-record-close]');
    const summaryStep = modal.querySelector('[data-study-record-step="summary"]');
    const formStep = modal.querySelector('[data-study-record-step="form"]');

    if (!panel || !summaryList || !formList || !memoInput || !nextButton || !backButton || !saveButton)
        return null;

    let currentTodoId = null;
    let lastAnchorEvent = null;

    // タイトル/日付のテキスト更新
    const setTextNodes = (nodes, text) => {
        nodes.forEach((node) => {
            node.textContent = text;
        });
    };

    // サマリー/フォームの表示切り替え
    const showStep = (step) => {
        summaryStep?.classList.toggle('hidden', step !== 'summary');
        formStep?.classList.toggle('hidden', step !== 'form');
        requestAnimationFrame(() => positionPanel(lastAnchorEvent));
    };

    // クリック位置を基準にパネルの表示位置を調整
    const positionPanel = (anchorEvent) => {
        if (!anchorEvent) return;
        const { clientX, clientY } = anchorEvent;
        const margin = 12;

        const panelRect = panel.getBoundingClientRect();
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;

        let left = clientX + 16;
        if (left + panelRect.width + margin > viewportWidth) {
            left = clientX - panelRect.width - 16;
        }
        left = Math.max(margin, Math.min(left, viewportWidth - panelRect.width - margin));

        let top = clientY - 24;
        if (top + panelRect.height + margin > viewportHeight) {
            top = viewportHeight - panelRect.height - margin;
        }
        top = Math.max(margin, top);

        panel.style.left = `${left}px`;
        panel.style.top = `${top}px`;
    };

    // モーダルを表示
    const openModal = (anchorEvent) => {
        lastAnchorEvent = anchorEvent ?? lastAnchorEvent;
        modal.classList.remove('is-hidden');
    };

    // モーダルを閉じて状態をリセット
    const closeModal = () => {
        modal.classList.add('is-hidden');
        showStep('summary');
        currentTodoId = null;
        lastAnchorEvent = null;
    };

    // 読み込み中の表示に切り替え
    const setLoading = () => {
        summaryList.innerHTML = '';
        formList.innerHTML = '';
        summaryList.append(buildLoadingRow());
        formList.append(buildLoadingRow());
        nextButton.setAttribute('disabled', 'true');
        saveButton.setAttribute('disabled', 'true');
    };

    // 取得した項目をサマリー/フォームに反映
    const setItems = (items) => {
        summaryList.innerHTML = '';
        formList.innerHTML = '';

        if (!Array.isArray(items) || items.length === 0) {
            summaryList.append(buildEmptyRow());
            formList.append(buildEmptyRow());
            nextButton.setAttribute('disabled', 'true');
            saveButton.setAttribute('disabled', 'true');
            return;
        }

        items.forEach((item) => {
            summaryList.append(buildSummaryRow(item));
            formList.append(buildFormRow(item));
        });

        nextButton.removeAttribute('disabled');
        saveButton.removeAttribute('disabled');
        requestAnimationFrame(() => positionPanel(lastAnchorEvent));
    };

    // todo詳細と項目の取得
    const fetchTodo = async (todoId) => {
        setLoading();
        try {
            const response = await fetch(`/study-records/todo/${todoId}`, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });
            if (!response.ok) throw new Error('failed to fetch todo');
            const data = await response.json();
            setTextNodes(titleNodes, data?.qualification_name ?? '');
            setTextNodes(dateNodes, formatDateLabel(data?.date));
            memoInput.value = data?.memo ?? '';
            setItems(Array.isArray(data?.items) ? data.items : []);
            requestAnimationFrame(() => positionPanel(lastAnchorEvent));
        } catch (error) {
            summaryList.innerHTML = '';
            summaryList.append(buildEmptyRow());
            formList.innerHTML = '';
            formList.append(buildEmptyRow());
            alert('実績データの取得に失敗しました。');
        }
    };

    // 入力フォームから実績データを回収
    const collectRecords = () =>
        Array.from(formList.querySelectorAll('[data-study-record-input]'))
            .map((input) => {
                if (!(input instanceof HTMLInputElement)) return null;
                const itemId = Number.parseInt(input.dataset.studyPlanItemId ?? '', 10);
                if (!Number.isFinite(itemId)) return null;
                const rawValue = input.value.trim();
                const actualMinutes = rawValue === '' ? 0 : Number.parseInt(rawValue, 10);
                if (!Number.isFinite(actualMinutes)) return null;
                return { study_plan_items_id: itemId, actual_minutes: actualMinutes };
            })
            .filter(Boolean);

    const saveRecords = async () => {
        if (!currentTodoId) return;
        const records = collectRecords();
        if (records.length === 0) {
            alert('実績分数を入力してください。');
            return;
        }

        // 保存中の表示に切り替え
        const originalLabel = saveButton.textContent;
        saveButton.setAttribute('disabled', 'true');
        saveButton.textContent = '保存中...';

        try {
            // 実績データを保存
            const response = await fetch('/study-records', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    todo_id: currentTodoId,
                    memo: memoInput.value,
                    records,
                }),
            });

            if (response.ok) {
                alert('実績を保存しました。');
                closeModal();
                return;
            }

            // バリデーション/エラーメッセージの整形
            const data = await response.json().catch(() => ({}));
            const message =
                (data?.errors && Object.values(data.errors).flat()[0]) ||
                data?.message ||
                '保存に失敗しました。';
            alert(message);
        } catch (error) {
            alert('通信に失敗しました。');
        } finally {
            // ボタン表示を元に戻す
            saveButton.removeAttribute('disabled');
            saveButton.textContent = originalLabel;
        }
    };

    // ステップ遷移と保存操作
    nextButton.addEventListener('click', () => showStep('form'));
    backButton.addEventListener('click', () => showStep('summary'));
    saveButton.addEventListener('click', saveRecords);

    // 閉じるボタンのイベント
    closeButtons.forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    // モーダル外クリックで閉じる
    document.addEventListener('click', (event) => {
        if (modal.classList.contains('is-hidden')) return;
        const target = event.target;
        if (!(target instanceof HTMLElement)) return;
        if (panel.contains(target)) return;
        closeModal();
    });

    // ESCキーで閉じる
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.classList.contains('is-hidden')) {
            closeModal();
        }
    });

    // 画面リサイズ時に位置調整
    window.addEventListener('resize', () => {
        if (!modal.classList.contains('is-hidden')) {
            requestAnimationFrame(() => positionPanel(lastAnchorEvent));
        }
    });

    return {
        // 外部から呼ぶモーダル表示API
        open: async ({ todoId, anchorEvent }) => {
            currentTodoId = todoId;
            showStep('summary');
            openModal(anchorEvent);
            requestAnimationFrame(() => positionPanel(anchorEvent));
            await fetchTodo(todoId);
        },
    };
};
