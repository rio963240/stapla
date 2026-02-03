<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNoStudyDay extends BaseModel
{
    protected $table = 'user_no_study_days';
    protected $primaryKey = 'user_no_study_days_id';
    public $timestamps = false; // ←あなたのテーブルは timestamps 無し

    protected $casts = [
        'no_study_day' => 'date',
    ];

    public function target(): BelongsTo
    {
        return $this->belongsTo(UserQualificationTarget::class, 'user_qualification_targets_id', 'user_qualification_targets_id');
    }
}
