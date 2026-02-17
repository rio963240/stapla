<x-app-layout :show-navigation="false">
    @php
        $backupItems = $backupItems ?? [];
        $setting = $setting ?? [
            'is_enabled' => false,
            'run_time' => '02:00',
            'frequency' => 'daily',
        ];
        $latestBackupAt = $latestBackupAt ?? '-';
        $autoSettingLabel = $setting['is_enabled']
            ? "ON（毎日 {$setting['run_time']} 実行）"
            : 'OFF';
        $storageUsageLabel = $storageUsageLabel ?? '-';
    @endphp
    @push('styles')
        @vite('resources/css/admin/admin-backups.css')
    @endpush

    <div class="h-screen bg-gray-100 overflow-hidden">
        <x-sidebar-layout context="admin" :is-admin="true">
            @include('admin.backups._header')

            <div class="admin-backup-grid mt-6">
                @include('admin.backups._manual-card')
                @include('admin.backups._auto-card')
            </div>

            @include('admin.backups._history-card')
        </x-sidebar-layout>
    </div>

    <div class="admin-toast-stack" aria-live="polite" aria-atomic="true">
        <div class="admin-toast" role="status" data-backup-toast>
            <span class="admin-toast-label"></span>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/admin/admin-backups.js')
    @endpush
</x-app-layout>
