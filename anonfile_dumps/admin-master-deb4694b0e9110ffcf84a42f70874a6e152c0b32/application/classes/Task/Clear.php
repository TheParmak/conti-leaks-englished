<?php defined('SYSPATH') or die('No direct script access.');

class Task_Clear extends Minion_Task {
    protected function _execute(array $params){
	    $tableWithClient = array();
	    $tables = $this->getTables();
	    foreach ($tables as $t) {
		    if($this->getTablesWithClient($t)){
			    $tableWithClient[] = $t;
		    }
	    }

	    $forClear = [
//		    '%\_W21____',
//		    'PLACEHOL%',
		    'JOHN\-PC%',
//		    '\_\w\d*',
//		    '\_[W,A]\d*'
	    ];

	    $op = 'LIKE';
//	    $op = '!~';

	    foreach($forClear as $clear){
		    $clients = $this->getClients($clear, $op);

		    foreach($clients as $c){
			    foreach($tableWithClient as $t){
				    $this->delete($t, $c);
			    }
		    }
	    }
    }

	private function delete($table, $client){
		DB::delete($table)
			->where('clientid', '=', $client)
			->execute();
	}

	private function getClients($clear, $operator){
		return DB::select('clientid')
			->from('clients')
			->where('prefix', $operator, $clear)
			->execute()
			->as_array(NULL, 'clientid');
	}

	private function getTablesWithClient($table){
		$columns = $this->getColumns($table);
		foreach($columns as $c){
			if($c == 'clientid'){
				return true;
			}
		}

		return false;
	}

	private function getTables(){
		$query = "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public';";
		return DB::query(Database::SELECT, $query)
			->execute()
			->as_array(NULL, 'table_name');
	}

	private function getColumns($table){
		$query = "SELECT column_name FROM information_schema.columns WHERE table_name ='".$table."';";
		return DB::query(Database::SELECT, $query)
			->execute()
			->as_array(NULL, 'column_name');
	}
}