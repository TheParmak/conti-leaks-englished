<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RandomByte extends Model{

    public $timestamps = false;
    protected $table = 'random_bytes';
    protected $fillable = ['email_id', 'value'];
}
