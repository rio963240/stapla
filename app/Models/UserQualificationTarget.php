<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserQualificationTarget extends BaseModel
{
    protected $table = 'user_qualification_targets';
    protected $primaryKey = 'user_qualification_targets_id';
    public $timestamps = true;

    protected $casts = [
        'start_date' => 'date',
        'exam_date'  => 'date',
    ];

    protected $fillable = [
        'user_id',
        'qualification_id',
        'start_date',
        'exam_date',
        'daily_study_time',
        'buffer_rate',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function qualification(): BelongsTo
    {
        return $this->belongsTo(Qualification::class, 'qualification_id', 'qualification_id');
    }

    public function noStudyDays(): HasMany
    {
        return $this->hasMany(UserNoStudyDay::class, 'user_qualification_targets_id', 'user_qualification_targets_id');
    }

    public function domainPreferences(): HasMany
    {
        return $this->hasMany(UserDomainPreference::class, 'user_qualification_targets_id', 'user_qualification_targets_id');
    }

    public function subdomainPreferences(): HasMany
    {
        return $this->hasMany(UserSubdomainPreference::class, 'user_qualification_targets_id', 'user_qualification_targets_id');
    }

    public function studyPlans(): HasMany
    {
        return $this->hasMany(StudyPlan::class, 'user_qualification_targets_id', 'user_qualification_targets_id');
    }
}
