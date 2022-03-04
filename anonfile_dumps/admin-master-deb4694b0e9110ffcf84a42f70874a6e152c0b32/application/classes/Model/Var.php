<?php defined('SYSPATH') or die('No direct script access.');

class Model_Var extends ORM {
	protected $_primary_key = 'id';
	protected $_table_name = 'storage';
	protected $_table_columns = [
		'id' => NULL,
		'client_id' => NULL,
		'key' => NULL,
		'value' => NULL,
		'updated_at' => NULL,
	];

	protected $_belongs_to = [
		'client' => [
			'model' => 'Client',
			'foreign_key' => 'client_id',
		]
	];

    // TODO argAll from model Vars not used, need know how create query
	public function getClientVars($argClient, $argAll = null){
        return DB::select()
            ->from('storage')
            ->where('client_id', '=', $argClient)
            ->execute()
            ->as_array();
	}

	public function getArrayClientVars(){
		$result = array();

		$systems = DB::select('system')
			->from('clients')
			->distinct(true)
			->execute();

		foreach($systems as $s){
			$system = $s['system'];
			$clients = DB::select()
				->from('clients')
				->where('system', '=', $system)
				->execute();

			if(!isset($result[$system])){
				$result[$system] = 0;
			}
			foreach($clients as $c){
				$result[$system] += count($this->getClientVars($c['client'], false));
			}
		}
		return $result;
	}
}
