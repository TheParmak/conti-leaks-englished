<?php defined('SYSPATH') or die('No direct script access.');

class Model_Server extends ORM {
	protected $_primary_key = 'id';
	protected $_table_name = 'backconnservers';
	protected $_table_columns = array(
		'id' => NULL,
		'ip' => NULL,
		'port' => NULL,
		'password1' => NULL,
		'password2' => NULL,
	);
    
    protected $errors;
    
    public function getErrors()
    {
        return $this->errors;
    }

	public function addBCServer($data)
    {
        $data = Arr::extract($data, [
            'ip',
            'port',
            'password1',
            'password2',
        ]);

        $validation = Validation::factory($data)
            ->label('ip', 'IP')
            ->rule('ip', 'not_empty')
//            ->rule('ip', 'ip') TODO: don't work
            ->rule('ip', 'regex', [':value', '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/'])
            ->label('port', 'Port')
            ->rule('port', 'not_empty')
            ->rule('port', 'digit')
            ->label('password1', 'Password 1')
            ->rule('password1', 'not_empty')
            ->label('password2', 'Password 2')
            ->rule('password2', 'not_empty');

        if ( ! $validation->check() ) {
            $this->errors = $validation->errors('validation');
            return false;
        }

        DB::insert($this->table_name(), array_keys($data))->values($data)->execute();

        return true;
	}

	public function deleteBCServer($argName){
        ORM::factory('Server', $argName)->delete();
	}

	public function updateBCServer($data, $server){
        $data = Arr::extract($data, [
            'ip',
            'port',
            'password1',
            'password2',
        ]);

        $validation = Validation::factory($data)
            ->label('ip', 'IP')
            ->rule('ip', 'not_empty')
            ->rule('ip', 'ip')
            ->label('port', 'Port')
            ->rule('port', 'not_empty')
            ->rule('port', 'digit')
            ->label('password1', 'Password 1')
            ->rule('password1', 'not_empty')
            ->label('password2', 'Password 2')
            ->rule('password2', 'not_empty');
        
        if ( ! $validation->check() )
        {
            $this->errors = $validation->errors('validation');
        
            return false;
        }

        $server->values($data)->update();

        return true;
	}
}
