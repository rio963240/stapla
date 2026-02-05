<x-app-layout :show-navigation="false">
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css">
        @vite('resources/css/home.css')
    @endpush

    <div class="h-screen bg-gray-100 overflow-hidden">
        <div class="mx-auto flex h-[calc(100vh-3rem)] max-w-7xl gap-8 px-6 py-6 sm:px-6 lg:px-8">
            @include('home.sidebar', ['context' => 'home'])

            <section class="flex-1 rounded-lg bg-white p-6 shadow-sm overflow-auto">
                <div id="calendar" class="h-full"></div>
            </section>
        </div>
    </div>

    @include('home.plan-register-modals')
    @include('home.study-record-modal')

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
        @vite('resources/js/home.js')
    @endpush
</x-app-layout>
