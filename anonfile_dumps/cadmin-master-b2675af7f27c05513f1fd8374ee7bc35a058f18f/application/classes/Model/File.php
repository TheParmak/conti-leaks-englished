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
		'userdefined_low' => NULL,
		'userdefined_high' => NULL,
		'client_id' => NULL,
		'priority' => NULL,
		'filename' => NULL,
		'data' => NULL,
		'created_at' => NULL,
	];

    public function getErrors(){ return $this->errors; }

	public function addFile($argData, $update = false){
        $data = Arr::extract($_POST, [
            'client',
            'group',
            'sys_ver',
            'country',
            'userdefined_low',
            'userdefined_high',
            'filename',
            'priority'
        ]);

        $data['data'] = $argData;

        $validation = self::dataValidation($data);

        if( $validation->check() ){

            if($update){
                $update->filename = "bak_".$update->filename."_".date('Y-m-d_H:i:s');
                $update->update();
                ORM::factory('Userslogs')->createLog('upload update &laquo;<a href="/download/index/'.$update->id.'">'.$update->filename.'</a>&raquo; on filename &laquo;'.$_FILES['file']['name'].'&raquo; file &laquo;<a href="/download/index/'.$_POST['filename'].'">'.$_POST['filename'].'</a>&raquo;');
            }else{
                $tmp_file = ORM::factory('File', ['filename' => $_POST['filename']]);
                ORM::factory('Userslogs')->createLog('upload filename &laquo;'.$_FILES['file']['name'].'&raquo; file &laquo;<a href="/download/index/'.$tmp_file->id.'">'.$_POST['filename'].'</a>&raquo;');
            }

            $data['client_id'] = $this->clientID;
                if(!$update){
                    ORM::factory('File')
                        ->values($data)
                        ->save();
                }else{
                    $update->values($data)
                        ->update();
                }
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
            'group',
            'sys_ver',
            'country',
            'userdefined_low',
            'userdefined_high',
            'filename',
            'priority'
        ]);

        $validation = self::dataValidation($data);
        
        if ( ! $validation->check() ) {
            $this->errors = $validation->errors('validation');
            return false;
        }
        
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

            ->label('priority', 'Priority')
            ->rule('priority', 'not_empty')
            ->rule('priority', [$this, 'unique'], ['priority', ':value'])
            ->rule('priority', 'digit');
    }
}
