<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyRecord extends BaseModel
{
    protected $table = 'study_records';
    protected $primaryKey = 'study_records_id';
    public $timestamps = true;

    protected $fillable = [
        'todo_id',
        'study_plan_items_id',
        'actual_minutes',
        'is_completed',
    ];

    public function todo(): BelongsTo
    {
        return $this->belongsTo(Todo::class, 'todo_id', 'todo_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(StudyPlanItem::class, 'study_plan_items_id', 'study_plan_items_id');
    }
}
