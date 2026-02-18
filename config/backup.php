<?php

/**
 * バックアップ設定
 *
 * データベースダンプの保存先・保持期間・非同期実行の有無などを定義する。
 * 本番（Render + Neon + R2）では BACKUP_DISK=s3, BACKUP_PATH=backups, BACKUP_RETENTION_DAYS=31 を推奨。
 *
 * 主な環境変数:
 * - BACKUP_DISK: 保存先ディスク。local=storage/app/private, s3=Cloudflare R2（filesystems.php の s3）
 * - BACKUP_PATH: ディスク内のパス（例: backups → バケット内 backups/ に保存）
 * - BACKUP_ASYNC: true にすると手動バックアップをキュー投入し即レスポンス（Render 等でタイムアウト回避）。Background Worker 必須
 * - BACKUP_RETENTION_DAYS: この日数より古い成功バックアップを自動削除（DB とストレージ両方）。0 で無効
 * - BACKUP_PGDUMP_PATH / BACKUP_MYSQLDUMP_PATH: コマンドのパス（Docker では postgresql-client で pg_dump が入る）
 */
return [
    'disk' => env('BACKUP_DISK', 'local'),
    'async' => env('BACKUP_ASYNC', false),
    'path' => env('BACKUP_PATH', 'backups'),
    'retention_days' => (int) env('BACKUP_RETENTION_DAYS', 31),
    'mysqldump_path' => env('BACKUP_MYSQLDUMP_PATH', 'mysqldump'),
    'pgdump_path' => env('BACKUP_PGDUMP_PATH', 'pg_dump'),
];
