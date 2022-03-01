<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rest_Group extends Controller{

    public function before(){
        ini_set('memory_limit', '-1');
        ini_set('post_max_size', '20M');
        ini_set('upload_max_filesize', '20M');

        if ($_SERVER['HTTP_ACCEPT'] == 'application/json, text/plain, */*'){
            $this->post = json_decode(file_get_contents("php://input"), true);
            foreach($this->post as $k => $v){
                $this->post[$k] = $v;
            }

            return parent::before();
        }

        throw HTTP_Exception::factory(500);
    }

    public function action_clients(){
        if(isset($this->post['name']) && isset($this->post['pass'])){
            $record = DB::select('groups', 'country')
                ->from('groups')
                ->where('name', '=', $this->post['name'])
                ->where('pass', '=', $this->post['pass'])
                ->execute()
                ->current();

            if($record){
                if(!empty($record['groups']))
                    $this->post['group'] = explode(' ', $record['groups']);
                if(Arr::get($this->post, 'groups')){
                    if(isset($this->post['group'])){
                        $need_groups = [];
                        foreach ($this->post['group'] as $g){
                            $match = preg_grep('#^'.preg_replace('#\*#', '', $g).'#', $this->post['groups']);
                            if($match){
                                $need_groups = array_merge($need_groups, $match);
                            }
                        }

                        if($need_groups){
                            $this->post['group'] = $need_groups;
                        }
                    }else{
                        $this->post['group'] = $this->post['groups'];
                    }
                }
                if(!empty($record['country']))
                    $this->post['country'] = json_decode($record['country'], true);
                
                if(isset($this->post['importance'])){
                    $this->post['importance_start'] = 20;
                    unset($this->post['importance']);
                }

                unset($this->post['name']);
                $response = [];

                $clientModel = ORM::factory('Client');
                $clients = $clientModel->selectByFilter($this->post);
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

                    $clients = $clients->limit($pagination->items_per_page)
                        ->offset($pagination->offset);
                    if($this->post['sortField']){
                        $clients->order_by($this->post['sortField'], $this->post['reverse'] ? 'DESC': 'ASC');
                    }
                    $clients = $clients->find_all();
                }

                $alpha_country = Kohana::$config->load('country')->as_array();

                foreach($clients as $k => $v){
                    $t = $v->as_array();
                    $t['id'] = (int) $v->id;
                    $t['client'] = $v->getFullName();
                    $t['created_at'] = $v->getDate('created_at');
                    $t['last_activity'] = $v->getDate('last_activity');

                    $t['country'] = isset($alpha_country[$t['country']]) ? $alpha_country[$t['country']] : $t['country'];

                    $t = Arr::extract($t, ['id', 'client', 'group', 'created_at', 'last_activity', 'ip', 'sys_ver', 'country', 'importance']);
                    $response['data'][$k] = $t;
                }

                $this->response->body(json_encode($response));
            }else{
                throw HTTP_Exception::factory(404);
            }
        }else{
            throw HTTP_Exception::factory(404);
        }
    }

    public function action_system(){
        if(isset($this->post['name']) && isset($this->post['pass'])){
            $record = DB::select('groups', 'country')
                ->from('groups')
                ->where('name', '=', $this->post['name'])
                ->where('pass', '=', $this->post['pass'])
                ->execute()
                ->current();

            if($record){
                if(!empty($record['groups']))
                    $this->post['group'] = explode(' ', $record['groups']);
                if(!empty($record['country']))
                    $this->post['country'] = json_decode($record['country'], true);

                if(isset($this->post['importance'])){
                    $this->post['importance_start'] = 20;
                    unset($this->post['importance']);
                }

                unset($this->post['name']);
                $clients = DB::select('sys_ver', DB::expr('COUNT(sys_ver) AS cnt'))->from('clients');

                $clients = Model::factory('Client')
                    ->selectByFilter($this->post, false, $clients);

                $clients = $clients->group_by('sys_ver')
                    ->order_by('cnt', 'DESC')
                    ->execute()
                    ->as_array('sys_ver', 'cnt');

                $detailedStat = [];
                foreach ($clients as $k => $v){
                    $detailedStat[] = ['system' => $k, 'count' => $v];
                }

                $this->response->body(
                    self::jsonForChart($clients, ['detailedStat' => $detailedStat])
                );
            }else{
                throw HTTP_Exception::factory(404);
            }
        }else{
            throw HTTP_Exception::factory(404);
        }
    }

    public function action_group(){
        if(isset($this->post['name']) && isset($this->post['pass'])){
            $record = DB::select('groups', 'country')
                ->from('groups')
                ->where('name', '=', $this->post['name'])
                ->where('pass', '=', $this->post['pass'])
                ->execute()
                ->current();

            if($record){
                if(!empty($record['groups']))
                    $this->post['group'] = explode(' ', $record['groups']);
                if(!empty($record['country']))
                    $this->post['country'] = json_decode($record['country'], true);

                if(isset($this->post['importance'])){
                    $this->post['importance_start'] = 20;
                    unset($this->post['importance']);
                }

                unset($this->post['name']);
                $clients = DB::select('group', DB::expr('COUNT("group") AS cnt'))->from('clients');

                $clients = Model::factory('Client')
                    ->selectByFilter($this->post, false, $clients);

                $clients = $clients->group_by('group')
                    ->order_by('cnt', 'DESC')
                    ->execute()
                    ->as_array('group', 'cnt');

                $detailedStat = [];
                foreach ($clients as $k => $v){
                    $detailedStat[] = ['group' => $k, 'count' => $v];
                }

                $this->response->body(
                    self::jsonForChart($clients, ['detailedStat' => $detailedStat])
                );
            }else{
                throw HTTP_Exception::factory(404);
            }
        }else{
            throw HTTP_Exception::factory(404);
        }
    }

//    public function action_usersystem(){
//        if(isset($this->post['name']) && isset($this->post['pass'])){
//            $record = DB::select('groups')
//                ->from('groups')
//                ->where('name', '=', $this->post['name'])
//                ->where('pass', '=', $this->post['pass'])
//                ->execute()
//                ->as_array();
//
//            if(count($record)){
//                if(!empty($record[0]['groups']))
//                    $this->post['group'] = explode(' ', $record[0]['groups']);
//
//                unset($this->post['name']);
//                $clients = DB::select('id')->from('clients');
//
//                $clients = Model::factory('Client')
//                    ->selectByFilter($this->post, false, $clients);
//
//                $clientids = $clients->execute()
//                    ->as_array(null, 'id');
//
//                $use_cache = true;
//                if ($use_cache) {
//                    $counts = [];
//                    $counts['user => SYSTEM'] = ORM::factory('Client_UserSystem')
//                        ->where('id', 'IN', $clientids)
//                        ->count_all();
//                    $counts['Other'] = count($clientids) - $counts['user => SYSTEM'];
//                } else {
//                    $counts = [
//                        'user => SYSTEM' => 0,
//                        'Other' => 0,
//                    ];
//                    $usersystem = array_map('mb_strtolower', Kohana::$config->load('vars.usersystem'));
//                    foreach($clientids as $clientid) {
//                        $vars = Model::factory('Var')->getClientVars($clientid, false);
//
//                        foreach($vars as $var) {
//                            if ( $var['key'] != 'user' ) {
//                                continue;
//                            }
//
//                            if ( in_array(mb_strtolower($var['value']), $usersystem) ) {
//                                $counts['user => SYSTEM']++;
//                            } else {
//                                $counts['Other']++;
//                            }
//
//                            break;
//                        }
//                    }
//                }
//
//                $this->response->body(
//                    self::jsonForChart($counts)
//                );
//            }else{
//                throw HTTP_Exception::factory(404);
//            }
//        }else{
//            throw HTTP_Exception::factory(404);
//        }
//    }

    public function action_geo(){
        if(isset($this->post['name']) && isset($this->post['pass'])){
            $record = DB::select('groups', 'country')
                ->from('groups')
                ->where('name', '=', $this->post['name'])
                ->where('pass', '=', $this->post['pass'])
                ->execute()
                ->current();

            if($record){
                if(!empty($record['groups']))
                    $this->post['group'] = explode(' ', $record['groups']);
                if(!empty($record['country']))
                    $this->post['country'] = json_decode($record['country'], true);

                if(isset($this->post['importance'])){
                    $this->post['importance_start'] = 20;
                    unset($this->post['importance']);
                }

                unset($this->post['name']);
                $clients = DB::select('country')->from('clients');
                
                $geo_stat = Model::factory('Client')->selectByFilter($this->post, false, $clients)
                    ->select('country', DB::expr('COUNT(*) AS cnt'))
                    ->group_by('country')
                    ->execute()
                    ->as_array('country', 'cnt');

                $total_count = array_sum($geo_stat);

                $detailedStat = [];
//                $less_tmp = 0;
                foreach($geo_stat as $location => $count){
                    $percent = $count / $total_count * 100;
                    $detailedStat[] = [
                        'location' => $location,
                        'count' => $count,
                        'percent' => sprintf('%.1F', $percent),
                    ];
//                    if($percent < 3){
//                        $less_tmp += $count;
//                        unset($tmp[$location]);
//                    }
                }
//                $tmp['Others'] = $less_tmp;

                $this->response->body(
                    self::jsonForChart($geo_stat, ['detailedStat' => $detailedStat])
                );
            }else{
                throw HTTP_Exception::factory(404);
            }
        }else{
            throw HTTP_Exception::factory(404);
        }
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