<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientsCache extends Model
{
    public $timestamps = false;
    protected $fillable = ['base64', 'last_activity'];
    protected $guarded = ['id'];
    protected $table = 'clients_cache';
}
