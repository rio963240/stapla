<?php

namespace App\Models;

class BackupFile extends BaseModel
{
    protected $table = 'backup_files';
    protected $primaryKey = 'backup_files_id';

    const UPDATED_AT = null;      // updated_at 無し
    public $timestamps = true;    // created_at はあるので true
}
