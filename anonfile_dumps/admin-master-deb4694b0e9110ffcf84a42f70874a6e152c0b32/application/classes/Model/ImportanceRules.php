<?php defined('SYSPATH') or die('No direct script access.');

class Model_ImportanceRules extends ORM{

    protected $_table_name = 'importance_rules';
    protected $_table_columns = [
        'id' => null,
        'class' => null,
        'params' => null,
        'preplus' => null,
        'mul' => null,
        'postplus' => null,
    ];

    protected $errors;

    public function getErrors(){ return $this->errors; }

    public function record(array $post){
        $fields = [
            'class',
            'params',
            'preplus',
            'mul',
            'postplus',
        ];

        $data = Arr::extract($post, $fields);

        $validation = Validation::factory($data)
            ->label('class', 'Class')
            ->rule('class', 'not_empty')
            ->rule('class', 'in_array', [':value', Kohana::$config->load('select.importance_rules_class')])

            ->label('params', 'Params')
            ->rule('params', 'not_empty')

            ->label('preplus', 'PrePlus')
            ->rule('preplus', 'numeric')
            ->rule('preplus', 'not_empty')

            ->label('mul', 'Mul')
            ->rule('mul', 'numeric')
            ->rule('mul', 'not_empty')

            ->label('postplus', 'PostPlus')
            ->rule('postplus', 'numeric')
            ->rule('postplus', 'not_empty');

        if( $validation->check() ){
            $this->values($data, $fields)
                ->save();

            HTTP::redirect('/crud/importancerules');
        }else{
            $this->errors = $validation->errors("validation");
        }
    }
}
