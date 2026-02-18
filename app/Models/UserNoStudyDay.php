<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNoStudyDay extends BaseModel
{
    protected $table = 'user_no_study_days';
    protected $primaryKey = 'user_no_study_days_id';
    public $timestamps = false;
    // 勉強不可日の登録に使用
    protected $fillable = [
        'user_qualification_targets_id',
        'no_study_day',
    ];

    protected $casts = [
        'no_study_day' => 'date',
    ];

    public function target(): BelongsTo
    {
        return $this->belongsTo(UserQualificationTarget::class, 'user_qualification_targets_id', 'user_qualification_targets_id');
    }
}
