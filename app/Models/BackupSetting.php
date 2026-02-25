<?php

namespace App\Models;

class BackupSetting extends BaseModel
{
    protected $guarded = [];
    protected $table = 'backup_settings';
    protected $primaryKey = 'backup_settings_id';
    public $timestamps = true;

    protected $casts = [
        'is_enabled' => 'boolean',
        'run_time'   => 'datetime:H:i:s',
    ];
}
