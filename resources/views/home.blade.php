<x-app-layout :show-navigation="false">
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css">
        @vite('resources/css/home/home.css')
    @endpush

    @php($isAdmin = $isAdmin ?? request()->routeIs('admin.*'))
    <div class="h-screen bg-gray-100 overflow-hidden">
        <x-sidebar-layout context="home" :is-admin="$isAdmin">
            <div id="calendar" class="h-full min-h-[400px]"></div>
        </x-sidebar-layout>
    </div>

    @include('home.plan-register-modals')
    @include('home.plan-reschedule-modal')
    @include('home.study-record-modal')

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
        @vite('resources/js/home/home.js')
    @endpush
</x-app-layout>
