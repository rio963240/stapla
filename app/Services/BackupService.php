<?php

namespace App\Services;

use App\Models\BackupFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;


class BackupService
{
    /**
     * バックアップを 1 回実行する（手動 or 自動）
     *
     * @param bool $isAuto true=自動バックアップ（スケジュール）、false=手動
     * @return array{success: bool, message: string, error: ?string, backup: BackupFile}
     */
    public function runBackup(bool $isAuto): array
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

        // 期限切れのバックアップを削除
        $this->deleteExpiredBackups();

        return [
            'success' => $success,
            'message' => $message,
            'error' => $errorDetail,
            'backup' => $backup,
        ];
    }

    /**
     * 保持期間を過ぎたバックアップを削除する
     *
     * backup.retention_days（デフォルト 31 日）より前に作成された成功バックアップについて、
     * ストレージ上のファイル（R2/S3 含む）を削除してから backup_files レコードを削除する。
     * R2 は S3 互換のため Storage::disk('s3')->delete($path) でオブジェクトが確実に削除される。
     */
    private function deleteExpiredBackups(): void
    {
        // 保持期間設定に従い、古い成功バックアップを削除
        $retentionDays = config('backup.retention_days', 31);
        if ($retentionDays < 1) {
            return;
        }

        $expiredAt = now()->subDays($retentionDays);
        $expiredFiles = BackupFile::query()
            ->where('created_at', '<', $expiredAt)
            ->where('is_success', true)
            ->whereNotNull('file_path')
            ->where('file_path', '!=', '')
            ->get();

        $diskName = config('backup.disk', 'local');
        $disk = Storage::disk($diskName);

        // ストレージとDBのレコードを削除
        foreach ($expiredFiles as $file) {
            if ($file->file_path && $disk->exists($file->file_path)) {
                $disk->delete($file->file_path);
            }
            $file->delete();
        }
    }

    /**
     * 現在の DB 接続でダンプを取得し、指定パスに書き出す
     *
     * - sqlite: ファイルコピー
     * - pgsql: pg_dump（DATABASE_URL があればパースして host/port/user/pass/dbname/sslmode を使用。Neon は sslmode=require）
     * - mysql/mariadb: mysqldump
     */
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
            $sslmode = $config['sslmode'] ?? null;

            // DATABASE_URL のみ設定している環境での接続情報を補完
            $url = $config['url'] ?? env('DATABASE_URL');
            if ($url) {
                $parsed = parse_url($url);
                if ($parsed !== false) {
                    $host = $parsed['host'] ?? $host;
                    $port = $parsed['port'] ?? 5432;
                    $username = $parsed['user'] ?? $username;
                    $password = $parsed['pass'] ?? $password;
                    $database = trim($parsed['path'] ?? '', '/') ?: $database;
                    $query = $parsed['query'] ?? '';
                    parse_str($query, $params);
                    $sslmode = $params['sslmode'] ?? $sslmode;
                }
            }

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
            if ($sslmode !== null && $sslmode !== '') {
                $env['PGSSLMODE'] = $sslmode;
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

    /** PATH から実行可能ファイルのフルパスを取得（pg_dump 等が which で見つからない場合のフォールバック） */
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
