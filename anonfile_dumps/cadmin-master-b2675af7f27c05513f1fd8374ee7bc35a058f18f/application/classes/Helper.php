<?php defined('SYSPATH') or die('No direct script access.');

class Helper{

    public static function GetFile($file_path){
        $handle = fopen($file_path, 'rb'); // read as binary safe
        $file = fread($handle, filesize($file_path));
        fclose($handle);
        return $file;
    }

    public static function pagination($obj){
        $obj->reset(FALSE);
        $total_items = $obj->count_all();
        return Pagination::factory(['total_items' => $total_items]);
    }

    public static function checkActionInRole($actionForCheck)
    {
        return Auth::instance()->get_user()->hasAction($actionForCheck);
    }

    public static function getCid($clientName)
    {
        $length = 16; // sizeof(int64) * sizeof(hex) = 8 * 2 = 16
        assert(2 * $length == strlen($clientName));
        
        $cid = strtolower($clientName);
        $cid1 = substr($cid, 0, $length);
        $cid0 = substr($cid, $length, $length);
        
        return [
            0 => DB::expr("x'" . $cid0 . "'::BIGINT"),
            1 => DB::expr("x'" . $cid1 . "'::BIGINT"),
        ];
    }

    public static function time_elapsed_string($datetime, $full = false) {
        $db_now = DB::select(DB::expr("NOW()::timestamp"))->execute()->as_array();
        $now = new DateTime($db_now[0]['now']);
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = [
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    public static function issetAndNotEmpty($array){
        if(isset($array)) {
            if (is_array($array) && count($array)) {
                return true;
            }elseif(!is_array($array) && $array != '') {
                return true;
            }
        }
        return false;
    }

    public static function trimPost($array){
        foreach($array as $k => $v){
            if(is_array($v))
                self::trimPost($v);
            elseif(is_string($v))
                $array[$k] = trim($v);
        }
        return $array;
    }

    public static function check_client($ClientID, $data){
        if($ClientID == '0'){
            return true;
        }else if(intval($ClientID)){
            $data = ORM::factory('Client', intval($ClientID));

            if($data->loaded()){
                return true;
            }else{
                return false;
            }
        }else{
            $data = Model::factory('Client')->getClientIDByName($ClientID);

            if($data == '0'){
                return false;
            }else{
                return true;
            }
        }
    }
}