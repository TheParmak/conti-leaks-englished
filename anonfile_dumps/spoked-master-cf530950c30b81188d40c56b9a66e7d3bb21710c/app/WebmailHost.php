<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebmailHost extends Model
{
    protected $table = "webmail_hosts";
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    public static $rules = [
        'name' => 'required'
    ];

    public function scripts(){
        return $this->hasMany(WebmailScript::class, "idhost");
    }

    public function cookies(){
        return $this->hasMany(WebmailCookie::class, "idhost");
    }
}
