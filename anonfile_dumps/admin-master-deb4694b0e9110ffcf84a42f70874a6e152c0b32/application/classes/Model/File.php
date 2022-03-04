<?php defined('SYSPATH') or die('No direct script access.');

class Model_File extends ORM {
    protected $errors;
    protected $clientID;
	protected $_primary_key = 'id';
	protected $_table_columns = [
		'id' => NULL,
		'group' => NULL,
		'country' => NULL,
		'sys_ver' => NULL,
		'importance_low' => NULL,
		'importance_high' => NULL,
		'userdefined_low' => NULL,
		'userdefined_high' => NULL,
		'client_id' => NULL,
		'priority' => NULL,
		'filename' => NULL,
		'data' => NULL,
		'created_at' => NULL,
        'group_include' => NULL,
        'group_exclude' => NULL,
	];

    public function getErrors(){ return $this->errors; }

	public function addFile($argData){
        $data = Arr::extract($_POST, [
            'client',
            'sys_ver',
            'country',
            'importance_low',
            'importance_high',
            'userdefined_low',
            'userdefined_high',
            'filename',
            'priority',
            'group_include',
            'group_exclude',
        ]);

        $data['data'] = $argData;
        $validation = self::dataValidation($data);

        if( $validation->check() ){
            $data = Helper::prepareGroup($data, ['group_exclude', 'group_include']);
            $tmp_file = ORM::factory('File', ['filename' => $_POST['filename']]);
            ORM::factory('Userslogs')->createLog('upload filename &laquo;'.$_FILES['file']['name'].'&raquo; file &laquo;<a href="/download/index/'.$tmp_file->id.'">'.$_POST['filename'].'</a>&raquo;');
            $data['client_id'] = $this->clientID;
            ORM::factory('File')
                ->values($data)
                ->save();
        }else{
            $this->errors = $validation->errors("validation");
        }
	}

    public function check_client($ClientID){
        if($ClientID == '0'){
            $this->clientID = 0;
            return true;
        }else{
            $this->clientID = Model::factory('Client')->getClientIDByName($ClientID);

            if($this->clientID == '0'){
                return false;
            }else{
                return true;
            }
        }
    }

    public function updateFile($data, $id){
        $data = Arr::extract($data, [
            'client',
            'sys_ver',
            'country',
            'importance_low',
            'importance_high',
            'userdefined_low',
            'userdefined_high',
            'filename',
            'priority',
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
        ORM::factory('File', $id)->values($data)->update();
        
        return true;
    }

    private function dataValidation($data){
        return Validation::factory($data)
            ->label('client', 'ClientID')
            ->rule('client', 'not_empty')
            ->rule('client', [$this, 'check_client'])

            ->label('filename', 'Name')
            ->rule('filename', 'not_empty')

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

            ->label('priority', 'Priority')
            ->rule('priority', 'not_empty')
            ->rule('priority', [$this, 'unique'], ['priority', ':value'])
            ->rule('priority', 'digit');
    }
}
