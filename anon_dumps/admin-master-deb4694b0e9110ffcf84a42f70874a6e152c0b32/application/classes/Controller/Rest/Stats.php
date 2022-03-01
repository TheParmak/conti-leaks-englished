<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rest_Stats extends Controller_Rest{

    public function action_usersystem(){
        $file_path_net_access = Kohana::find_file('net_access', Auth::instance()->get_user()->id, 'json');
        $clients = DB::select('id')->from('clients');

        if(!empty($this->post)){
            $clients = Model::factory('Client')
                ->selectByFilter($this->post, Auth::instance()->get_user()->id, $clients);
        }else{
            /* NET ACCESS */
            if($file_path_net_access){
                $net_list = json_decode(
                    file_get_contents($file_path_net_access)
                );

                $net_list_mask = preg_grep('#\*#', $net_list);
                $net_list = preg_grep('#\*#', $net_list, PREG_GREP_INVERT);

                if(!empty($net_list_mask)){
                    $clients->where_open();
                }

                if(!empty($net_list)){
                    $clients->where('group', 'IN', $net_list);
                }

                foreach ($net_list_mask as $net){
                    $clients->or_where('group', 'LIKE', preg_replace('#\*#', '', trim($net)).'%');
                }

                if(!empty($net_list_mask)){
                    $clients->where_close();
                }
            }
        }

        $clientids = $clients->execute()
            ->as_array(null, 'id');

        $use_cache = true;
        if ($use_cache) {
            $counts = [];
            $counts['user => SYSTEM'] = ORM::factory('Client_UserSystem')
                ->where('id', 'IN', $clientids)
                ->count_all();
            $counts['Other'] = count($clientids) - $counts['user => SYSTEM'];
        } else {
            $counts = [
                'user => SYSTEM' => 0,
                'Other' => 0,
            ];
            $usersystem = array_map('mb_strtolower', Kohana::$config->load('vars.usersystem'));
            foreach($clientids as $clientid) {
                $vars = Model::factory('Var')->getClientVars($clientid, false);

                foreach($vars as $var) {
                    if ( $var['key'] != 'user' ) {
                        continue;
                    }

                    if ( in_array(mb_strtolower($var['value']), $usersystem) ) {
                        $counts['user => SYSTEM']++;
                    } else {
                        $counts['Other']++;
                    }

                    break;
                }
            }
        }

        $this->response->body(
            self::jsonForChart($counts)
        );
    }

    public function action_ip(){
        $file_path_net_access = Kohana::find_file('net_access', Auth::instance()->get_user()->id, 'json');
        $clients = DB::select('ip', DB::expr('COUNT(ip) AS cnt'))->from('clients');

        if(!empty($this->post)){
            $clients = Model::factory('Client')
                ->selectByFilter($this->post, Auth::instance()->get_user()->id, $clients);
        }else{
            /* NET ACCESS */
            if($file_path_net_access){
                $net_list = json_decode(
                    file_get_contents($file_path_net_access)
                );
                $net_list_mask = preg_grep('#\*#', $net_list);
                $net_list = preg_grep('#\*#', $net_list, PREG_GREP_INVERT);

                if(!empty($net_list_mask)){
                    $clients->where_open();
                }

                if(!empty($net_list)){
                    $clients->where('group', 'IN', $net_list);
                }

                foreach ($net_list_mask as $net){
                    $clients->or_where('group', 'LIKE', preg_replace('#\*#', '', trim($net)).'%');
                }

                if(!empty($net_list_mask)){
                    $clients->where_close();
                }
            }
        }

        $clients = $clients->group_by('ip')
            ->having(DB::expr('count(id)'), '>', 1)
            ->execute()
            ->as_array('ip', 'cnt');

        $detailedStat = [];
        foreach ($clients as $k => $v){
            $detailedStat[] = [
                'ip' => $k,
                'count' => $v,
            ];
        }

        $this->response->body(
            self::jsonForChart($clients, ['detailedStat' => $detailedStat])
        );
    }

    public function action_system(){
        $file_path_net_access = Kohana::find_file('net_access', Auth::instance()->get_user()->id, 'json');
        $clients = DB::select('sys_ver', DB::expr('COUNT(sys_ver) AS cnt'))->from('clients');

        if(!empty($this->post)){
            $clients = Model::factory('Client')
                ->selectByFilter($this->post, Auth::instance()->get_user()->id, $clients);
        }else{
            /* NET ACCESS */
            if($file_path_net_access){
                $net_list = json_decode(
                    file_get_contents($file_path_net_access)
                );
                $net_list_mask = preg_grep('#\*#', $net_list);
                $net_list = preg_grep('#\*#', $net_list, PREG_GREP_INVERT);

                if(!empty($net_list_mask)){
                    $clients->where_open();
                }

                if(!empty($net_list)){
                    $clients->where('group', 'IN', $net_list);
                }

                foreach ($net_list_mask as $net){
                    $clients->or_where('group', 'LIKE', preg_replace('#\*#', '', trim($net)).'%');
                }

                if(!empty($net_list_mask)){
                    $clients->where_close();
                }
            }
        }

        $clients = $clients->group_by('sys_ver')
            ->execute()
            ->as_array('sys_ver', 'cnt');

        $this->response->body(
            self::jsonForChart($clients)
        );
    }

    public function action_av(){
        $file_path_net_access = Kohana::find_file('net_access', Auth::instance()->get_user()->id, 'json');
        $clients = DB::select('id')->from('clients');

        if(!empty($this->post)){
            $clients = Model::factory('Client')
                ->selectByFilter($this->post, Auth::instance()->get_user()->id, $clients);
        }else{
            /* NET ACCESS */
            if($file_path_net_access){
                $net_list = json_decode(
                    file_get_contents($file_path_net_access)
                );
                $net_list_mask = preg_grep('#\*#', $net_list);
                $net_list = preg_grep('#\*#', $net_list, PREG_GREP_INVERT);

                if(!empty($net_list_mask)){
                    $clients->where_open();
                }

                if(!empty($net_list)){
                    $clients->where('group', 'IN', $net_list);
                }

                foreach ($net_list_mask as $net){
                    $clients->or_where('group', 'LIKE', preg_replace('#\*#', '', trim($net)).'%');
                }

                if(!empty($net_list_mask)){
                    $clients->where_close();
                }
            }
        }

        $ids = $clients->execute()
            ->as_array(null, 'id');


        $av = DB::select('name', DB::expr('COUNT(name) AS cnt'))
            ->from('av')
            ->where('client_id', 'IN', $ids)
            ->group_by('name')
            ->execute()
            ->as_array('name', 'cnt');

        $detailedStat = [];
        foreach ($av as $k => $v){
            $detailedStat[] = [
                'av' => $k,
                'count' => $v,
            ];
        }

        $this->response->body(
            self::jsonForChart($av, ['detailedStat' => $detailedStat])
        );
    }

    public function action_geo(){
        $alpha_country = Kohana::$config->load('country')->as_array();
        $file_path_net_access = Kohana::find_file('net_access', Auth::instance()->get_user()->id, 'json');
        $geo_stat = ORM::factory('Client');

        if(!empty($this->post)){
            $geo_stat = $geo_stat->selectByFilter($this->post, Auth::instance()->get_user()->id);
        }else{
            /* NET ACCESS */
            if($file_path_net_access){
                $net_list = json_decode(
                    file_get_contents($file_path_net_access)
                );
                $net_list_mask = preg_grep('#\*#', $net_list);
                $net_list = preg_grep('#\*#', $net_list, PREG_GREP_INVERT);

                if(!empty($net_list_mask)){
                    $geo_stat->where_open();
                }

                if(!empty($net_list)){
                    $geo_stat->where('group', 'IN', $net_list);
                }

                foreach ($net_list_mask as $net){
                    $geo_stat->or_where('group', 'LIKE', preg_replace('#\*#', '', trim($net)).'%');
                }

                if(!empty($net_list_mask)){
                    $geo_stat->where_close();
                }
            }
        }

        $geo_stat = $geo_stat->distinct('true')
            ->find_all();

        $tmp = array();
        foreach($geo_stat as $client){
            $country = trim($client->country);
            $country = isset($alpha_country[$country]) ? $alpha_country[$country] : $country;
            $tmp[$country] = Arr::get($tmp, $country, 0) + 1;
        }

        $total_count = array_sum($tmp);

        $detailedStat = [];
        $less_tmp = 0;
        foreach($tmp as $location => $count){
            $percent = $count / $total_count * 100;
            $detailedStat[] = [
                'location' => $location,
                'count' => $count,
                'percent' => sprintf('%.1F', $percent),
            ];
            if($percent < 3){
                $less_tmp += $count;
                unset($tmp[$location]);
            }
        }
        $tmp['Others'] = $less_tmp;

        $this->response->body(
            self::jsonForChart($tmp, ['detailedStat' => $detailedStat])
        );
    }
}