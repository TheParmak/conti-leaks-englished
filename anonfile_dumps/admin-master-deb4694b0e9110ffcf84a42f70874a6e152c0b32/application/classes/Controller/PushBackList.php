<?php defined('SYSPATH') or die('No direct script access.');

class Controller_PushBackList extends Controller_Template {

    public $template = 'v_main';

    public function before(){
        $auth = Auth::instance();

        if($auth->logged_in() && Helper::checkActionInRole('PushBackList')){
            return parent::before();
        }else{
            HTTP::redirect('/');
        }
    }

	public function action_index(){
        if(isset($_POST['upd'])){
            $clients = explode(PHP_EOL, $_POST['clients']);
            $clients = array_map(function ($value){
                return explode('.', $value);
            }, $clients);

            $ids = DB::select('id')
                ->from('clients');

            foreach ($clients as $client){
                if(count($client) > 1){
                    if(!preg_match('/^[0-9A-F]{32}$/i', $client[1])){
                        continue;
                    }
                    $cid = Helper::getCid($client[1]);
                    $ids->or_where_open()
                            ->where('name', '=', $client[0])
                            ->where('id_low', '=', $cid[0])
                            ->where('id_high','=', $cid[1])
                        ->or_where_close();
                }else{
                    if(!preg_match('/^[0-9A-F]{32}$/i', $client[0])){
                        continue;
                    }
                    $cid = Helper::getCid($client[0]);
                    $ids->or_where_open()
                            ->where('id_low', '=', $cid[0])
                            ->where('id_high','=', $cid[1])
                        ->or_where_close();
                }
            }

            $ids = $ids->execute()
                ->as_array(null, 'id');

            if($ids){
                $fields = ['client_id', 'incode', 'params'];
                $insert = DB::insert('commands', $fields);
                foreach ($ids as $id){
                    $insert->values(Arr::extract([
                        'client_id' => $id,
                        'incode' => intval($_POST['incode']),
                        'params' => $_POST['params'],
//                        'created_at' => DB::expr('NOW()'),
                    ], $fields));
                }
                $insert->execute();

                ORM::factory('Userslogs')->createLog2Task(
                    "&laquo;Push Back&raquo",
                    Arr::extract($_POST, ['incode', 'params', 'clients']),
                    Auth::instance()->get_user()->pk()
                );

                HTTP::redirect('/');
            }
        }
		$this->template->content = BladeView::factory('push_back_list.index');
	}

	public function after(){
        $actions = ORM::factory('Action')->find_all();
        $user = Auth::instance()->get_user();

        $this->template->navbar = View::factory('v_navbar')
            ->bind('user', $user)
            ->bind('actions', $actions);

        parent::after();
    }
}