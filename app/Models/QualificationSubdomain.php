<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QualificationSubdomain extends BaseModel
{
    protected $table = 'qualification_subdomains';
    protected $primaryKey = 'qualification_subdomains_id';
    public $timestamps = true;
    protected $fillable = [
        'qualification_domains_id',
        'name',
        'is_active',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(QualificationDomain::class, 'qualification_domains_id', 'qualification_domains_id');
    }
}
