<x-app-layout :show-navigation="false">
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css">
        <style>
            .fc .fc-toolbar-title {
                font-size: 1.25rem;
                font-weight: 600;
            }
        </style>
    @endpush

    <div class="min-h-screen bg-gray-100">
        <div class="mx-auto flex max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:px-8">
            <aside class="flex min-h-[calc(100vh-3rem)] w-64 flex-col rounded-lg bg-white p-6 shadow-sm">
                <nav class="flex flex-1 flex-col justify-between text-sm">
                    <a class="flex items-center rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100" href="#">
                        <span>HOME</span>
                    </a>
                    <a class="flex items-center rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100" href="#">
                        <span>計画登録</span>
                    </a>
                    <a class="flex items-center rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100" href="#">
                        <span>学習実績</span>
                    </a>
                    <a class="flex items-center rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100" href="#">
                        <span>リスケジュール</span>
                    </a>
                    <hr class="my-4 h-px w-40 self-center border-0 bg-black" />
                    <div class="flex items-center gap-3 px-3 pb-2">
                        <img
                            src="{{ asset('images/no-image.jpeg') }}"
                            alt="プロフィール"
                            class="h-14 w-14 rounded-full object-cover"
                        />
                    </div>
                </nav>
            </aside>

            <section class="flex-1 rounded-lg bg-white p-6 shadow-sm">
                <div id="calendar"></div>
            </section>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const calendarEl = document.getElementById('calendar');
                if (!calendarEl) return;

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'ja',
                    height: 'auto',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek',
                    },
                });

                calendar.render();
            });
        </script>
    @endpush
</x-app-layout>
