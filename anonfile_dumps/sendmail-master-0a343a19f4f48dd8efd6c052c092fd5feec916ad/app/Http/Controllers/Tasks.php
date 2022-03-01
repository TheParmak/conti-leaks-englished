<?php

namespace App\Http\Controllers;

use App\Email;
use App\Task;
use App\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Tasks extends Controller{

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        return view('tasks.list');
    }

    public function edit($id = null){
        $task = Task::findOrNew($id);
        $files = [];

        if($id){

            if (!Storage::disk('data')->exists($id)) {
                Storage::disk('data')->makeDirectory($id);
                Storage::disk('data')->put($id . '/body', '{"groups": []}');
            }

            //$files = \json_decode(Storage::disk('data')->read($id . '/body'), true)['groups'];
            $files = \json_decode(Storage::disk('data')->read($id . '/body'), true);

            if (empty($files['groups'])) {
                $files['groups'] = [];
                Storage::disk('data')->put($id . '/body', \json_encode($files));
            }

            $files = $files['groups'];
        }

        $list = Storage::disk('emails')->allFiles();
        $gitignore = array_search('.gitignore', $list);
        if($gitignore !== false){
            unset($list[$gitignore]);
        }

        $emails_list = [];
        foreach ($list as $k => $v){
            $emails_list['emails/'.$v] = base64_decode($v);
            $resolving = Storage::disk('resolving');
            if($resolving->exists($v) && $resolving->exists($v.'/result')){
                $emails_list['resolving/'.$v] = base64_decode($v).' (resolved)';
            }
        }

        $emails = Email::pluck('title', 'id')->toArray();

        return view('tasks.edit', compact(
            'files',
            'emails',
            'task',
            'emails_list',
            'id'
        ));
    }

    /**
     * EmailsLists
     */
    public function email_list(){
        return view('tasks.email_list.list');
    }

    public function email_list_edit(Request $request){
        $post = $request->all();
        if($request->method() == 'POST' && !empty($post)){
            preg_match('#^(?:ftp\:\/\/)?(.*)\:(.*)@([\d\w-_\.]+)(\/.*)$#', $post['email_path'], $ftp);
            $client = Helper::getClient();

            if(empty($ftp)){
                $post['type'] = 'http';
                $data = json_encode($post);
                $client->addTaskBackground("sendmail:email:list:uploader", $data, null, md5($data));
                $client->runTasks();
                return redirect(route('email_list_edit'))->with('message', 'Success!');
            }else{
                $post['type'] = 'ftp';
                try{
                    preg_match('#^(?:ftp\:\/\/)?(.*)\:(.*)@([\d\w-_\.]+)(\/.*)$#', $post['email_path'], $ftp);
                    $config = [
                        'host' => $ftp[3],
                        'username' => $ftp[1],
                        'password' => $ftp[2],
                    ];

                    if(dirname($ftp[4]) != '/'){
                        $config['root'] = substr(dirname($ftp[4]), 1);
                    }

                    $exists = Storage::createFtpDriver($config)
                        ->exists(basename($ftp[4]));

                    if(Storage::disk('emails')->exists(base64_encode($post['name']))){
                        return redirect('/upload/email_list')
                            ->with('message_error', 'File with this name already exists')
                            ->with('name', $post['name'])
                            ->with('email_path', $post['email_path']);
                    }elseif($exists){
                        $data = json_encode($post);
                        $client->addTaskBackground("sendmail:email:list:uploader", $data, null, md5($data));
                        $client->runTasks();
                        return redirect(route('email_list_edit'))->with('message', 'Success!');
                    }else{
                        return redirect('/upload/email_list')
                            ->with('message_error', 'File not exists')
                            ->with('name', $post['name'])
                            ->with('email_path', $post['email_path']);
                    }
                }catch (\Exception $e){
                    return redirect(route('email_list_edit'))->with('message_error', $e->getMessage());
                }
            }
        }

        return view('tasks.email_list.edit');
    }

    /**
     * Results
     */
    public function result(){
        return view('tasks.result');
    }
}
