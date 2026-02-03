<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubdomainPreference extends BaseModel
{
    protected $table = 'user_subdomain_preferences';
    protected $primaryKey = 'user_subdomain_preferences_id';
    public $timestamps = false; // ←timestamps無し

    public function target(): BelongsTo
    {
        return $this->belongsTo(UserQualificationTarget::class, 'user_qualification_targets_id', 'user_qualification_targets_id');
    }

    public function subdomain(): BelongsTo
    {
        return $this->belongsTo(QualificationSubdomain::class, 'qualification_subdomains_id', 'qualification_subdomains_id');
    }
}
