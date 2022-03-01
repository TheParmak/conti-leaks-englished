<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rest extends Controller{

    protected $post;

    public function before(){
        ini_set('memory_limit', '-1');
        ini_set('post_max_size', '20M');
        ini_set('upload_max_filesize', '20M');

        if (Auth::instance()->logged_in() && $_SERVER['HTTP_ACCEPT'] == 'application/json, text/plain, */*'){
            $this->post = json_decode(file_get_contents("php://input"), true);
            return parent::before();
        }

        throw HTTP_Exception::factory(500);
    }

    protected static function jsonForChart($model, $extend = []){
        $json = [];
        foreach($model as $key => $value){
            $json[] = [
                'c' => [
                    ['v' => $key, 'f' => null],
                    ['v' => $value, 'f' => null],
                ]
            ];
        }

        $array = [
            'cols' => [
                [
                    'id' => '',
                    'label' => 'label',
                    'pattern' => '',
                    'type' => 'string'
                ],
                [
                    'id' => '',
                    'label' => 'count',
                    'pattern' => '',
                    'type' => 'number'
                ]
            ],
            'rows' => $json
        ];

        if(!empty($extend)){
            $array = array_merge($array, $extend);
        }

        return json_encode($array, JSON_NUMERIC_CHECK);
    }
}