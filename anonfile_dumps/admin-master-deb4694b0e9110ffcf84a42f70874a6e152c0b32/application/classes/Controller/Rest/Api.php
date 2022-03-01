<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rest_Api extends Controller_Rest{

    public function action_log(){
        $response = [];
        $model = ORM::factory('Apilog');
        $model->filter($this->post);

        if ( false !== $model ) {
            $clone = clone $model;
            $total_items = $clone->count_all();
            unset($clone);
            $pagination = Pagination::factory([
                'total_items' => $total_items,
                'current_page' => [
                    'source' => 'route',
                    'key' => 'page',
                ],
            ]);
            $response['total_items'] = $pagination->getTotalItems();
            $response['current_page'] = $pagination->getCurrentPage();
            $response['items_per_page'] = $pagination->getItemsPerPage();

            $model = $model->limit($pagination->items_per_page)
                ->offset($pagination->offset)
                ->order_by($this->post['sortField'], $this->post['reverse'] ? 'DESC': 'ASC')
                ->find_all();
        }

        foreach($model as $k => $v){
            $t = $v->as_array();
            $t['apikey_id'] = intval($t['apikey_id']);
            $response['data'][$k] = $t;
        }

        $this->response->body(json_encode($response));
    }
}