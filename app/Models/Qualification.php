<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Qualification extends BaseModel
{
    protected $table = 'qualification';
    protected $primaryKey = 'qualification_id';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'is_active',
    ];

    public function domains(): HasMany
    {
        return $this->hasMany(QualificationDomain::class, 'qualification_id', 'qualification_id');
    }

    public function targets(): HasMany
    {
        return $this->hasMany(UserQualificationTarget::class, 'qualification_id', 'qualification_id');
    }
}
