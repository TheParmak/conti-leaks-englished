<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MailAddress extends Model
{
    public $timestamps = false;
    protected $table = 'mail_address';
}
