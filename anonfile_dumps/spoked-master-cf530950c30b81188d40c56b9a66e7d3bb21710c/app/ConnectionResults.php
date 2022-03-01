<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConnectionResults extends Model
{
    public $timestamps = false;
    protected $table = 'connection_results';
    protected $primaryKey = 'connection_id';

    public function mailAddress(){
        return $this->hasMany(MailAddress::class, 'connection_id', 'connection_id');
    }
}
