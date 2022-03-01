<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * App\Task
 *
 * @property int $id
 * @property string $name
 * @property string $smtp_path
 * @property string $email_list
 * @property string $email_id
 * @method static \Illuminate\Database\Query\Builder|\App\Task whereEmailId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Task whereEmailList($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Task whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Task whereName($value)
 * @mixin \Eloquent
 */
class Task extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'email_id', 'email_list', 'test_email'];
    protected $guarded = ['id'];

    public static $rules = [
        'name' => 'required',
        'email_list' => 'required_without_all:test_email',
        'email_id' => 'required',
        'test_email' => 'required_without_all:email_list',
    ];

    public static function attributes(){
        return [
            'name' => 'Name',
            'email_list' => 'EmailList',
            'email_id' => 'Email',
            'test_email' => 'TestEmail',
        ];
    }

    /**
     * @return bool|null
     */
    public static function getBackEndStatus(){
        if(Storage::exists('sendmail_server.pid')){
            $pid = trim(Storage::get('sendmail_server.pid'));
            exec("ps -p ".$pid." --no-headers", $daemon);
            if(!empty($daemon)){
                return true;
            }else{
                exec("pgrep make", $pids);
                if(!empty($pids)){
                    foreach ($pids as $pid){
                        exec("ps -p ".$pid." -o args | grep make", $make);
                        if(preg_match('#resolve#', $make[0])){
                            return false;
                        }
                        unset($make);
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param Int $id
     * @param String $name
     * @param String $file
     * @return null
     */
    public static function getResult($id, $name, $file = 'result.json'){
        if(Storage::exists($file) && isset(json_decode(Storage::get($file), true)[$id])){
            return json_decode(Storage::get($file), true)[$id][$name];
        }

        return null;
    }
}
