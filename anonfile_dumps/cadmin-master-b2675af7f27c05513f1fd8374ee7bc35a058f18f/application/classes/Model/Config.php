<?php defined('SYSPATH') or die('No direct script access.');

class Model_Config extends ORM {
    protected $errors;
    protected $clientID;
	protected $_table_name = 'configs';
	protected $_table_columns = [
		'id' => NULL,
        'version' => NULL,
        'data' => NULL,
        'group' => NULL,
        'sys_ver' => NULL,
        'client_id' => NULL,
		'created_at' => NULL,
		'country' => NULL,
	];

    protected $_belongs_to = [
        'client' => [
            'model' => 'Client',
            'foreign_key' => 'client_id'
        ]
    ];

    public function getErrors(){ return $this->errors; }

	public function getConfigs(){
		$query = "SELECT getconfigs()";
		$query = DB::query(Database::SELECT, $query);
		return $query->execute();
	}

	public function addConfig($argConfig){
        $data = Arr::extract($_POST, [
            'client_id',
            'group',
            'sys_ver',
            'country',
            'version',
            'userdefined_low',
            'userdefined_high',
        ]);
        $data['data'] = $argConfig;

        $validation = Validation::factory($data)
            ->label('client_id', 'ClientID')
            ->rule('client_id', 'not_empty')
            ->rule('client_id', [$this, 'check_client'])

            ->label('group', 'Group')
            ->rule('group', 'not_empty')

            ->label('sys_ver', 'System')
            ->rule('sys_ver', 'not_empty')

            ->label('country', 'Country')
            ->rule('country', 'not_empty')

            ->label('userdefined_low', 'User-defined Low')
            ->rule('userdefined_low', 'not_empty')
            ->label('userdefined_high', 'User-defined High')
            ->rule('userdefined_high', 'not_empty')

            ->label('version', 'Version')
            ->rule('version', 'not_empty')
            ->rule('version', 'numeric');

        if( $validation->check() ){
            $data['client_id'] = $this->clientID;
            DB::insert('configs', array_keys($data))->values($data)->execute();
            ORM::factory('Userslogs')->createLog2('Add config', $data);
        }else{
            $this->errors = $validation->errors("validation");
        }
	}

    public function check_client($ClientID){
        if(ctype_digit($ClientID)){
            $this->clientID = $ClientID;
            return true;
        }else{
            if ( preg_match('/^.*\.([0-9A-F]{32})$/i', $ClientID, $matches) ) {
                $ClientID = $matches[1];
            }
            if ( ! preg_match('/^[0-9A-F]{32}$/i', $ClientID) ) {
                return false;
            }

            $this->clientID = Model::factory('Client')->getClientIDByName($ClientID);

            if($this->clientID == '0'){
                return false;
            }else{
                return true;
            }
        }
    }
}
