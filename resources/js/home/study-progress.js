// サーバーから渡された初期データ
const initialData = window.studyProgressInitialData ?? {};

let cumulativeChart = null;
let periodChart = null;

// DOM読み込み後に実行するユーティリティ
const onReady = (callback) => {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback);
    } else {
        callback();
    }
};

// 率表示のフォーマット
const formatRate = (value) => {
    if (!Number.isFinite(value)) return '--';
    return `${value}%`;
};

// 分数表示のフォーマット
const formatMinutes = (value) => {
    if (!Number.isFinite(value)) return '--';
    return `${value}分`;
};

// 空表示のON/OFF
const setEmptyState = (key, isEmpty) => {
    const el = document.querySelector(`[data-study-progress-empty="${key}"]`);
    if (!el) return;
    el.classList.toggle('is-visible', isEmpty);
};

// サマリー（達成率/実績合計）の更新
const updateSummary = (summary) => {
    const rateEl = document.querySelector('[data-study-progress-summary-rate]');
    const actualEl = document.querySelector('[data-study-progress-summary-actual]');
    if (!rateEl || !actualEl) return;

    const rate = Number(summary?.achievement_rate ?? 0);
    const actual = Number(summary?.actual_total ?? 0);
    rateEl.textContent = formatRate(rate);
    actualEl.textContent = formatMinutes(actual);
};

// 累積グラフの生成・更新
const buildCumulativeChart = (data) => {
    const canvas = document.getElementById('study-progress-cumulative');
    if (!canvas || !window.Chart) return;

    const labels = data.map((row) => row.date);
    const actual = data.map((row) => row.actual_cumulative);
    const planned = data.map((row) => row.planned_cumulative);

    if (cumulativeChart) {
        cumulativeChart.data.labels = labels;
        cumulativeChart.data.datasets[0].data = actual;
        cumulativeChart.data.datasets[1].data = planned;
        cumulativeChart.update();
        return;
    }

    cumulativeChart = new window.Chart(canvas, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: '実績累積',
                    data: actual,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    tension: 0.2,
                    pointRadius: 3,
                },
                {
                    label: '計画累積',
                    data: planned,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.2)',
                    tension: 0.2,
                    pointRadius: 3,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: '累積(分)',
                    },
                },
            },
            plugins: {
                legend: {
                    position: 'bottom',
                },
            },
        },
    });
};

// 分野別リストの描画
const renderDomainList = (items) => {
    const container = document.querySelector('[data-study-progress-domain-list]');
    if (!container) return;

    container.innerHTML = '';

    if (!Array.isArray(items) || items.length === 0) {
        setEmptyState('domain', true);
        return;
    }

    setEmptyState('domain', false);

    items.forEach((item) => {
        const rate = Number(item.achievement_rate ?? 0);
        const planned = Number(item.planned_minutes ?? 0);
        const actual = Number(item.actual_minutes ?? 0);
        const width = Math.min(rate, 100);

        const wrapper = document.createElement('div');
        wrapper.className = 'study-progress-domain-item';

        const head = document.createElement('div');
        head.className = 'study-progress-domain-head';

        const name = document.createElement('span');
        name.className = 'study-progress-domain-name';
        name.textContent = item.name ?? '未分類';

        const meta = document.createElement('span');
        meta.className = 'study-progress-domain-meta';
        meta.textContent = `${formatRate(rate)} (${formatMinutes(actual)}/${formatMinutes(planned)})`;

        head.append(name, meta);

        const bar = document.createElement('div');
        bar.className = 'study-progress-domain-bar';

        const fill = document.createElement('div');
        fill.className = 'study-progress-domain-bar-fill';
        fill.style.width = `${width}%`;

        bar.append(fill);
        wrapper.append(head, bar);
        container.append(wrapper);
    });
};

// 期間別達成率グラフの生成・更新
const buildPeriodChart = (data) => {
    const canvas = document.getElementById('study-progress-period');
    if (!canvas || !window.Chart) return;

    const labels = data.map((row) => row.date);
    const rates = data.map((row) => row.achievement_rate);
    const colors = rates.map((rate) => {
        if (rate >= 100) return '#10b981';
        if (rate >= 80) return '#f59e0b';
        return '#ef4444';
    });
    const maxRate = rates.length ? Math.max(...rates, 100) : 100;

    if (periodChart) {
        periodChart.data.labels = labels;
        periodChart.data.datasets[0].data = rates;
        periodChart.data.datasets[0].backgroundColor = colors;
        periodChart.options.scales.y.suggestedMax = Math.ceil(maxRate / 10) * 10;
        periodChart.update();
        return;
    }

    periodChart = new window.Chart(canvas, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: '達成率',
                    data: rates,
                    backgroundColor: colors,
                    borderRadius: 6,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: Math.ceil(maxRate / 10) * 10,
                    title: {
                        display: true,
                        text: '達成率(%)',
                    },
                },
            },
            plugins: {
                legend: {
                    display: false,
                },
            },
        },
    });
};

// 画面全体の表示更新
const updateCharts = (data) => {
    const cumulative = data?.cumulative ?? [];
    const period = data?.period_rates ?? [];

    setEmptyState('cumulative', cumulative.length === 0);
    setEmptyState('period', period.length === 0);

    if (cumulative.length > 0) {
        buildCumulativeChart(cumulative);
    } else if (cumulativeChart) {
        cumulativeChart.data.labels = [];
        cumulativeChart.data.datasets[0].data = [];
        cumulativeChart.data.datasets[1].data = [];
        cumulativeChart.update();
    }
    renderDomainList(data?.domain_rates ?? []);

    if (period.length > 0) {
        buildPeriodChart(period);
    } else if (periodChart) {
        periodChart.data.labels = [];
        periodChart.data.datasets[0].data = [];
        periodChart.update();
    }

    updateSummary(data?.summary ?? {});
};

// 期間・対象を指定してAPIからデータ取得
const fetchData = async ({ targetId, start, end }) => {
    if (!targetId) return;
    const params = new URLSearchParams({ target_id: String(targetId) });
    if (start) params.set('start', start);
    if (end) params.set('end', end);

    const response = await fetch(`/study-progress/data?${params.toString()}`, {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
    });

    if (!response.ok) {
        throw new Error('failed to load data');
    }

    return response.json();
};

// 画面初期化とイベント登録
const init = () => {
    const targetSelect = document.querySelector('[data-study-progress-target]');
    const startInput = document.querySelector('[data-study-progress-start]');
    const endInput = document.querySelector('[data-study-progress-end]');
    const applyButton = document.querySelector('[data-study-progress-apply]');

    initProfileMenu();
    updateCharts(initialData);

    if (!targetSelect || !startInput || !endInput || !applyButton) return;

    const applyFilters = async () => {
        try {
            const data = await fetchData({
                targetId: targetSelect.value,
                start: startInput.value,
                end: endInput.value,
            });

            if (data?.period_start) startInput.value = data.period_start;
            if (data?.period_end) endInput.value = data.period_end;

            updateCharts(data);
        } catch (error) {
            setEmptyState('cumulative', true);
            setEmptyState('domain', true);
            setEmptyState('period', true);
        }
    };

    targetSelect.addEventListener('change', applyFilters);
    applyButton.addEventListener('click', applyFilters);
};

// プロフィールメニューの開閉処理（PC・スマホの両方のサイドバーに対応）
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

onReady(init);
