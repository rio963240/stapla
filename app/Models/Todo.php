<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Todo extends BaseModel
{
    protected $table = 'todo';
    protected $primaryKey = 'todo_id';
    public $timestamps = true;

    protected $casts = [
        'date' => 'date',
    ];

    protected $fillable = [
        'study_plans_id',
        'date',
        'memo',
    ];

    public function studyPlan(): BelongsTo
    {
        return $this->belongsTo(StudyPlan::class, 'study_plans_id', 'study_plans_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StudyPlanItem::class, 'todo_id', 'todo_id');
    }

    public function records(): HasMany
    {
        return $this->hasMany(StudyRecord::class, 'todo_id', 'todo_id');
    }
}
