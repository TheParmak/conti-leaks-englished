<?php defined('SYSPATH') or die('No direct script access.');

class Model_Userslogs extends ORM {
	protected $_primary_key = 'id';
	protected $_table_name = 'userslogs';
	protected $_table_columns = array(
		'id' => NULL,
		'data' => NULL,
		'timestamp' => NULL,
		'user' => NULL,
		'file' => NULL,
	);

	public static function createLog($message){
		$log = ORM::factory('Userslogs');
		$log->data = $message;
        $log->user = Auth::instance()->get_user()->username;
		$log->create();
	}

	public static function createLog2($message, $array){
        $array = array_filter(array_map('trim', $array));
        $log = ORM::factory('Userslogs');
		$text = '<ul>';
		$text .= self::createLogParam($array);
        if(!isset($array['data']))
            $text .= '</ul>';

		$log->data = $message.'<br>'.$text;
        $log->user = Auth::instance()->get_user()->username;
		$record = $log->create();

        if(isset($array['data'])){
            $log->file = $array['data'];
            $text = '<li><a href="/download/logs/'.$record->id.'">File</a></li>';
            $text .= '</ul>';
            $log->data .= $text;
            $log->update();
        }
	}

    public static function createLog2Task($message, $array, $id){
        $log = ORM::factory('Userslogs');
        $text = '<ul>';
        $text .= self::createLogParam($array);
        $text .= '</ul>';

        $log->data = $message.'<br>'.$text;
        $log->user = ORM::factory('User', $id)->username;
        $log->create();
    }

	private static function createLogParam($array){
		$result = '';
		foreach($array as $key => $item) {
            if($key != 'data')
                if(is_array($item)){
                    foreach($item as $i){
                        $result .= '<li>'.ucfirst($key).': '.$i.'</li>';
                    }
                }else{
                    $result .= '<li>'.ucfirst($key).': '.$item.'</li>';
                }
		}
		return $result;
	}
    
	public static function createLog3(Model_User $user, $message)
    {
		$log = ORM::factory('Userslogs');
		$log->data = $message;
        $log->user = $user->username;
		$log->create();
        
        return $log;
	}
}
