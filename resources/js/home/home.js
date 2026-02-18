import { initStudyRecordModal } from './study-record-modal';

let studyRecordModal = null;

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
            container.classList.add('fc-month-event-title');
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
        eventOrder: 'planOrder',
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
        // ビュー切替・今日ボタンの表示名
        buttonText: {
            today: '今日',
            month: '月',
            week: '週',
        },
        // 週表示の列ヘッダーは「日付(曜日)」のみ（月は出さない）
        views: {
            dayGridWeek: {
                dayHeaderContent: (arg) => {
                    const d = arg.date;
                    const day = d.getDate();
                    const weekdays = ['日', '月', '火', '水', '木', '金', '土'];
                    const w = weekdays[d.getDay()];
                    return `${day}(${w})`;
                },
            },
        },
        eventClick: (info) => {
            if (info.view.type !== 'dayGridWeek') return;
            const todoId = info.event.extendedProps?.todoId;
            if (!todoId || !studyRecordModal?.open) return;
            info.jsEvent?.preventDefault();
            info.jsEvent?.stopPropagation();
            studyRecordModal.open({ todoId, anchorEvent: info.jsEvent });
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
import { initPlanRegisterSubdomain } from './plan-register-subdomain';
// リスケジュール機能
import { initPlanReschedule } from './plan-reschedule';

// プロフィールメニューの開閉（PC・スマホの両方のサイドバーに対応）
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

// DOM準備完了でコールバック実行
const onReady = (callback) => {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback);
    } else {
        callback();
    }
};

const openPlanRegisterFromQuery = () => {
    const params = new URLSearchParams(window.location.search);
    if (params.get('open') !== 'plan-register') return;
    const trigger = document.querySelector('.plan-register-trigger');
    if (trigger) {
        trigger.click();
    }

    params.delete('open');
    const next = params.toString();
    const nextUrl = next ? `${window.location.pathname}?${next}` : window.location.pathname;
    window.history.replaceState(null, '', nextUrl);
};

const openRescheduleFromQuery = () => {
    const params = new URLSearchParams(window.location.search);
    if (params.get('open') !== 'reschedule') return;
    const trigger = document.querySelector('.plan-reschedule-trigger');
    if (trigger) {
        trigger.click();
    }

    params.delete('open');
    const next = params.toString();
    const nextUrl = next ? `${window.location.pathname}?${next}` : window.location.pathname;
    window.history.replaceState(null, '', nextUrl);
};

// 初期化エントリポイント
onReady(() => {
    studyRecordModal = initStudyRecordModal();
    waitForCalendarLib();
    initPlanRegister();
    openPlanRegisterFromQuery();
    initPlanReschedule();
    openRescheduleFromQuery();
    initPlanRegisterSubdomain();
    initProfileMenu();
});
