<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineAccount extends BaseModel
{
    protected $table = 'line_accounts';
    protected $primaryKey = 'line_accounts_id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'line_user_id',
        'line_link_token',
        'is_linked',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
