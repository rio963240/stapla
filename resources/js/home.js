const initCalendar = () => {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl || !window.FullCalendar) return false;

    const calendar = new window.FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'ja',
        height: '100%',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek',
        },
        buttonText: {
            month: '月',
            week: '週',
        },
    });

    calendar.render();
    return true;
};

const waitForCalendarLib = () => {
    if (initCalendar()) return;

    let attempts = 0;
    const timer = setInterval(() => {
        attempts += 1;
        if (initCalendar() || attempts >= 50) {
            clearInterval(timer);
        }
    }, 100);
};

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

    const closeAll = () => {
        modals.forEach((modal) => modal.classList.add('hidden'));
        document.body.classList.remove('overflow-hidden');
    };

    const openModal = (targetId) => {
        modals.forEach((modal) => {
            modal.classList.toggle('hidden', modal.id !== targetId);
        });
        document.body.classList.add('overflow-hidden');
    };

    trigger?.addEventListener('click', () => openModal('plan-register-choice-modal'));
    choiceDomainButton?.addEventListener('click', () => openModal('plan-register-domain-modal'));
    choiceSubdomainButton?.addEventListener('click', () =>
        openModal('plan-register-subdomain-modal'),
    );

    document.querySelectorAll('[data-modal-close]').forEach((button) => {
        button.addEventListener('click', closeAll);
    });

    modals.forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeAll();
            }
        });
    });
};

const initNoStudyChips = () => {
    document.querySelectorAll('[data-no-study-add]').forEach((button) => {
        const key = button.dataset.noStudyAdd;
        const input = document.querySelector(`[data-no-study-input="${key}"]`);
        const list = document.querySelector(`[data-no-study-list="${key}"]`);
        if (!input || !list) return;

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
            chip.querySelector('button').addEventListener('click', () => chip.remove());
            list.appendChild(chip);
            input.value = '';
        };

        button.addEventListener('click', addChip);
        input.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                addChip();
            }
        });
    });
};

const initRepeatRows = () => {
    document.querySelectorAll('[data-repeat-add]').forEach((button) => {
        const key = button.dataset.repeatAdd;
        const container = button.closest('[data-repeat-container]');
        if (!key || !container) return;

        button.addEventListener('click', () => {
            const row = button.closest('[data-repeat-row]');
            if (!row) return;

            const clone = row.cloneNode(true);
            const addButton = clone.querySelector('[data-repeat-add]');
            if (addButton) {
                addButton.removeAttribute('data-repeat-add');
                addButton.setAttribute('data-repeat-remove', key);
                addButton.setAttribute('aria-label', '入力欄を削除');
                addButton.textContent = '-';
            }

            clone.querySelectorAll('input').forEach((input) => {
                input.value = '';
            });
            clone.querySelectorAll('select').forEach((select) => {
                select.selectedIndex = 0;
            });

            container.appendChild(clone);
        });
    });

    document.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) return;
        if (!target.matches('[data-repeat-remove]')) return;
        const row = target.closest('[data-repeat-row]');
        if (row) row.remove();
    });
};

const initProfileMenu = () => {
    const trigger = document.getElementById('profile-menu-trigger');
    const menu = document.getElementById('profile-menu');
    if (!trigger || !menu) return;

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
};

const onReady = (callback) => {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback);
    } else {
        callback();
    }
};

onReady(() => {
    waitForCalendarLib();
    initPlanRegisterModals();
    initNoStudyChips();
    initRepeatRows();
    initProfileMenu();
});
