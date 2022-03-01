<?php

namespace App\Http\Controllers;

use App;
use App\Console\Commands\Tasks\Execute;
use App\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Settings extends Controller{

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(Request $request){
        $md5 = null;
        $md5_error = false;
        if(!Storage::exists('md5_sendmail.app.src')){
            $md5 = md5_file(storage_path('app/sendmail.app.src'));
            Storage::put('md5_sendmail.app.src', $md5);
        }else{
            $md5 = Storage::get('md5_sendmail.app.src');
        }

        $file = file(storage_path('app/sendmail.app.src'));
        $data = $request->all();
        $task_size_array = preg_grep('#task_size#', $file);
        preg_match('#\d+#', end($task_size_array), $match);
        $data['task_size'] = $match[0];

        if($request->method() == 'POST' && !empty($request->all()) && $md5 == md5_file(storage_path('app/sendmail.app.src'))){
            $new_file = implode('', $file);
            if($request->get('task_size')){
                $key = array_keys($task_size_array)[0];
                $file[$key] = preg_replace('#\d+#', $request->get('task_size'), end($task_size_array));

                $new_file = implode('', $file);
            }

            Storage::put('sendmail.app.src', $new_file);
            if(App::environment() != 'local'){
                $client = Helper::getClient();
                $client->addTaskHigh("sendmail:tasks:execute", 'empty');
                $client->runTasks();
            }
            $md5 = md5_file(storage_path('app/sendmail.app.src'));
            Storage::put('md5_sendmail.app.src', $md5);

            return redirect('/settings');
        }
        if($md5 != md5_file(storage_path('app/sendmail.app.src'))){
            $md5_error = true;
        }

        return view('settings.form', compact(
            'data',
            'md5_error'
        ));
    }
}
