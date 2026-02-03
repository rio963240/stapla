<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineAccount extends BaseModel
{
    protected $table = 'line_accounts';
    protected $primaryKey = 'line_accounts_id';
    public $timestamps = true;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
