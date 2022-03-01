<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebmailCookie extends Model
{
    protected $table = "webmail_cookies";
    public $timestamps = false;

    protected $fillable = [
        'idhost',
        'name',
        'domain'
    ];

    public static $rules = [
        'idhost' => 'required',
        'name' => 'required'
    ];
}
