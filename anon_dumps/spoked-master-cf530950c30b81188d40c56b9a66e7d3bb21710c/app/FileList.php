<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FileList extends Model{

    public $timestamps = false;
    protected $table = 'files_list';
    protected $fillable = ['name'];

    public function files(){
        return $this->hasMany(File::class);
    }

    public static $rules = [
        'name' => 'required',
    ];

    public static function attributes(){
        return [
            'name' => 'Name',
        ];
    }

    public function getNameAttribute($value){
        return trim($value);
    }
}
