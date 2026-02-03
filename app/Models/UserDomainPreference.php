<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDomainPreference extends BaseModel
{
    protected $table = 'user_domain_preferences';
    protected $primaryKey = 'user_domain_preferences_id';
    public $timestamps = false; // ←timestamps無し

    public function target(): BelongsTo
    {
        return $this->belongsTo(UserQualificationTarget::class, 'user_qualification_targets_id', 'user_qualification_targets_id');
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(QualificationDomain::class, 'qualification_domains_id', 'qualification_domains_id');
    }
}
