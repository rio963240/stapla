<?php

namespace App\Services;

use App\Models\BackupFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;

class BackupService
{
    public function runBackup(bool $isAuto): array
    {
        $diskName = config('backup.disk', 'local');
        $backupPath = trim(config('backup.path', 'backups'), '/');
        $disk = Storage::disk($diskName);

        $timestamp = now()->format('Ymd_His');
        $fileName = "backup_{$timestamp}.sql";
        $relativePath = $backupPath . '/' . $fileName;

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
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
        }

        $backup = new BackupFile();
        $backup->is_auto = $isAuto;
        $backup->file_name = $success ? $fileName : '-';
        $backup->file_path = $success ? $relativePath : '';
        $backup->is_success = $success;
        $backup->size = $success ? $size : 0;
        $backup->save();

        return [
            'success' => $success,
            'message' => $message,
            'error' => $errorDetail,
            'backup' => $backup,
        ];
    }

    private function dumpDatabase(string $outputPath): void
    {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}", []);
        $driver = $config['driver'] ?? '';

        if ($driver === 'sqlite') {
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
            $host = $config['host'] ?? '127.0.0.1';
            $port = $config['port'] ?? 5432;
            $username = $config['username'] ?? '';
            $password = $config['password'] ?? '';
            $database = $config['database'] ?? '';

            if ($database === '') {
                throw new RuntimeException('データベース名が未設定です');
            }

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

            $process = new Process($command, null, $env, null, 120);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new RuntimeException($process->getErrorOutput() ?: 'pg_dumpに失敗しました');
            }
            return;
        }

        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            throw new RuntimeException("このDBドライバ({$driver})はバックアップに未対応です");
        }

        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? 3306;
        $username = $config['username'] ?? '';
        $password = $config['password'] ?? '';
        $database = $config['database'] ?? '';

        if ($database === '') {
            throw new RuntimeException('データベース名が未設定です');
        }

        $mysqldump = config('backup.mysqldump_path', 'mysqldump');
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

        $process = new Process($command, null, $env, null, 120);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException($process->getErrorOutput() ?: 'mysqldumpに失敗しました');
        }
    }

    private function resolveBinary(string $binary): ?string
    {
        $process = Process::fromShellCommandline('command -v ' . escapeshellarg($binary));
        $process->run();
        if (!$process->isSuccessful()) {
            return null;
        }
        $path = trim($process->getOutput());
        return $path !== '' ? $path : null;
    }
}
