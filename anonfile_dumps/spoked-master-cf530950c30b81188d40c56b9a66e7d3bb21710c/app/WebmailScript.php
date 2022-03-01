<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebmailScript extends Model
{
    protected $table = "webmail_scripts";
    public $timestamps = false;

    protected $fillable = [
        'idtype',
        'idhost',
        'script'
    ];

    public static $rules = [
        'idtype' => 'required',
        'idhost' => 'required'
    ];
}
