<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailsMacros extends Model
{
    public $timestamps = false;
    protected $table = 'emails_macros';
    protected $fillable = ['email_id', 'macros_id'];
}
