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

document.addEventListener('DOMContentLoaded', waitForCalendarLib);
