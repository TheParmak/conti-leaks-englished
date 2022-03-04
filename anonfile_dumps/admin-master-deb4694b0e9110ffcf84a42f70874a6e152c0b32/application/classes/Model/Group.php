<?php defined('SYSPATH') or die('No direct script access.');

class Model_Group extends ORM {
    protected $errors;
	protected $_primary_key = 'id';
	protected $_table_name = 'groups';
	protected $_table_columns = [
		'id' => NULL,
		'name' => NULL,
		'groups' => NULL,
		'country' => NULL,
		'pass' => NULL,
	];

    public function getErrors(){ return $this->errors; }

    public function saveGroup(array $post){
        $fields = [
            'name',
            'groups',
            'country',
            'pass',
        ];

        $data = Arr::extract($post, $fields);

        if($data['groups'] == '*')
            $data['groups'] = '';

        if($data['country'] == NULL){
            $data['country'] = '[]';
        }else{
            $alpha_country = Kohana::$config->load('country')->as_array();
            foreach($data['country'] as $k => $v){
                $data['country'][$k] = isset($alpha_country[$v]) ? $alpha_country[$v] : $v;
            }
            $data['country'] = json_encode($data['country']);
        }

        $validation = Validation::factory($data)
            ->label('name', 'Name')
            ->rule('name', 'not_empty')

            ->label('pass', 'Password')
            ->rule('pass', 'not_empty');

        if( $validation->check() ){
            $this->values($data, $fields)
                ->save();

            HTTP::redirect('/groups');
        }else{
            $this->errors = $validation->errors("validation");
        }
    }

    public function to_list($column){
        return explode(' ', $this->{$column});
    }
}

