<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Configs extends Controller
{
    public function index(Request $request){
        if(Auth::user()->name != 'root'){
            return redirect(route('emails_index'))->setStatusCode(403);
        }
        $file_name = 'database.json';
        $storage = Storage::disk('config');
        $config = json_decode(
            $storage->get($file_name)
        );

        if($request->isMethod('post')){
            $storage->put(
                $file_name,
                json_encode(
                    Arr::except($request->all(), '_token')
                )
            );
            return redirect(route('config_database'))->with('success', 'Config has ben updated!');
        }

        return view('configs.database', compact(
            'config'
        ));
    }

    public function general(Request $request){
        if(Auth::user()->name != 'root'){
            return redirect(route('emails_index'))->setStatusCode(403);
        }

        $file_name = 'general.json';
        $storage = Storage::disk('config');
        if(!$storage->exists($file_name)){
            $storage->put($file_name,
                json_encode(['ServerPort' => '', 'UseSSL' => ''])
            );
        }
        $config = json_decode(
            $storage->get($file_name)
        );

        if($request->isMethod('post')){
            $storage->put(
                $file_name,
                json_encode(
                    Arr::except($request->all(), '_token')
                )
            );
            return redirect(route('config_general'))->with('success', 'Config has ben updated!');
        }

        return view('configs.general', compact(
            'config'
        ));
    }

    public function global_macros(Request $request){
        $file_name = 'global_macros.txt';
        $storage = Storage::disk('config');
        $config = $storage->get($file_name);

        if($request->isMethod('post')){
            $storage->put(
                $file_name, $request->get('global')
            );
            return redirect(route('config_global_macros'))->with('success', 'Config has ben updated!');
        }

        return view('configs.global_macros', compact(
            'config'
        ));
    }

    public function blacklist(Request $request){
        $file_name = 'blacklist.json';
        $storage = Storage::disk('config');
        $config = $storage->exists($file_name) ? $storage->get($file_name) : "[]";

        if($request->isMethod('post')){
            $storage->put(
                $file_name,
                json_encode(
                    array_values(
                        array_filter($request->get('config'))
                    )
                )
            );
            return redirect(route('config_blacklist'))->with('success', 'Config has ben updated!');
        }

        return view('configs.blacklist', compact(
            'config'
        ));
    }

    public function mail_proxies(Request $request) {
        $file_name = 'mail_proxies.json';
        $storage = Storage::disk('config');
        $config = $storage->exists($file_name) ? $storage->get($file_name) : "[]";

        if($request->isMethod('post')){
            $storage->put(
                $file_name,
                json_encode(
                    array_values(
                        array_filter($request->get('config'))
                    )
                )
            );
            return redirect(route('config_mail_proxies'))->with('success', 'Proxies has ben updated!');
        }

        return view('configs.mail_proxies', compact(
            'config'
        ));
    }
}
