<?php defined('SYSPATH') or die('No direct script access.');

class Model_Apilog extends ORM {
    protected $_primary_key = 'apikey_id';
	protected $_table_name = 'apilog';
	protected $_table_columns = [
		'apikey' => NULL,
		'apikey_id' => NULL,
		'ip' => NULL,
		'command' => NULL,
		'time' => NULL,
		'type' => NULL,
	];

    public function filter($post){
        $post = Arr::extract($post, [
            'apikey',
            'command',
            'start',
            'end',
        ]);

        // TODO: add validation rules
        $validation = Validation::factory($post)
            ->label('apikey', 'ApiKey')

            ->label('command', 'Command')

            ->label('start', 'Registered start')
            ->rule('start', 'regex', [':value', '/^\d{4}\/\d{2}\/\d{2}$/'])

            ->label('end', 'Registered end')
            ->rule('end', 'regex', [':value', '/^\d{4}\/\d{2}\/\d{2}$/']);

        if ( ! $validation->check() ) {
            $this->errors = $validation->errors('validation');

            return false;
        }

        /* time */
        if (Helper::issetAndNotEmpty($post['start'])) {
            $this->where('time', '>=', $post['start'] . ' 00:00:00');
        }
        if (Helper::issetAndNotEmpty($post['end'])) {
            $this->where('time', '<=', $post['end'] . ' 23:59:59');
        }

        /* apikey */
        if (Helper::issetAndNotEmpty($post['apikey'])) {
            $this->where('apikey', '=', $post['apikey']);
        }

        /* command */
        if (Helper::issetAndNotEmpty($post['command'])) {
            $this->where('command', 'LIKE', '%'.$post['command'].'%');
        }

        return $this;
    }
}

