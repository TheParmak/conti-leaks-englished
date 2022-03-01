<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Macro extends Model
{
    public $timestamps = false;
    protected $table = 'macros';
    protected $fillable = [
        'name',
        'value',
    ];

    public static $rules = [
        'name' => 'required',
        'value' => 'required',
    ];

    public static function attributes(){
        return [
            'name' => 'Name',
            'value' => 'Value',
        ];
    }

    public function getValueAttribute($value){
        if(env('DB_CONNECTION') == 'mysql') {
            return $value;
        }else{
            return pg_unescape_bytea($value);
        }
    }

    public function setValueAttribute($value){
        if(env('DB_CONNECTION') == 'mysql') {
            $this->attributes['value'] = $value;
        }else{
            $this->attributes['value'] = pg_escape_bytea($value);
        }
    }
}
