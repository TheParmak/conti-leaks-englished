<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rest extends Controller{

    /** @var array */
    protected $post;

    public function before(){
        ini_set('memory_limit', '-1');
        ini_set('post_max_size', '20M');
        ini_set('upload_max_filesize', '20M');

        // todo hack for restClient and angular
        if (Auth::instance()->logged_in()){
            if(!empty($this->request->post())){
                $this->post = $this->request->post();
            }else{
                $this->post = json_decode(file_get_contents("php://input"), true);
            }
            return parent::before();
        }

        throw HTTP_Exception::factory(401);
    }

    public function action_utc_timer(){
        $this->response->body(
            DB::select(DB::expr("to_char(NOW(), 'HH24:MI') as now"))
                ->execute()
                ->as_array()[0]['now']
        );
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

    protected function paginate(&$response, $model, $items_per_page = 10){
        $total_items = 0;

        if($model instanceof ORM){
            $model = $model->reset(false);
            $total_items = $model->count_all();
        }else{
            $clone = clone $model;
            $total_items = $clone->select(DB::expr('COUNT(*) AS total_items'))
                ->execute()
                ->get('total_items');
        }

        $pagination = Pagination::factory([
            'total_items' => $total_items,
            'current_page' => [
                'source' => 'route',
                'key' => 'page',
            ],
            'items_per_page' => $items_per_page,
        ]);
        $response['total_items'] = $pagination->getTotalItems();
        $response['current_page'] = $pagination->getCurrentPage();
        $response['items_per_page'] = $pagination->getItemsPerPage();

        return $model->limit($pagination->items_per_page)
            ->offset($pagination->offset);
    }

    protected function getDate($date){
        $date = new DateTime($date);
        return $date->format('Y-m-d H:i:s');
    }
}