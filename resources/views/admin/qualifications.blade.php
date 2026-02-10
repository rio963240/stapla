<x-app-layout :show-navigation="false">
    @php($toastStatus = session('toast_status') ?? ($errors->any() ? 'error' : null))
    @php($toastMessage = session('toast_message') ?? ($errors->any() ? $errors->first() : null))
    @push('styles')
        @vite('resources/css/admin/admin-qualifications.css')
    @endpush
    @php($isAdmin = true)
    <div class="h-screen bg-gray-100 overflow-hidden">
        <div class="mx-auto flex h-[calc(100vh-3rem)] max-w-7xl gap-8 px-6 py-6 sm:px-6 lg:px-8">
            @include('home.sidebar', ['context' => 'admin', 'isAdmin' => $isAdmin])

            <section class="flex-1 rounded-lg bg-white p-6 shadow-sm overflow-auto">
                @include('admin.qualifications._header')
                @include('admin.qualifications._table')
            </section>
        </div>
    </div>

    @push('modals')
        @include('admin.qualifications._create-modal')
        @include('admin.qualifications._edit-modal')
        @include('admin.qualifications._delete-modal')
    @endpush

    <div class="admin-toast-stack" aria-live="polite" aria-atomic="true">
        <div id="admin-toast"
            class="admin-toast {{ $toastStatus ? 'is-visible' : '' }} {{ $toastStatus === 'error' ? 'is-error' : '' }}"
            role="status"
            data-toast-status="{{ $toastStatus ?? '' }}"
            data-toast-message="{{ $toastMessage ?? '' }}"
            data-toast-autoshow="{{ $toastStatus ? 'true' : 'false' }}">
            <span class="admin-toast-label"></span>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/admin/admin-qualifications.js')
    @endpush
</x-app-layout>
