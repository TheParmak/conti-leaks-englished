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
        'importance_low' => NULL,
        'importance_high' => NULL,
        'userdefined_low' => NULL,
        'userdefined_high' => NULL,
        'group_include' => NULL,
        'group_exclude' => NULL,
        'timer' => NULL,
        'count_orig' => NULL,
	];

    public function getErrors(){ return $this->errors; }

    public function saveIdle(array $post, $id = null){
        $fields = [
            'count',
            'sys_ver',
            'country_1',
            'country_2',
            'country_3',
            'country_4',
            'country_5',
            'country_6',
            'country_7',
            'importance_low',
            'importance_high',
            'userdefined_low',
            'userdefined_high',
            'incode',
            'params',
            'group_include',
            'group_exclude',
            'timer',
            'count_orig',
        ];

        $post['count_orig'] = $post['count'];
        $data = Arr::extract($post, $fields);

        $validation = Validation::factory($data)
            ->label('count', 'Count')
            ->rule('count', 'not_empty')
            ->rule('count', 'numeric')

            ->label('sys_ver', 'System')
            ->rule('sys_ver', 'not_empty')

            ->label('importance_low', 'ImportanceLow')
            ->rule('importance_low', 'not_empty')
            ->rule('importance_low', ['Helper', 'check_importance_edit'], [':validation', ':field'])
            ->label('importance_high', 'ImportanceHigh')
            ->rule('importance_high', 'not_empty')
            ->rule(':importance_high', ['Helper', 'check_importance_edit'], [':validation', ':field'])

            ->label('userdefined_low', 'UserDefinedLow')
            ->rule('userdefined_low', 'not_empty')
            ->label('userdefined_high', 'UserDefinedHigh')
            ->rule('userdefined_high', 'not_empty')

            ->label('incode', 'Incode')
            ->rule('incode', 'not_empty')
            ->rule('incode', 'numeric')

            ->label('incode', 'Incode')
            ->rule('incode', 'numeric')

            ->label('params', 'Params')
            ->rule('params', 'not_empty');

        if( $validation->check() ){
            $data = Helper::prepareGroup($data, ['group_exclude', 'group_include']);
            ORM::factory('Userslogs')->createLog2('add Idle Command', $data);
            ORM::factory('Idlecommands', $id)
                ->values($data, $fields)
                ->save();
        }else{
            $this->errors = $validation->errors("validation");
        }
	}

	public function deleteIdleCommandsBlock($idlecommandsblocks){
		foreach($idlecommandsblocks as $id){
            $finded = ORM::factory('Idlecommands', $id);
            ORM::factory('Userslogs')
                ->createLog2('delete Idle Command Block', $finded->as_array());
            if($finded->loaded()){
                $finded->delete();
            }
		}
	}

	public function getUriForFilter(){
        if(strlen($this->params) < 100 && count(explode(' ', $this->params)) > 1){
            $arr = array_filter(explode(' ', $this->params));
            $arr = array_slice($arr, 0, -1);
            $url = implode(' ', $arr);
            return '<a  target="_blank" href="/?params_as='.$url.'">'.$this->params.'</a>';
        }else{
            return $this->params;
        }
    }
}
