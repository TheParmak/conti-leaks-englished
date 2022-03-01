<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('login', ['as' => 'login', 'uses' => 'Auth\LoginController@showLoginForm']);
Route::post('login', ['as' => 'login.post', 'uses' => 'Auth\LoginController@login']);
Route::get('logout', ['as' => 'logout', 'uses' => 'Auth\LoginController@logout']);

Route::get('/file/{id}/{fileID}', 'Tasks@getFile');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', 'Tasks@index');
    Route::get('/clients/', 'Clients@index');
    Route::get('/edit/{id?}', 'Tasks@edit');
    Route::match(['get', 'post'], '/get_info', 'GetInfo@index');
    Route::match(['get', 'post'], '/email_list', 'Tasks@email_list')->name('email_list');
    Route::match(['get', 'post'], '/email_list/edit', 'Tasks@email_list_edit')->name('email_list_edit');
    Route::match(['get', 'post'], '/emails/', 'Emails@index');
    Route::match(['get', 'post'], '/emails/edit/{id?}', 'Emails@edit');
    Route::get('/emails/delete/{id}', 'Emails@delete');
    Route::get('/result', 'Tasks@result');
    Route::match(['get', 'post'], '/settings', 'Settings@index');
    Route::get('/stats', 'Stats@index');
});

Route::group(['middleware' => 'angular'], function () {
    Route::any('/api/emails/create/{id?}{slash?}', 'Angular\Tasks@index');
    Route::any('/api/emails/gen/{id}', 'Angular\Tasks@genTask')
        ->where(['id' => '[0-9]+']);
    Route::any('/api/emails/exec/{id}', 'Angular\Tasks@execTask')
        ->where(['id' => '[0-9]+']);
    Route::get('/api/emails/resolve/{base64}', 'Angular\Tasks@resolveTask')->name('emails_resolve');
    Route::any('/api/emails/upload/', 'Angular\Tasks@uploadFile');
    Route::get('/api/emails/read/{directory}/{file}', 'Angular\Tasks@readFile');
    Route::get('/api/emails/download_result/{directory}/{file}', 'Angular\Tasks@downloadResult');
    Route::get('/api/emails/delete/{directory}/{file}', 'Angular\Tasks@deleteFile');

    Route::get('/api/tasks', 'Angular\Tasks@tasksList');
    Route::get('/api/tasks/status', 'Angular\Tasks@backEndStatus');
    Route::get('/api/tasks/delete/{id}', 'Angular\Tasks@delete')->name('api_tasks_delete');
    Route::get('/api/tasks/result', 'Angular\Tasks@result');
    Route::get('/api/tasks/result/delete/{id}', 'Angular\Tasks@result_delete')->name('api_result_delete');
    Route::post('/api/tasks/result_downloader/', 'Angular\Tasks@result_downloader');
    Route::get('/api/tasks/email_list', 'Angular\Tasks@email_list')->name('api_email_list');
    Route::post('/api/tasks/email_list/download/', 'Angular\Tasks@email_list_download')->name('api_email_list_download');
    Route::get('/api/tasks/email_list/delete/{base64}', 'Angular\Tasks@email_list_delete')->name('api_email_list_delete');
    /* TASK QUEUE */
    Route::get('/api/tasks/queue/active', 'Angular\Tasks@queue_active')->name('api_task_queue_active');
    Route::get('/api/tasks/queue/add/{task_id}/{start_from?}', 'Angular\Tasks@queue_add')->name('api_task_queue_add');
    Route::get('/api/tasks/queue/stop/{task_id}', 'Angular\Tasks@queue_stop')->name('api_task_queue_stop');
    Route::get('/api/tasks/queue/delete/{task_id}', 'Angular\Tasks@queue_delete')->name('api_task_queue_delete');

    Route::get('/api/blacklist', 'Angular\Clients@blacklist');
    Route::get('/api/clients', 'Angular\Clients@clients');
    Route::post('/api/clients/clear', 'Angular\Clients@clientsClear');
    Route::get('/api/clients/new', 'Angular\Clients@clients_new');
    Route::post('/api/clients/online', 'Angular\Clients@clients_online');
    Route::post('/api/clients/set_white_list/', 'Angular\Clients@setWhiteList');
    Route::get('/api/clients/white_list/all/clear', 'Angular\Clients@whiteListClearAll')->name('white_list_clear_all');
    Route::get('/api/clients/white_list/all/add', 'Angular\Clients@whiteListAddAll')->name('white_list_add_all');
    Route::get('/api/clients/delete/old/{base64}', 'Angular\Clients@deleteOldClient');

    Route::get('/api/stats', 'Angular\Stats@index');
});


if(App::environment() == 'local'){
    Route::get('/task_stat/{task_id}', function(){
        return response()->json([
            "email_number_fail" => 1459902,
            "email_number_right" => 452501,
            "in_process" => 0,
            "in_process_pr" => 0.0,
            "processed" => 390144000,
            "processed_pr" => 74.20673746292935,
            "size" => 525752800
        ]);
    });

    Route::get('/task_queue', function(){
        return Storage::get('task_queue.json');
    });

    Route::get('/task_active', function(){
        return Storage::get('task_queue_active.json');
    });

    Route::get('/task_delete/{task_id}', function($task_id){
        $queue = json_decode(Storage::get('task_queue.json'), true);
        foreach($queue as $key => $v){
            if(intval($v['body']) == $task_id){
                unset($queue[$key]);
            }
        }
        Storage::put('task_queue.json', json_encode($queue));
    });

    Route::get('/task_stop/{task_id}', function($task_id){
        $queue_active = json_decode(
            Storage::get('task_queue_active.json'), true
        );

        foreach ($queue_active as $key => $item) {
            if(intval($item['body']) == $task_id){
                $queue = json_decode(Storage::get('task_queue.json'), true);
                foreach($queue as $k => $v){
                    if(intval($v['body']) == $task_id){
                        unset($queue_active[$key]);
                        continue 2;
                    }
                }
                $queue[] = $queue_active[$key];
                Storage::put('task_queue.json', json_encode($queue));
                unset($queue_active[$key]);
            }
        }
        Storage::put('task_queue_active.json', json_encode($queue_active));
    });

    Route::get('/task_add/{task_id}/{folder}/{start_from?}', function($task_id, $folder, $start_from = null){
        $files =['task_queue_active.json', 'task_queue.json'];
        $file = $files[array_rand($files)];
        $data = json_decode(Storage::get($file), true);
        $data[] = [
            "body" => $task_id."/body",
            "emails" => $task_id."/email_list",
            "id" => "",
            "output" => $task_id."/checked_emails",
            "smtp" => $task_id."/smtp_list",
            "start_from" => $start_from ?: 0,
        ];
        Storage::put($file, json_encode($data));
    });
}