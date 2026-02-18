<x-app-layout :show-navigation="false">
    @php($toastStatus = session('toast_status') ?? ($errors->any() ? 'error' : null))
    @php($toastMessage = session('toast_message') ?? ($errors->any() ? $errors->first() : null))
    @push('styles')
        @vite('resources/css/admin/admin-qualifications.css')
        @vite('resources/css/home/profile-menu.css')
    @endpush
    @php($isAdmin = true)
    <div class="h-screen bg-gray-100 overflow-hidden">
        <x-sidebar-layout context="admin" :is-admin="$isAdmin">
            @include('admin.qualifications._header')
            @include('admin.qualifications._table')
        </x-sidebar-layout>
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
        @vite('resources/js/admin/profile-menu.js')
    @endpush
</x-app-layout>
