<?php

namespace App\Jobs;

use App\Services\BackupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * バックアップ非同期実行ジョブ
 *
 * backup.async が true のとき、手動バックアップ・再実行はこのジョブに投入され、
 * HTTP は即座にレスポンスを返す。Render 等でリクエストタイムアウトを避けるため。
 *
 * キューは config('queue.default')（通常 database）を使用。
 * Render で動かす場合は Background Worker で php artisan queue:work を実行するか、
 * Web コンテナの CMD で apache と queue:work を両方起動する必要がある。
 */
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
