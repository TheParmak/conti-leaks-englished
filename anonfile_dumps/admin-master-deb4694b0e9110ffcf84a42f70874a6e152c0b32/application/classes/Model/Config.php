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
        'importance_low' => NULL,
        'importance_high' => NULL,
        'client_id' => NULL,
		'created_at' => NULL,
		'country' => NULL,
        'group_include' => NULL,
        'group_exclude' => NULL,
        'userdefined_low' => NULL,
        'userdefined_high' => NULL,
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
            'sys_ver',
            'country',
            'version',
            'importance_low',
            'importance_high',
            'userdefined_low',
            'userdefined_high',
            'group_include',
            'group_exclude',
        ]);
        $data['data'] = $argConfig;

        $validation = self::dataValidation($data);

        if( $validation->check() ){
            $data = Helper::prepareGroup($data, ['group_exclude', 'group_include']);
            $data['client_id'] = $this->clientID;
            DB::insert('configs', array_keys($data))->values($data)->execute();
            ORM::factory('Userslogs')->createLog2('Add config', $data);
        }else{
            $this->errors = $validation->errors("validation");
        }
	}

    public function updateConfig($data, $id){
        $data = Arr::extract($data, [
            'client_id',
            'sys_ver',
            'country',
            'version',
            'importance_low',
            'importance_high',
            'userdefined_low',
            'userdefined_high',
            'group_include',
            'group_exclude',
        ]);

        $validation = self::dataValidation($data);

        if ( ! $validation->check() ) {
            $this->errors = $validation->errors('validation');
            return false;
        }
        $data = Helper::prepareGroup($data, ['group_exclude', 'group_include']);
        $data['client_id'] = $this->clientID;
        ORM::factory('Config', $id)->values($data)->update();

        return true;
    }

    private function dataValidation($data){
        return Validation::factory($data)
            ->label('client_id', 'ClientID')
            ->rule('client_id', 'not_empty')
            ->rule('client_id', [$this, 'check_client'])

            ->label('sys_ver', 'System')
            ->rule('sys_ver', 'not_empty')

            ->label('country', 'Country')
            ->rule('country', 'not_empty')

            ->label('importance_low', 'Importance Low')
            ->rule('importance_low', 'not_empty')
            ->rule('importance_low', ['Helper', 'check_importance_edit'], [':validation', ':field'])
            ->label('importance_high', 'Importance High')
            ->rule('importance_high', 'not_empty')
            ->rule('importance_high', ['Helper', 'check_importance_edit'], [':validation', ':field'])

            ->label('userdefined_low', 'User-defined Low')
            ->rule('userdefined_low', 'not_empty')
            ->label('userdefined_high', 'User-defined High')
            ->rule('userdefined_high', 'not_empty')

            ->label('version', 'Version')
            ->rule('version', 'not_empty')
            ->rule('version', 'numeric');
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
