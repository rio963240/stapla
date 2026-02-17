<?php

namespace App\Jobs;

use App\Services\BackupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunBackupJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly bool $isAuto
    ) {}

    public function handle(BackupService $backupService): void
    {
        $backupService->runBackup($this->isAuto);
    }

    /**
     * バックアップは時間がかかるためタイムアウトを長めに
     */
    public int $timeout = 300;

    /**
     * 失敗時の再試行回数
     */
    public int $tries = 1;
}
