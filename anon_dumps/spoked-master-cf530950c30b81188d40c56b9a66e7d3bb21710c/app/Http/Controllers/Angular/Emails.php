<?php

namespace App\Http\Controllers\Angular;

use App\Email;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class Emails extends Controller
{
    public function state_active_set($id){
        Storage::disk('config')->put('state.txt', $id);
        Email::genConf($id);
    }

    public function state_active_get(){
        $storage = Storage::disk('config');
        if($storage->exists('state.txt'))
            return $storage->get('state.txt');
        else
            return 0;
    }

    public function proxy_check() {
        $file_name = 'mail_proxies.json';
        $storage = Storage::disk('config');
        $config = $storage->exists($file_name) ? $storage->get($file_name) : "[]";
        $client = new \GuzzleHttp\Client(array(
            'verify' => false
        ));
        $results = [];
        $date = new \DateTime();

        foreach (json_decode($config) as $item)
        {
            try {
                $res = $client->request('GET', $item."?rnd=".$date->getTimestamp(), [
                    'http_errors' => false,
                    'headers'     => ['Cache-Control' => 'no-cache']
                ]);
                array_push($results, ['code' => $res->getStatusCode(), 'status' => $res->getReasonPhrase(), 'address' => $item]);
            } catch (RequestException $e) {
                array_push($results, ['code' => 500, 'status' => 'Server not found', 'address' => $item]);
            }
        }

        return $results;
    }
}
