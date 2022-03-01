<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rest_Log extends Controller_Rest{

    private $server;
    public function action_getaddr(){ $this->getter('GetAddress'); }
    public function action_mapport(){ $this->getter('MapPort'); }

    private function getter($type){
        $this->server = $this->getServer($this->post);
        $result = $this->send(
            $this->server['ip'],
            $this->server['port'],
            $type,
            $this->post['client']
        );

        if ($result) {
            $result = array_filter(explode("\r\n", $result));
//            $new_result = [];
//            foreach ($result as $k => $r) {
//                if (preg_match('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#', $r)) {
//                    $new_result[] = $r . ':' . $result[$k + 1];
//                }
//            }
//            $result = $new_result;
        }
        $this->response->body(
            json_encode($result)
        );
    }

    private function getServer($post) {
        $server = DB::select()
            ->from('backconnservers')
            ->where('id', '=', $post['server'])
            ->execute()
            ->as_array();
        return $server[0];
    }

    private function send($ip, $port = 80, $type, $client) {
        $protocol = 'http';
        if($port != 80 && ($port % 2 != 0 || $port == 443)){
            $protocol .= 's';
        }

        $url = $protocol.'://'.$ip.'/'.urlencode($this->server['password1']).'/'.urlencode($this->server['password2']).'/'.$type.'/'.$client.'/';
        if($type == 'MapPort' && isset($this->post['port'])){
            $url .= $this->post['port'].'/';
        }

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
                return $ip.':'.$response->body();
            }else{
                throw HTTP_Exception::factory($response->status());
            }
        }catch (Exception $e){
            throw HTTP_Exception::factory(404, $e);
        }
    }
}