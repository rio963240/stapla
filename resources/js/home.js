// カレンダーの初期化（FullCalendar）
const initCalendar = () => {
    const calendarEl = document.getElementById('calendar');

    if (!calendarEl || !window.FullCalendar) return false;

    const calendar = new window.FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'ja',
        height: '100%',
        allDaySlot: false,
        // 上部ツールバーの配置
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek',
        },
        // ビュー切替ボタンの表示名
        buttonText: {
            month: '月',
            week: '週',
        },
    });

    // 画面に描画
    calendar.render();
    return true;
};

// FullCalendarの読み込み完了まで待機
const waitForCalendarLib = () => {
    if (initCalendar()) return;

    let attempts = 0;
    const timer = setInterval(() => {
        attempts += 1;
        // 一定回数で打ち切り
        if (initCalendar() || attempts >= 50) {
            clearInterval(timer);
        }
    }, 100);
};

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

// プロフィールメニューの開閉
const initProfileMenu = () => {
    const trigger = document.getElementById('profile-menu-trigger');
    const menu = document.getElementById('profile-menu');
    if (!trigger || !menu) return;

    // メニューを閉じる
    const closeMenu = () => {
        menu.classList.add('hidden');
        trigger.setAttribute('aria-expanded', 'false');
    };

    // メニューを開く
    const openMenu = () => {
        menu.classList.remove('hidden');
        trigger.setAttribute('aria-expanded', 'true');
    };

    // クリックでトグル
    trigger.addEventListener('click', (event) => {
        event.stopPropagation();
        if (menu.classList.contains('hidden')) {
            openMenu();
        } else {
            closeMenu();
        }
    });

    // 外側クリックで閉じる
    document.addEventListener('click', (event) => {
        if (!menu.contains(event.target) && !trigger.contains(event.target)) {
            closeMenu();
        }
    });

    // ESCで閉じる
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenu();
        }
    });
};

// DOM準備完了でコールバック実行
const onReady = (callback) => {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback);
    } else {
        callback();
    }
};

// 初期化エントリポイント
onReady(() => {
    waitForCalendarLib();
    initPlanRegisterModals();
    initNoStudyChips();
    initRepeatRows();
    initProfileMenu();
});
