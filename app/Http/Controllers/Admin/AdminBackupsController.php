<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminBackupSettingsRequest;
use App\Models\BackupFile;
use App\Models\BackupSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;

class AdminBackupsController extends Controller
{
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
        ]);
    }

    public function storeManual(Request $request): JsonResponse
    {
        // 手動バックアップ作成
        return $this->createBackup(false);
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

    private function createBackup(bool $isAuto): JsonResponse
    {
        // 保存先ディスクとファイル名を決定
        $diskName = config('backup.disk', 'local');
        $backupPath = trim(config('backup.path', 'backups'), '/');
        $disk = Storage::disk($diskName);

        $timestamp = now()->format('Ymd_His');
        $fileName = "backup_{$timestamp}.sql";
        $relativePath = $backupPath . '/' . $fileName;

        // 一時ファイルの保存先を準備
        $tempDir = storage_path('app/private/backup-temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        $tempPath = $tempDir . '/' . $fileName;

        $success = false;
        $message = 'バックアップに失敗しました';
        $errorDetail = null;
        $size = 0;

        try {
            // DBダンプを作成し、ストレージへ保存
            $this->dumpDatabase($tempPath);
            $size = filesize($tempPath) ?: 0;

            $stream = fopen($tempPath, 'r');
            if (!$stream) {
                throw new RuntimeException('バックアップファイルの読み込みに失敗しました');
            }
            $disk->put($relativePath, $stream);
            fclose($stream);

            $success = true;
            $message = 'バックアップを作成しました';
        } catch (Throwable $exception) {
            $success = false;
            $message = $exception->getMessage() ?: 'バックアップに失敗しました';
            $errorDetail = $exception->getMessage();
        } finally {
            // 一時ファイルを削除
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
        }

        // バックアップ履歴を保存
        $backup = new BackupFile();
        $backup->is_auto = $isAuto;
        $backup->file_name = $success ? $fileName : '-';
        $backup->file_path = $success ? $relativePath : '';
        $backup->is_success = $success;
        $backup->size = $success ? $size : 0;
        $backup->save();

        return response()->json([
            'status' => $success ? 'success' : 'error',
            'message' => $message,
            'error' => $errorDetail,
            'item' => $this->formatBackupItem($backup),
            'latest_at' => $backup->created_at?->format('Y/m/d H:i'),
        ], $success ? 200 : 500);
    }

    private function dumpDatabase(string $outputPath): void
    {
        // 接続ドライバに応じてダンプ方法を分岐
        $connection = config('database.default');
        $config = config("database.connections.{$connection}", []);
        $driver = $config['driver'] ?? '';

        if ($driver === 'sqlite') {
            // SQLiteはDBファイルをコピー
            $databasePath = $config['database'] ?? null;
            if (!$databasePath || !file_exists($databasePath)) {
                throw new RuntimeException('SQLiteのデータベースファイルが見つかりません');
            }
            if (!copy($databasePath, $outputPath)) {
                throw new RuntimeException('SQLiteバックアップのコピーに失敗しました');
            }
            return;
        }

        if ($driver === 'pgsql') {
            // PostgreSQLはpg_dumpでエクスポート
            $host = $config['host'] ?? '127.0.0.1';
            $port = $config['port'] ?? 5432;
            $username = $config['username'] ?? '';
            $password = $config['password'] ?? '';
            $database = $config['database'] ?? '';

            if ($database === '') {
                throw new RuntimeException('データベース名が未設定です');
            }

            // pg_dumpのパス解決
            $pgdump = config('backup.pgdump_path', 'pg_dump');
            if (!is_executable($pgdump)) {
                $resolved = $this->resolveBinary('pg_dump');
                if ($resolved) {
                    $pgdump = $resolved;
                } elseif ($pgdump !== 'pg_dump') {
                    throw new RuntimeException("pg_dumpが見つかりません。BACKUP_PGDUMP_PATH={$pgdump} を確認してください。");
                } else {
                    throw new RuntimeException('pg_dumpが見つかりません。PostgreSQLクライアントをインストールしてください。');
                }
            }
            // pg_dumpの実行コマンド
            $command = [
                $pgdump,
                '--format=plain',
                '--no-owner',
                '--no-privileges',
                "--host={$host}",
                "--port={$port}",
                "--username={$username}",
                "--file={$outputPath}",
                $database,
            ];

            $env = [];
            if ($password !== '') {
                $env['PGPASSWORD'] = $password;
            }

            // 実行とエラーチェック
            $process = new Process($command, null, $env, null, 120);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new RuntimeException($process->getErrorOutput() ?: 'pg_dumpに失敗しました');
            }
            return;
        }

        // MySQL/MariaDB以外は未対応
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            throw new RuntimeException("このDBドライバ({$driver})はバックアップに未対応です");
        }

        // MySQL/MariaDBはmysqldumpでエクスポート
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? 3306;
        $username = $config['username'] ?? '';
        $password = $config['password'] ?? '';
        $database = $config['database'] ?? '';

        if ($database === '') {
            throw new RuntimeException('データベース名が未設定です');
        }

        $mysqldump = config('backup.mysqldump_path', 'mysqldump');
        // mysqldumpの実行コマンド
        $command = [
            $mysqldump,
            '--single-transaction',
            '--quick',
            '--lock-tables=false',
            "--host={$host}",
            "--port={$port}",
            "--user={$username}",
            "--result-file={$outputPath}",
            $database,
        ];

        $env = [];
        if ($password !== '') {
            $env['MYSQL_PWD'] = $password;
        }

        // 実行とエラーチェック
        $process = new Process($command, null, $env, null, 120);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException($process->getErrorOutput() ?: 'mysqldumpに失敗しました');
        }
    }

    private function formatBackupItem(BackupFile $file): array
    {
        // 一覧表示用の整形
        return [
            'created_at' => $file->created_at?->format('Y/m/d H:i'),
            'type_label' => $file->is_auto ? '自動' : '手動',
            'status_label' => $file->is_success ? '成功' : '失敗',
            'status_key' => $file->is_success ? 'success' : 'failed',
            'file_name' => $file->file_name ?: '-',
            'size_label' => $file->size ? $this->formatBytes($file->size) : '-',
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

    private function resolveBinary(string $binary): ?string
    {
        // 実行ファイルのパスを解決
        $process = Process::fromShellCommandline('command -v ' . escapeshellarg($binary));
        $process->run();
        if (!$process->isSuccessful()) {
            return null;
        }
        $path = trim($process->getOutput());
        return $path !== '' ? $path : null;
    }
}
