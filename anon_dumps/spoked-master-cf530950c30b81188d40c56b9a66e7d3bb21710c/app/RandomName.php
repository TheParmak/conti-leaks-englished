<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RandomName extends Model{

    public $timestamps = false;
    protected $table = 'random_name';
    protected $fillable = ['email_id', 'value'];
}
