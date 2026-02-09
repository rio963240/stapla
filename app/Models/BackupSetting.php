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
        'run_time'   => 'datetime:H:i:s', // time型を表示整形したい場合（好み）
    ];
}
