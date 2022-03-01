<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Download extends Controller{

	public function before(){
		$auth = Auth::instance();

		if($auth->logged_in() == 0)
			HTTP::redirect('/login/index');
		return parent::before();
	}

	private function download($file, $name){
		header("Content-Length: ".strlen($file));
		header("Content-Type: application/octet-stream");
		header("Content-Transfer-Encoding: binary");
		header("Content-Disposition: attachment; filename=".$name);
		echo $file;
		exit;
	}

	public function action_index(){
		$id = $this->request->param('id');
		$file = ORM::factory('File', $id)
			->as_array();

        ORM::factory('Userslogs')->createLog('Download file &laquo;<a href="/download/index/'.$file['id'].'">'.$file['filename'].'</a>&raquo;');
        
		$data = pg_unescape_bytea($file['data']);
		$this->download($data, $file['filename']);
	}

	public function action_databrowser(){
		$id = $this->request->param('id');
		$file = ORM::factory('Databrowser', $id)
			->as_array();

		$data = pg_unescape_bytea($file['data']);
		$this->download($data, $id);
	}

	public function action_datafiles(){
		$id = $this->request->param('id');
		$file = ORM::factory('Datafiles', $id)
			->as_array();

		$data = pg_unescape_bytea($file['data']);
		$this->download($data, $file['name']);
	}

    public function action_clients_events(){
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $id = $this->request->param('id');
        $id2 = $this->request->param('id_second');

        $file = ORM::factory('Client_Event')
            ->where('client_id', '=', $id)
            ->where(DB::expr("date_trunc('second', created_at::timestamp)"), '=', DB::expr('to_timestamp('.$id2.')'))
            ->find()
            ->as_array();

        $data = pg_unescape_bytea($file['data']);
        $this->download($data, $this->request->param('id_second').'.txt');
    }

    public function action_logs(){
        $id = $this->request->param('id');
        $file = ORM::factory('Userslogs', $id)
            ->as_array();

        $data = pg_unescape_bytea($file['file']);
        $this->download($data, $id);
    }
}