<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Globalinfo extends Controller{

	public function before(){
		if($this->request->is_initial()){
			HTTP::redirect('/login');
		}

		$auth = Auth::instance();
		if($auth->logged_in() == 0)
			HTTP::redirect('/login');
		return parent::before();
	}

	private static function worker($orm, $where_param, $where_value){
		$tmp = $orm->where($where_param, '=', $where_value)->find();
		if($tmp->loaded()){
			$tmp->set('value', DB::expr('value + 1'));
		}else{
			$tmp->param = $where_value;
			$tmp->user_id = Auth::instance()->get_user()->id;
		}
		$tmp->save();
		$orm->clear();
	}

	public function action_index(){
		$post = $this->request->post()['clients'];

		$data_general = ORM::factory('Datageneral');
		$info_program = ORM::factory('Info_Program');
		$info_browser = ORM::factory('Info_Browser');
		$info_client = ORM::factory('Info_Client');
		$info_var = ORM::factory('Info_Var');
		$var = ORM::factory('Var');

		$info_program->clearDb();
		$info_browser->clearDb();
		$info_client->clearDb();
		$info_var->clearDb();

		foreach($post as $p){
			foreach($data_general->where('client', '=', $p->client)->find_all() as $d){
				$search = 'ie\d+$|Mozilla\sFirefox|chrome|^Windows\sInternet\sExplorer';
				$data = array_filter(
					explode("\r\n", $d->data)
				);
				$program_key = array_search('==Programs==', $data);
				$service_key = array_search('==Services==', $data);
				$array_browser = array();

				/* PROGRAMS */
				foreach($data as $key => $item){
					if($key > $program_key && $key < $service_key){
						$array_browser[] = $item;
						$this::worker($info_program, 'param', $item);
					}
				}

				/* BROWSER */
				foreach($array_browser as $item){
					if(preg_match('#'.$search.'#i', $item)){
						$this::worker($info_browser, 'param', $item);
					}
				}
			}
		}


		foreach($post as $client){
			$tmp_var = $var->where('client', '=', $client->client)
				->order_by('datetime', 'desc')
				->find_all();

			/* SYSTEMS e.g. win7 */
			$this::worker($info_client, 'param', $client->system);

			/* VARS SYSTEM */
			foreach($tmp_var as $item){
				if(preg_match('#SYSTEM#', $item->value)){
					$this::worker($info_var, 'param', $item->value);
					break;
				}
			}

			/* VARS NAT */
			foreach($tmp_var as $item){
				if(preg_match('#NAT#', $item->name)){
					$this::worker($info_var, 'param', $item->value);
					break;
				}
			}
		}


		$programs = ORM::factory('Info_Program')
			->order_by('value', 'DESC')
			->find_all();
		$clients = ORM::factory('Info_Client')
			->order_by('value', 'DESC')
			->find_all();
		$browsers = ORM::factory('Info_Browser')
			->order_by('value', 'DESC')
			->find_all();
		$vars = ORM::factory('Info_Var')
			->order_by('value', 'DESC')
			->find_all();

		$vars_count = 0;
		foreach($post as $client){
			$vars_count += $client->vars->count_all();
		}

		$this->response->body(
			View::factory('globalinfo/v_index')
				->bind('programs', $programs)
				->bind('clients', $clients)
				->bind('browser', $browsers)
				->bind('vars', $vars)
				->bind('vars_count', $vars_count) // very lazy rename variable
		);
	}
}