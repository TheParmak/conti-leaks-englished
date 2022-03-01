<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ajax_Server extends Controller {

    private $server;

    public function before() {
        if (Auth::instance()->logged_in() && Request::$initial->is_ajax())
            return parent::before();
        else
            HTTP::redirect('/');
    }

    protected function getServerOrm($serverName)
    {
        $server = ORM::factory('Server', $serverName);
        if ( ! $server->loaded() )
        {
            throw new Exception_ModelNotFoundException('Server &quot;' . $serverName . '&quot; not found.');
        }

        return $server;
    }

    public function action_get_list()
    {
        try
        {
            $serverName = $this->request->post('serverName'); // id
            $server = $this->getServerOrm($serverName);

            $result = $this->send($server);

            $rows = array_filter(explode("\r\n", trim($result)));
            $result = array();
            foreach($rows as $row)
            {
                if ( ! preg_match('/^(?P<port>\d+)\|(?P<ID>[A-F0-9]+)\|(?P<ip>\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})\|(?P<online>Y|N)$/', $row, $matches) )
                {
                    throw new ErrorException('Error parsing response');
                }

                $row = array();
                $row['ID'] = rtrim($matches['ID']);
                $row['ip'] = $matches['ip'];
                $row['port'] = $matches['port'];
                $row['online'] = $matches['online'];
                $result[] = $row;
            }

            // Filters
            $onlyOnline = $this->request->post('onlyOnline');
            if ( $onlyOnline )
            {
                $result = array_filter($result, function($row)
                {
                    return 'Y' == $row['onlyOnline'];
                });
            }
            $onlyOwn = $this->request->post('onlyOwn');
            if ( $onlyOwn )
            {
                $result = array_filter($result, function($row)
                {
                    $cid = Helper::getCid($row['ID']);
                    return ORM::factory('Client')
                        ->where('cid0', '=', $cid[0])
                        ->where('cid1', '=', $cid[1])
                        ->count_all();
                });
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

        $this->response->headers('Content-Type', 'application/json; charset=' . Kohana::$charset);
        $this->response->body(json_encode($result));
    }

    private function send($server) {
        $protocol = 'http';
        if($server->port != 80 && ($server->port % 2 != 0 || $server->port == 443)){
            $protocol .= 's';
        }

        $url = $protocol.'://'.$server->ip.'/'.urlencode($server->password1).'/'.urlencode($server->password2).'/GetList/';
        $response = Request::factory($url)->method('GET');
        $response->client()->options([
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_RETURNTRANSFER => true
        ]);

        Kohana_Exception::$error_view = 'rest_exception';
        try{
            $response = $response->execute();
            $status = $response->status();
            if($status == '200'){
                return $response->body();
            }else{
                throw HTTP_Exception::factory($response->status());
            }
        }catch (Exception $e){
            throw HTTP_Exception::factory(404, $e);
        }

//        $this->response->headers('Content-Type', 'application/json; charset=' . Kohana::$charset);
//        $this->response->body(json_encode($result));
    }
}