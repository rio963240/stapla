// 管理画面のサイドバー内プロフィールメニュー開閉（資格一覧・バックアップ・ユーザー管理で共通）
const initProfileMenu = () => {
    // 複数トリガーに対応
    const triggers = document.querySelectorAll('.profile-menu-trigger');
    triggers.forEach((trigger) => {
        const menu = trigger.parentElement?.querySelector('.profile-menu');
        if (!menu) return;

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

        // トリガークリックで開閉
        trigger.addEventListener('click', (event) => {
            event.stopPropagation();
            if (menu.classList.contains('hidden')) {
                openMenu();
            } else {
                closeMenu();
            }
        });

        // 画面外クリックで閉じる
        document.addEventListener('click', (event) => {
            if (!menu.contains(event.target) && !trigger.contains(event.target)) {
                closeMenu();
            }
        });

        // ESCキーで閉じる
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeMenu();
            }
        });
    });
};

// DOM読み込み後に初期化
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initProfileMenu);
} else {
    initProfileMenu();
}
