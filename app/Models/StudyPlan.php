<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyPlan extends BaseModel
{
    protected $table = 'study_plans';
    protected $primaryKey = 'study_plans_id';
    public $timestamps = true;

    protected $fillable = [
        'user_qualification_targets_id',
        'version',
        'is_active',
    ];

    public function target(): BelongsTo
    {
        return $this->belongsTo(UserQualificationTarget::class, 'user_qualification_targets_id', 'user_qualification_targets_id');
    }

    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class, 'study_plans_id', 'study_plans_id');
    }
}
