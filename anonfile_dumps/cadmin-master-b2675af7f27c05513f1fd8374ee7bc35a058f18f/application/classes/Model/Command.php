<?php defined('SYSPATH') or die('No direct script access.');

class Model_Command extends ORM {
	protected $_table_columns = [
		'id' => NULL,
		'client_id' => NULL,
		'incode' => NULL,
		'params' => NULL,
		'result_code' => NULL,
		'created_at' => NULL,
		'resulted_at' => NULL,
	];

	protected $_belongs_to = [
		'client' => [
			'model' => 'Client',
			'foreign_key' => 'client_id',
		]
	];

    protected $errors;
    protected $clientID;
    
    public function getErrors(){
        return $this->errors;
    }
    
	public function clearDb(){
		DB::query(Database::DELETE, "TRUNCATE commands")->execute();
		DB::query(null, "ALTER SEQUENCE commands_id_seq RESTART WITH 1;")->execute();
	}

	public function addCommandWithValidation($data)
    {
        $data = Arr::extract($data, [
            'client_id',
            'incode',
            'params',
        ]);

        $validation = Validation::factory($data)
            ->label('client_id', 'ClientID')
            ->rule('client_id', 'not_empty')
            ->rule('client_id', [$this, 'check_client'])

            ->label('incode', 'Code')
            ->rule('incode', 'not_empty')
            ->rule('incode', 'numeric')

            ->label('params', 'Params')
            ->rule('params', 'not_empty');

        if (!$validation->check()) {
            $data['client_id'] = $this->clientID;
            $data['created_at'] = DB::expr('NOW()');
            $this->errors = $validation->errors('validation');

            return false;
        }

        ORM::factory('Command')->values($data)->save();
        return true;
    }

    public function check_client($ClientID){
        return Helper::check_client($ClientID, $this->clientID);
    }
}
