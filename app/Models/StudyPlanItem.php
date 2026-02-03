<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyPlanItem extends BaseModel
{
    protected $table = 'study_plan_items';
    protected $primaryKey = 'study_plan_items_id';
    public $timestamps = false; // ←timestamps無し

    protected $fillable = [
        'todo_id',
        // 分野単位計画で使用
        'qualification_domains_id',
        'qualification_subdomains_id',
        'planned_minutes',
        'status',
    ];

    public function todo(): BelongsTo
    {
        return $this->belongsTo(Todo::class, 'todo_id', 'todo_id');
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(QualificationDomain::class, 'qualification_domains_id', 'qualification_domains_id');
    }

    public function subdomain(): BelongsTo
    {
        return $this->belongsTo(QualificationSubdomain::class, 'qualification_subdomains_id', 'qualification_subdomains_id');
    }

    public function records(): HasMany
    {
        return $this->hasMany(StudyRecord::class, 'study_plan_items_id', 'study_plan_items_id');
    }
}
