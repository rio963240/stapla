// カレンダーの初期化（FullCalendar）
const initCalendar = () => {
    const calendarEl = document.getElementById('calendar');

    if (!calendarEl || !window.FullCalendar) return false;

    let calendar;
    // ビュー切替時にAPIのviewパラメータを更新するため保持
    let currentViewType = 'dayGridMonth';
    const renderEventContent = (arg) => {
        const { qualificationName, domainName, plannedMinutes, details } = arg.event.extendedProps ?? {};
        const container = document.createElement('div');

        // 月表示は資格名のみ
        if (arg.view.type === 'dayGridMonth') {
            container.textContent = qualificationName || arg.event.title;
            return { domNodes: [container] };
        }

        // 週表示は資格名 + 分野名/分数
        const title = document.createElement('div');
        title.textContent = qualificationName || arg.event.title;
        container.append(title);

        if (Array.isArray(details) && details.length > 0) {
            details.forEach((item) => {
                const line = document.createElement('div');
                if (item?.domainName && Number.isFinite(item?.plannedMinutes)) {
                    line.textContent = `${item.domainName}: ${item.plannedMinutes}分`;
                } else if (Number.isFinite(item?.plannedMinutes)) {
                    line.textContent = `${item.plannedMinutes}分`;
                }
                container.append(line);
            });
            return { domNodes: [container] };
        }

        const detail = document.createElement('div');
        if (domainName && Number.isFinite(plannedMinutes)) {
            detail.textContent = `${domainName}: ${plannedMinutes}分`;
        } else if (Number.isFinite(plannedMinutes)) {
            detail.textContent = `${plannedMinutes}分`;
        } else {
            detail.textContent = '';
        }
        container.append(detail);
        return { domNodes: [container] };
    };

    calendar = new window.FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'ja',
        height: '100%',
        allDaySlot: false,
        // 同時刻のイベントは横並びにせず縦に積む
        slotEventOverlap: false,
        eventOverlap: false,
        // FullCalendarのリクエスト範囲に合わせてイベントを取得
        events: (info, successCallback, failureCallback) => {
            const viewType = currentViewType;
            const params = new URLSearchParams({
                start: info.startStr,
                end: info.endStr,
                view: viewType,
            });
            fetch(`/calendar/events?${params.toString()}`, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            })
                .then((response) => {
                    if (!response.ok) throw new Error('failed to load events');
                    return response.json();
                })
                .then((events) => successCallback(events))
                .catch((error) => failureCallback(error));
        },
        eventContent: renderEventContent,
        // ビュー切替時に view を更新して再取得
        datesSet: (arg) => {
            const nextViewType = arg.view.type;
            const shouldRefetch = currentViewType !== nextViewType;
            currentViewType = nextViewType;
            if (shouldRefetch && calendar) {
                calendar.refetchEvents();
            }
        },
        // 上部ツールバーの配置
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek',
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

import { initPlanRegister } from './plan-register';

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
    initPlanRegister();
    initProfileMenu();
});
