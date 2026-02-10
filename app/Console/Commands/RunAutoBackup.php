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
        // 設定を取得（未作成なら初期化）
        $setting = BackupSetting::firstOrCreate(['settings_key' => 'default']);

        // 自動バックアップが無効なら終了
        if (!$setting->is_enabled) {
            $this->info(__('console.backup.disabled'));
            return Command::SUCCESS;
        }

        // 実行時刻の判定
        $runTime = $setting->run_time
            ? Carbon::parse($setting->run_time)->format('H:i')
            : '02:00';
        $nowTime = now()->format('H:i');

        if ($nowTime !== $runTime) {
            $this->info(__('console.backup.not_scheduled'));
            return Command::SUCCESS;
        }

        // 直近の自動バックアップが同一分に実行済みならスキップ
        $latestAuto = BackupFile::query()
            ->where('is_auto', true)
            ->orderByDesc('created_at')
            ->first();

        if ($latestAuto?->created_at?->format('Y-m-d H:i') === now()->format('Y-m-d H:i')) {
            $this->info(__('console.backup.already_executed'));
            return Command::SUCCESS;
        }

        // 自動バックアップを実行
        $result = $backupService->runBackup(true);

        // 実行結果に応じて終了コードを返す
        if ($result['success']) {
            $this->info(__('console.backup.created'));
            return Command::SUCCESS;
        }

        $this->error($result['message'] ?? __('console.backup.failed'));
        return Command::FAILURE;
    }
}
