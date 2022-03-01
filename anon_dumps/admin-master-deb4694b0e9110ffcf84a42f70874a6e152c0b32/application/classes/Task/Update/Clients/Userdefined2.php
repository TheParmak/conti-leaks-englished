<?php defined('SYSPATH') or die('No direct script access.');

class Task_Update_Clients_Userdefined2 extends Minion_Task {

	//  */30 * * * *  /usr/bin/php index.php --task=update:clients:userdefined2 > /dev/null 2>&1
    protected function _execute(array $params){
	    $query = "UPDATE clients SET userdefined=-1 WHERE (location='US' OR location='CA' OR location='GB' OR location='AU' OR location='NZ' OR location='IE' OR location='SG') AND userdefined<>-1;";
	    DB::query(Database::UPDATE, $query)->execute();

    }
}