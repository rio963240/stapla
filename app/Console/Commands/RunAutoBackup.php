<?php

namespace App\Console\Commands;

use App\Models\BackupFile;
use App\Models\BackupSetting;
use App\Services\BackupService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class RunAutoBackup extends Command
{
    protected $signature = 'backups:auto';
    protected $description = 'Run automatic backup when scheduled time matches';

    public function handle(BackupService $backupService): int
    {
        $setting = BackupSetting::firstOrCreate(['settings_key' => 'default']);

        if (!$setting->is_enabled) {
            $this->info('Auto backup is disabled.');
            return Command::SUCCESS;
        }

        $runTime = $setting->run_time
            ? Carbon::parse($setting->run_time)->format('H:i')
            : '02:00';
        $nowTime = now()->format('H:i');

        if ($nowTime !== $runTime) {
            $this->info('Not scheduled time.');
            return Command::SUCCESS;
        }

        $latestAuto = BackupFile::query()
            ->where('is_auto', true)
            ->orderByDesc('created_at')
            ->first();

        if ($latestAuto?->created_at?->format('Y-m-d H:i') === now()->format('Y-m-d H:i')) {
            $this->info('Already executed this minute.');
            return Command::SUCCESS;
        }

        $result = $backupService->runBackup(true);

        if ($result['success']) {
            $this->info('Backup created.');
            return Command::SUCCESS;
        }

        $this->error($result['message'] ?? 'Backup failed.');
        return Command::FAILURE;
    }
}
