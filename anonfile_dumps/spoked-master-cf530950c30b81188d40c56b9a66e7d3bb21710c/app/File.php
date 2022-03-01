<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model{

    public $timestamps = false;
    protected $table = 'files';
    protected $fillable = ['name', 'file_list_id', 'data'];

    public function file_list(){
        return $this->belongsTo(FileList::class);
    }

    public function getNameAttribute($value){
        return trim($value);
    }

    public function validateFileName($attribute, $value, $parameters, $validator){
        foreach ($value as $file){
            if(!preg_match('#^[\w\d-_\.]+$#', $file->getClientOriginalName())){
                return false;
            }
        }
        return true;
    }

    public static $rules = [
        'file' => 'required|file_name',
    ];
}
