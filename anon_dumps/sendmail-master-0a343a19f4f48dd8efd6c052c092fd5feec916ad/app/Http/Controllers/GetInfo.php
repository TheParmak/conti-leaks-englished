<?php

namespace App\Http\Controllers;

use App\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GetInfo extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $data = [];
        $data = $request->all();
        if($request->get('client') && $request->method() == 'GET'){
            $path = Client::getPathFile($request->get('client'));
            if(Storage::disk('client_data')->exists($path)){
                $data = explode("\r\n\r\n", Storage::disk('client_data')->get($path));
            }
        }else if($request->method() == 'POST' && !empty($data)){
            $file = $data['domain'];
            $file .= "\r\n";
            $file .= "\r\n";
            $file .= $data['type'];
            $file .= "\r\n";
            $file .= "\r\n";
            $file .= $data['rsa'];

            $ip = $data['base64'];
            $path = $ip[0].$ip[1].'/'.$ip[2].$ip[3].'/'.$ip[4].$ip[5].'/'.substr($ip, 6);
            Storage::disk('client_data')->put($path, $file);
            Client::where('base64', $ip)->delete();
            return redirect('/get_info')->with('message', 'Success!');
        }

        return view('get_info.form', compact(
            'data'
        ));
    }
}
