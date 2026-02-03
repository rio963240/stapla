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
