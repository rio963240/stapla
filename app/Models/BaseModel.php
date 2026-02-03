<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    /**
     * PK が bigint の場合は基本 string 扱い不要だが、
     * Laravel は int として扱えるので keyType は 'int' でOK。
     */
    protected $keyType = 'int';
    public $incrementing = true;

    // セキュリティ的に guarded で運用が安全
    //protected $guarded = ['*'];
}
