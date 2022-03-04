<?php defined('SYSPATH') or die('No direct script access.');

class Model_Command_Event extends ORM{

	protected $_primary_key = 'id';
	protected $_table_name = 'commands_event';
	protected $_table_columns = [
		'id' => null,
		'incode' => null,
		'params' => null,
		'module' => null,
		'event' => null,
		'info' => null,
		'interval' => null,
	];

    protected $errors;

    public function getErrors(){ return $this->errors; }

    public function record(array $post){
        $fields = [
            'incode',
            'params',
            'module',
            'event',
            'info',
            'interval',
        ];

        $data = Arr::extract($post, $fields);

        $validation = Validation::factory($data)
            ->label('incode', 'Incode')
            ->rule('incode', 'not_empty')
            ->rule('incode', 'numeric')

            ->label('params', 'Params')
            ->rule('params', 'not_empty')

            ->label('module', 'Module')
            ->rule('module', 'not_empty')

            ->label('module', 'Module')
            ->rule('module', 'not_empty')

            ->label('info', 'Info')
            ->rule('info', 'not_empty')

            ->label('interval', 'Interval')
            ->rule('interval', 'not_empty')

            ->label('event', 'Event')
            ->rule('event', 'not_empty');

        if( $validation->check() ){
            $this->values($data, $fields)
                ->save();

            HTTP::redirect('/crud/commandsevent');
        }else{
            $this->errors = $validation->errors("validation");
        }
    }
}
