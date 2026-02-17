<?php

return [
    'disk' => env('BACKUP_DISK', 'local'),
    'async' => env('BACKUP_ASYNC', false),
    'path' => env('BACKUP_PATH', 'backups'),
    'mysqldump_path' => env('BACKUP_MYSQLDUMP_PATH', 'mysqldump'),
    'pgdump_path' => env('BACKUP_PGDUMP_PATH', 'pg_dump'),
];
