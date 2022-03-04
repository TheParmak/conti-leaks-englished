<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Remove extends CheckAction
{

	public function action_index()
    {
        // TODO: unite "errors" and "success" into single variable "messages" and move contents of this IF to some service
        if ( Request::POST === $this->request->method() )
        {
            if ( null !== $this->request->post('DeleteVars') )
            {
                $function = 'DeleteVars';
            }
            elseif ( null !== $this->request->post('DeleteLog') )
            {
                $function = 'DeleteLog';
            }
            elseif ( null !== $this->request->post('DeleteBackConnData') )
            {
                $function = 'DeleteBackConnData';
            }
            else
            {
                throw HTTP_Exception::factory(404);
            }
            
            $post = $this->request->post();
            $validation = Validation::factory($post)
                ->label('from_lastactivity', 'Last activity')
                ->rule('from_lastactivity', 'regex', array(':value', '/^\d{4}\/\d{2}\/\d{2}$/'))
                ->label('to_lastactivity', 'Last activity')
                ->rule('to_lastactivity', 'regex', array(':value', '/^\d{4}\/\d{2}\/\d{2}$/'))
                ->label('clientid', 'ClientID')
                ->rule('clientid', 'regex', array(':value', '/^(\s*|[0-9]+|[A-F0-9]+)$/i'));
            if ( ! $validation->check() )
            {
                $errors = $validation->errors('validation');
            }
            else
            {
                if ( $clientid = Arr::get($post, 'clientid') )
                {
                    if ( ! ctype_digit($clientid) )
                    {
                        $clientid = $this->clientID = Model::factory('Client')->getClientIDByName($clientid);
                    }
                    
                    $this->deleteDataClient($function, $clientid);
                    
                    if ( null !== ( $redirect_to = $this->request->query('redirect_to') ) && preg_match('/^https\:\/\/' . preg_quote($_SERVER['HTTP_HOST'], '/') . '\//', $redirect_to) )
                    {
                        $this->redirect($redirect_to);
                    }
                    
                    $client = ORM::factory('Client', $clientid);
                    
                    $success = 'Successfully executed <strong>' . $function . '</strong> on client ' . $client->getLink() . '.';
                }
                else
                {
                    $countTotal = $this->deleteDataPeriod($function, $post);
                    if ( ! $countTotal )
                    {
                        $errors = array('No clients to process in selected period');
                    }
                    else
                    {
                        $process = array();
                        $process['function'] = $function;
                        $process['from_lastactivity'] = Arr::get($post, 'from_lastactivity');
                        $process['to_lastactivity'] = Arr::get($post, 'to_lastactivity');
                        $process['countTotal'] = $countTotal;

                        $this->redirect('/remove/process?' . http_build_query($process));
                    }
                }
            }
        }
        
		$this->template->content = View::factory('remove/v_index')
            ->bind('errors', $errors)
            ->bind('success', $success)
            ->set('post', $this->request->post());
	}

    /* TODO need more info about query */
	private function deleteDataClient($function, $clientid, $isProcess = false)
    {
        $query = 'SELECT ' . $function . '(:argClient)';
        $query = DB::query(Database::SELECT, $query);
        $query->parameters(array(
            ':argClient' => $clientid,
        ));
        $query->execute();
        
        if ( ! $isProcess )
        {
            $client = ORM::factory('Client', $clientid);
            ORM::factory('Userslogs')->createLog('Remove 75% data &laquo;' . $function . '&raquo; on client ' . $client->getLink());
        }
    }

    private function deleteDataPeriod($function, $post)
    {
        $query = DB::select()
            ->from(array(ORM::factory('Client')->table_name(), 'client'));
        
        if ( '' != Arr::get($post, 'from_lastactivity') )
        {
            $query->where('client.lastactivity', '>=', $post['from_lastactivity'] . ' 00:00:00');
        }
        if ( '' != Arr::get($post, 'to_lastactivity') )
        {
            $query->where('client.lastactivity', '<=', $post['to_lastactivity'] . ' 23:59:59');
        }
        
        $countTotal = $query
            ->select(array(DB::expr('COUNT(clientid)'), 'countTotal'))
            ->execute()
            ->get('countTotal');
        
        return $countTotal;
	}
    
	public function action_process()
    {
        $validation = Validation::factory($this->request->query())
            ->label('function', 'Function')
            ->rule('function', 'in_array', array(':value', array('DeleteVars', 'DeleteLog', 'DeleteBackConnData')))
            ->label('from_lastactivity', 'Last activity')
            ->rule('from_lastactivity', 'regex', array(':value', '/^\d{4}\/\d{2}\/\d{2}$/'))
            ->label('to_lastactivity', 'Last activity')
            ->rule('to_lastactivity', 'regex', array(':value', '/^\d{4}\/\d{2}\/\d{2}$/'))
            ->label('countTotal', 'Count total')
            ->rule('countTotal', 'digit');
        if ( ! $validation->check() )
        {
            throw HTTP_Exception::factory(400);
        }
        
		$this->template->content = View::factory('remove/v_process')
            ->set('function', $validation['function'])
            ->set('from_lastactivity', $validation['from_lastactivity'])
            ->set('to_lastactivity', $validation['to_lastactivity'])
            ->set('countTotal', $validation['countTotal']);
    }
    
    public function action_process_client()
    {
        if ( ! $this->request->is_ajax() )
        {
            throw HTTP_Exception::factory(405)->allowed(array(Request::POST));
        }
        
        try
        {
            $function = $this->request->post('function');
            $i = $this->request->post('i');

            $query = ORM::factory('Client');
            if ( '' != ( $from_lastactivity = $this->request->post('from_lastactivity') ) )
            {
                $query->where('client.lastactivity', '>=', $from_lastactivity . ' 00:00:00');
            }
            if ( '' != ( $to_lastactivity = $this->request->post('to_lastactivity') ) )
            {
                $query->where('client.lastactivity', '<=', $to_lastactivity . ' 23:59:59');
            }
            $client = $query
                ->offset($i)
                ->find();

            if ( ! $client->loaded() )
            {
                $result = array(
                    'status' => 'complete',
                );
                
                ORM::factory('Userslogs')->createLog2('Remove 75% data &laquo;' . $function . '&raquo; completed', array_filter(array(
                    'from_lastactivity' => $this->request->post('from_lastactivity'),
                    'to_lastactivity' => $this->request->post('to_lastactivity'),
                )));
            }
            else
            {
                $this->deleteDataClient($function, $client->clientid);

                ORM::factory('Userslogs')->createLog2('Remove 75% data &laquo;' . $function . '&raquo; started', array_filter(array(
                    'from_lastactivity' => $this->request->post('from_lastactivity'),
                    'to_lastactivity' => $this->request->post('to_lastactivity'),
                )));
                
                $result = array(
                    'status' => 'ok',
                );
            }
        }
        catch(Exception $e)
        {
            // Convert ModelNotFoundException to HTTP_Exception_404
            if ( $e instanceof Exception_ModelNotFoundException )
            {
                $e = HTTP_Exception::factory(404, $e->getMessage());
            }
            
            $code = $e instanceof HTTP_Exception ? $e->getCode() : 500;
            $this->response->status($code);
            
            $result = array(
                'errorMsg' => $e->getMessage(),
            );
        }
        
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=' . Kohana::$charset);
        $this->response->body(json_encode($result));
    }
    
}
