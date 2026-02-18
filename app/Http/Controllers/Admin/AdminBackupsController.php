<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminBackupSettingsRequest;
use App\Jobs\RunBackupJob;
use App\Models\BackupFile;
use App\Models\BackupSetting;
use App\Services\BackupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * 管理画面：バックアップ
 *
 * - index: バックアップ一覧・手動/自動設定の表示。backup_settings（1件）と backup_files（直近50件）を取得
 * - storeManual: 手動バックアップ。backup.async が true のときは RunBackupJob をディスパッチして即返す
 * - list: 一覧の JSON（非同期時のポーリング用）
 * - updateSettings: 自動バックアップの ON/OFF と実行時刻を保存
 * - retry: 失敗バックアップの再実行（同様に async の場合はキュー投入）
 * - download: 成功したバックアップのファイルを config('backup.disk') からストリーム返却（R2 の場合は readStream）
 * - destroy: ストレージ上のファイルを削除してから backup_files レコードを削除（R2 も Storage::disk()->delete で削除される）
 */
class AdminBackupsController extends Controller
{
    private BackupService $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    public function index()
    {
        // 設定と最新バックアップ一覧を取得して画面表示
        $setting = BackupSetting::firstOrCreate(['settings_key' => 'default']);
        $backupFiles = BackupFile::query()
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        $backupItems = $backupFiles->map(fn (BackupFile $file) => $this->formatBackupItem($file))->all();
        $latestBackupAt = $backupFiles->first()?->created_at?->format('Y/m/d H:i');
        $storageUsageBytes = (int) $backupFiles->where('is_success', true)->sum('size');
        $storageUsageLabel = $this->formatBytes($storageUsageBytes);

        return view('admin.backups', [
            'backupItems' => $backupItems,
            'setting' => [
                'is_enabled' => (bool) $setting->is_enabled,
                'run_time' => $setting->run_time
                    ? Carbon::parse($setting->run_time)->format('H:i')
                    : '02:00',
                'frequency' => $setting->frequency ?? 'daily',
            ],
            'latestBackupAt' => $latestBackupAt,
            'storageUsageLabel' => $storageUsageLabel,
        ]);
    }

    public function storeManual(Request $request): JsonResponse
    {
        if (config('backup.async', false)) {
            RunBackupJob::dispatch(false);

            return response()->json([
                'status' => 'success',
                'message' => 'バックアップを開始しました。数十秒後にページを更新して結果を確認してください。',
                'async' => true,
            ]);
        }

        $result = $this->backupService->runBackup(false);

        return $this->buildBackupResponse($result);
    }

    public function updateSettings(AdminBackupSettingsRequest $request): JsonResponse
    {
        // 自動バックアップ設定を更新
        $data = $request->validated();

        $setting = BackupSetting::firstOrCreate(['settings_key' => 'default']);
        $setting->is_enabled = (bool) $data['is_enabled'];
        $setting->run_time = $data['run_time'] . ':00';
        $setting->save();

        return response()->json([
            'status' => 'success',
            'message' => '自動バックアップ設定を保存しました',
            'setting' => [
                'is_enabled' => (bool) $setting->is_enabled,
                'run_time' => Carbon::parse($setting->run_time)->format('H:i'),
            ],
        ]);
    }

    public function retry(BackupFile $backupFile): JsonResponse
    {
        if (config('backup.async', false)) {
            RunBackupJob::dispatch((bool) $backupFile->is_auto);

            return response()->json([
                'status' => 'success',
                'message' => 'バックアップを再実行しました。数十秒後にページを更新して結果を確認してください。',
                'async' => true,
            ]);
        }

        $result = $this->backupService->runBackup((bool) $backupFile->is_auto);

        return $this->buildBackupResponse($result);
    }

    public function list(Request $request): JsonResponse
    {
        $backupFiles = BackupFile::query()
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $backupItems = $backupFiles->map(fn (BackupFile $file) => $this->formatBackupItem($file))->all();
        $latestBackupAt = $backupFiles->first()?->created_at?->format('Y/m/d H:i');

        return response()->json([
            'backupItems' => $backupItems,
            'latestBackupAt' => $latestBackupAt,
        ]);
    }

    public function download(BackupFile $backupFile)
    {
        if (!$backupFile->is_success || !$backupFile->file_path) {
            abort(404);
        }

        $diskName = config('backup.disk', 'local');
        $disk = Storage::disk($diskName);

        if (!$disk->exists($backupFile->file_path)) {
            abort(404);
        }

        $stream = $disk->readStream($backupFile->file_path);
        if ($stream === false) {
            abort(404);
        }

        return response()->streamDownload(
            fn () => fpassthru($stream),
            $backupFile->file_name
        );
    }

    public function destroy(BackupFile $backupFile): JsonResponse
    {
        $diskName = config('backup.disk', 'local');
        $disk = Storage::disk($diskName);

        if ($backupFile->file_path && $disk->exists($backupFile->file_path)) {
            if (!$disk->delete($backupFile->file_path)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'バックアップファイルの削除に失敗しました',
                ], 500);
            }
        }

        $backupFile->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'バックアップを削除しました',
        ]);
    }

    private function formatBackupItem(BackupFile $file): array
    {
        // 一覧表示用の整形
        return [
            'id' => $file->backup_files_id,
            'created_at' => $file->created_at?->format('Y/m/d H:i'),
            'type_label' => $file->is_auto ? '自動' : '手動',
            'status_label' => $file->is_success ? '成功' : '失敗',
            'status_key' => $file->is_success ? 'success' : 'failed',
            'file_name' => $file->file_name ?: '-',
            'size_label' => $file->size ? $this->formatBytes($file->size) : '-',
            'download_url' => $file->is_success
                ? route('admin.backups.download', $file)
                : null,
            'delete_url' => route('admin.backups.destroy', $file),
            'retry_url' => route('admin.backups.retry', $file),
        ];
    }

    private function formatBytes(int $bytes): string
    {
        // バイト数を見やすい単位へ変換
        if ($bytes < 1024) {
            return $bytes . 'B';
        }
        if ($bytes < 1024 * 1024) {
            return number_format($bytes / 1024, 1) . 'KB';
        }
        if ($bytes < 1024 * 1024 * 1024) {
            return number_format($bytes / (1024 * 1024), 1) . 'MB';
        }
        return number_format($bytes / (1024 * 1024 * 1024), 2) . 'GB';
    }

    private function buildBackupResponse(array $result): JsonResponse
    {
        $backup = $result['backup'];
        $success = (bool) $result['success'];

        return response()->json([
            'status' => $success ? 'success' : 'error',
            'message' => $result['message'],
            'error' => $result['error'],
            'item' => $this->formatBackupItem($backup),
            'latest_at' => $backup->created_at?->format('Y/m/d H:i'),
        ], $success ? 200 : 500);
    }
}
