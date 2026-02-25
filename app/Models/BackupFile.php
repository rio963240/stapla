<?php

namespace App\Models;

class BackupFile extends BaseModel
{
    protected $table = 'backup_files';
    protected $primaryKey = 'backup_files_id';

    const UPDATED_AT = null;
    public $timestamps = true;    // created_at はあるので true
}
