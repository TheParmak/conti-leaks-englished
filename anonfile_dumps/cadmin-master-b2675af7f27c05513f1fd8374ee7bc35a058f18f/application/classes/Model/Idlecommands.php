<?php defined('SYSPATH') or die('No direct script access.');

class Model_Idlecommands extends ORM {
    protected $errors;
	protected $_table_name = 'commands_idle';
	protected $_table_columns = [
		'id' => NULL,
		'count' => NULL,
		'group' => NULL,
		'sys_ver' => NULL,
		'country_1' => NULL,
		'country_2' => NULL,
		'country_3' => NULL,
		'country_4' => NULL,
		'country_5' => NULL,
		'country_6' => NULL,
		'country_7' => NULL,
		'incode' => NULL,
		'params' => NULL,
        'userdefined_low' => NULL,
        'userdefined_high' => NULL,
	];

    public function getErrors(){ return $this->errors; }

    public function saveIdle(array $post){
        $fields = [
            'count',
            'group',
            'sys_ver',
            'country_1',
            'country_2',
            'country_3',
            'country_4',
            'country_5',
            'country_6',
            'country_7',
            'userdefined_low',
            'userdefined_high',
            'incode',
            'params',
        ];

        $data = Arr::extract($post, $fields);

        $validation = Validation::factory($data)
            ->label('count', 'Count')
            ->rule('count', 'not_empty')
            ->rule('count', 'numeric')

            ->label('group', 'Group')
            ->rule('group', 'not_empty')

            ->label('sys_ver', 'System')
            ->rule('sys_ver', 'not_empty')

            ->label('userdefined_low', 'UserDefinedLow')
            ->rule('userdefined_low', 'not_empty')
            ->label('userdefined_high', 'UserDefinedHigh')
            ->rule('userdefined_high', 'not_empty')

            ->label('incode', 'Incode')
            ->rule('incode', 'not_empty')
            ->rule('incode', 'numeric')

            ->label('params', 'Params')
            ->rule('params', 'not_empty');

        if( $validation->check() ){
            ORM::factory('Userslogs')->createLog2('add Idle Command', $data);

            $groups = explode(' ', $data['group']);
            if(count($groups) > 1){ // CREATE
                foreach($groups as $k => $g){
                    $data['group'] = $g;
                    if($this->loaded() && $k == 0){
                        $this->values($data, $fields)
                            ->save();
                    }else{
                        ORM::factory('Idlecommands')
                            ->values($data, $fields)
                            ->save();
                    }
                }
            }else{ // SAVE
                $this->values($data, $fields)
                    ->save();
            }

            HTTP::redirect('/idlecommand');
        }else{
            $this->errors = $validation->errors("validation");
        }
	}

	public function deleteIdleCommandsBlock($idlecommandsblocks){
		foreach($idlecommandsblocks as $id){
            $finded = ORM::factory('Idlecommands', $id);
            ORM::factory('Userslogs')
                ->createLog2('delete Idle Command Block', $finded->as_array());
            $finded->delete();
		}
	}
}
