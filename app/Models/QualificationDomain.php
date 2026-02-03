<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QualificationDomain extends BaseModel
{
    protected $table = 'qualification_domains';
    protected $primaryKey = 'qualification_domains_id';
    public $timestamps = true;
    protected $fillable = [
        'qualification_id',
        'name',
        'is_active',
    ];

    public function qualification(): BelongsTo
    {
        return $this->belongsTo(Qualification::class, 'qualification_id', 'qualification_id');
    }

    public function subdomains(): HasMany
    {
        return $this->hasMany(QualificationSubdomain::class, 'qualification_domains_id', 'qualification_domains_id');
    }
}
