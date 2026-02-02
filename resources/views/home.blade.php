<x-app-layout :show-navigation="false">
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css">
        @vite('resources/css/home.css')
    @endpush

    <div class="h-screen bg-gray-100 overflow-hidden">
        <div class="mx-auto flex h-[calc(100vh-3rem)] max-w-7xl gap-8 px-6 py-6 sm:px-6 lg:px-8">
            <aside class="flex h-full w-64 flex-col rounded-lg bg-white p-6 shadow-sm">
                <nav class="flex flex-1 flex-col justify-between text-lg">
                    <a class="flex items-center rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100" href="#">
                        <span>HOME</span>
                    </a>
                    <button
                        id="plan-register-trigger"
                        type="button"
                        class="flex items-center rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100"
                    >
                        <span>計画登録</span>
                    </button>
                    <a class="flex items-center rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100" href="#">
                        <span>学習実績</span>
                    </a>
                    <a class="flex items-center rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100" href="#">
                        <span>リスケジュール</span>
                    </a>
                    <hr class="my-2 h-px w-40 self-center border-0 bg-black" />
                    @include('home.profile-menu')
                </nav>
            </aside>

            <section class="flex-1 rounded-lg bg-white p-6 shadow-sm overflow-auto">
                <div id="calendar" class="h-full"></div>
            </section>
        </div>
    </div>

    @include('home.plan-register-modals')

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
        @vite('resources/js/home.js')
    @endpush
</x-app-layout>
