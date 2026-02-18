// CSRFトークン取得（Laravel用）
const getCsrfToken = () => {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : '';
};

// 資格名に応じてサブ分野を取得・反映
const initQualificationSubdomains = () => {
    const qualificationSelect = document.querySelector('[data-qualification-select="subdomain"]');
    if (!qualificationSelect) return;

    // サブ分野セレクトをまとめて取得
    const getSubdomainSelects = () => document.querySelectorAll('[data-subdomain-select="subdomain"]');

    // セレクト用のoptionを構築
    const buildOptions = (subdomains) => {
        const fragment = document.createDocumentFragment();
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = '選択してください';
        fragment.appendChild(placeholder);

        subdomains.forEach((subdomain) => {
            const option = document.createElement('option');
            option.value = String(subdomain.id);
            const domainName = subdomain.domain_name ? `${subdomain.domain_name} / ` : '';
            option.textContent = `${domainName}${subdomain.name}`;
            fragment.appendChild(option);
        });

        return fragment;
    };

    // 全セレクトにoptionを反映
    const applyOptions = (subdomains) => {
        getSubdomainSelects().forEach((select) => {
            select.innerHTML = '';
            select.appendChild(buildOptions(subdomains));
        });
    };

    // APIからサブ分野を取得
    const fetchSubdomains = async (qualificationId) => {
        if (!qualificationId) {
            applyOptions([]);
            return;
        }

        try {
            const response = await fetch(`/qualifications/${qualificationId}/subdomains`, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });
            if (!response.ok) throw new Error('failed to fetch subdomains');
            const subdomains = await response.json();
            applyOptions(Array.isArray(subdomains) ? subdomains : []);
        } catch (error) {
            applyOptions([]);
        }
    };

    // 資格変更時にサブ分野を再取得
    qualificationSelect.addEventListener('change', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLSelectElement)) return;
        fetchSubdomains(target.value);
    });
};

// 計画登録（サブ分野単位）の送信
const initPlanRegisterSubdomainSubmit = () => {
    const submitButton = document.querySelector('[data-plan-submit="subdomain"]');
    if (!submitButton) return;

    submitButton.addEventListener('click', async () => {
        // 入力要素を取得
        const startInput = document.querySelector('[data-plan-start="subdomain"]');
        const examInput = document.querySelector('[data-plan-exam="subdomain"]');
        const qualificationSelect = document.querySelector('[data-qualification-select="subdomain"]');
        const dailyInput = document.querySelector('[data-daily-study="subdomain"]');
        const bufferInput = document.querySelector('[data-buffer-rate="subdomain"]');
        const noStudyList = document.querySelector('[data-no-study-list="subdomain"]');

        if (!startInput || !examInput || !qualificationSelect || !dailyInput || !bufferInput) return;

        // 入力されたサブ分野と重みを収集
        const subdomains = Array.from(
            document.querySelectorAll('#plan-register-subdomain-modal [data-repeat-row]'),
        )
            .map((row) => {
                const subdomainSelect = row.querySelector('[data-subdomain-select="subdomain"]');
                const weightInput = row.querySelector('[data-subdomain-weight="subdomain"]');
                if (!(subdomainSelect instanceof HTMLSelectElement)) return null;
                if (!(weightInput instanceof HTMLInputElement)) return null;
                const id = Number.parseInt(subdomainSelect.value, 10);
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
        if (subdomains.length === 0) errors.push('サブ分野と重みを入力してください。');

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
            subdomains,
            no_study_days: noStudyDays,
        };

        // 送信中表示に切り替え
        const originalLabel = submitButton.textContent;
        submitButton.setAttribute('disabled', 'true');
        submitButton.textContent = '送信中...';

        try {
            // 計画登録APIへ送信
            const response = await fetch('/plan-register/subdomain', {
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

            // エラーメッセージの整形
            const data = await response.json().catch(() => ({}));
            const message =
                (data?.errors && Object.values(data.errors).flat()[0]) ||
                data?.message ||
                '登録に失敗しました。';
            alert(message);
        } catch (error) {
            alert('通信に失敗しました。');
        } finally {
            // ボタン表示を元に戻す
            submitButton.removeAttribute('disabled');
            submitButton.textContent = originalLabel;
        }
    });
};

// 初期化（サブ分野取得と送信処理）
export const initPlanRegisterSubdomain = () => {
    initQualificationSubdomains();
    initPlanRegisterSubdomainSubmit();
};
