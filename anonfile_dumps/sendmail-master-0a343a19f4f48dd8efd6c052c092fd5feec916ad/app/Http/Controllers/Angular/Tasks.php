<?php

namespace App\Http\Controllers\Angular;

use App\Email;
use App\Helper;
use App\Task;
use App\TaskQueueHistory;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Tasks extends Controller
{
    public function index(Request $request, $id = null)
    {
        $model = Task::findOrNew($id);
        $this->validate($request, Task::$rules, [], Task::attributes());
        $model->fill($request->all());
        $model->save();

        /*if (!Storage::disk('data')->exists($model->id . '/struct.json')) {
            Storage::disk('data')->put($model->id . '/struct.json', '{"groups": [], "files": []}');
        }

        $files = \json_decode(Storage::disk('data')->read($model->id . '/struct.json'), true);
        $files['groups'] = $request->get('groups');

        foreach ($files['groups'] as &$group) {
            !empty($group['id']) ?: $group['id'] = $this->getID(20);
        }

        Storage::disk('data')->put($model->id . '/struct.json', \json_encode($files));*/

        return json_encode(['id' => $model->id]);
    }

    public function genTask(Request $request, $id)
    {
        $task = Task::find($id);
        Storage::disk('data')->makeDirectory($id);
        /*$files = Storage::disk('local')->read($id . '/body');
        $files = $files['groups'];*/
        $email = Email::find($task->email_id);

        $json = [
            'from' => $email->from,
            'title' => $email->title,
            'body' => [
                'text' => base64_encode($email->simple_body),
                'html' => base64_encode($email->body),
            ],
            'agtype' => $email->type
        ];

        /*foreach ($files as $file){
            $json['files'][] = [
                'name' => basename($file),
                'file' => base64_encode(Storage::disk('local')->get($file)),
            ];
        }*/


        // ------------------------------- FILES -----------------------------

        $newGroups = \json_decode($request->getContent(), true);

        if (!Storage::disk('data')->exists($id . '/body')) {
            $json['groups'] = [];
        } else {
            $json['groups'] = \json_decode(Storage::disk('data')->read($id . '/body'), true)['groups'];
        }

        $filesList = [];
        foreach ($newGroups['groups'] as &$group) {
            !empty($group['id']) ?: $group['id'] = $this->getID(20);

            foreach ($group['files'] as &$file) {
                !empty($file['id']) ?: $file['id'] = $this->getID(20);
                $filesList[] .= $file['name'];
            }
        }

        /*foreach ($newGroups['groups'] as &$group) {
            $key = array_search($group['id'], array_column($json['groups'], 'id'));
            if (!is_bool($key)) {
                $filesList = array_merge($filesList, array_column($group['files'], 'name'));
                // $group['files'] = $json['groups'][$key]['files'];
            }
        }*/

        $filesList = array_unique($filesList);

        $json['groups'] = $newGroups['groups'];

        // --------- clear files

        $filesInFolder = Storage::files($id);
        $filesList = array_map(function (&$item) use ($id) {
            return $id . '/' . $item;
        }, $filesList);

        $drop = array_diff($filesInFolder, $filesList);
        Storage::delete($drop);

        // ------------------------------- FILES -----------------------------


        Storage::disk('data')->put($id.'/body', json_encode($json));

        if (file_exists(storage_path('data/'.$id.'/email_list'))) {
            unlink(storage_path('data/'.$id.'/email_list'));
        }
        if ($task->test_email != null) {
            $emails = implode(PHP_EOL, json_decode($task->test_email, true));
            Storage::disk('data')->put($id.'/email_list', $emails);
        } else {
            if (preg_match('#^resolving.*$#', $task->email_list)) {
                symlink(
                    storage_path($task->email_list.'/result'),
                    storage_path('data/'.$id.'/email_list')
                );
            } else {
                symlink(
                    storage_path($task->email_list),
                    storage_path('data/'.$id.'/email_list')
                );
            }
        }

        Storage::disk('data')->put($id.'/smtp_list', "/1\r\n/2\r\n/3\r\n/4\r\n/5\r\n/6\r\n/7\r\n/8");
    }

    public function execTask($id)
    {
        $client = Helper::getClient();
        $client->addTaskHigh("sendmail:tasks:execute", $id, null, md5($id));
        $client->runTasks();
    }

    public function resolveTask($base64)
    {
        $client = Helper::getClient();
        $client->addTaskHigh("sendmail:tasks:resolve", $base64);
        $client->runTasks();
    }

    public function backEndStatus()
    {
        return response()->json(['status' => Task::getBackEndStatus()]);
    }

    public function uploadFile(Request $request)
    {
        Storage::makeDirectory($request->get('id'));
        copy($_FILES['file']['tmp_name'], storage_path('app/'.$request->get('id').'/'.$_FILES['file']['name']));

        /*$files = \json_decode(Storage::disk('data')->read($request->get('id') . '/body'), true);

        foreach ($files['groups'] as &$group) {
            if ($group['id'] == $request->get('group')) {
                array_push($group['files'], [
                    'name' => $_FILES['file']['name'],
                    'id' => $this->getID(20)
                ]);
            }
        }

        Storage::disk('data')->put($request->get('id') . '/body', \json_encode($files));*/
    }

    public function readFile($directory, $file)
    {
        return response()->download(storage_path('app/'.$directory.'/'.$file));
    }

    public function downloadResult($directory, $file)
    {
        return response()->download(storage_path('resolving/'.$directory.'/'.$file));
    }

    public function deleteFile($directory, $file)
    {
        Storage::disk('local')->delete($directory.'/'.$file);
        if (empty(Storage::files($directory))) {
            Storage::disk('local')->deleteDirectory($directory);
        }
    }

    public function tasksList()
    {
        $model = Task::orderBy('id', 'DESC')->paginate();
        $response['total_items'] = $model->total();
        $response['current_page'] = $model->currentPage();
        $response['items_per_page'] = $model->perPage();

        $client = new \GuzzleHttp\Client();

        $task_queue_active = array_values(json_decode(
            $client->get(config('api.task_queue_active'))
                ->getBody()
                ->getContents(),
            true
        ));

        $task_queue_active_ids = collect(array_pluck($task_queue_active, 'body'))->map(function ($value) {
            return intval($value);
        })->toArray();

        $task_queue = array_values(json_decode(
            $client->get(config('api.task_queue'))
                ->getBody()
                ->getContents(),
            true
        ));

        $task_queue_ids = collect(array_pluck($task_queue, 'body'))->map(function ($value) {
            return intval($value);
        })->toArray();

        foreach ($model as $r) {
            $data = [
                'id' => $r->id,
                'name' => $r->name,
            ];

            if (in_array($r->id, $task_queue_active_ids)) {
//                $task_queue_active[array_search($r->id, $task_queue_active_ids)];
                $data['queue_active'] = true;
                $uri = config('api.task_queue_stat').$r->id;
                $status = $client->get($uri)->getBody()->getContents();
                $data['status'] = json_decode($status, true);
            } elseif (in_array($r->id, $task_queue_ids)) {
//                $task_queue[array_search($r->id, $task_queue_ids)];
                $data['queue_active'] = false;
                $history = TaskQueueHistory::find($r->id);
                if ($history) {
                    $data['status'] = $history;
                } else {
                    $data['status'] = [];
                }
            }

            $response['data'][] = $data;
        }

        return response()->json($response);
    }

    /**
     * Result
     */
    public function result()
    {
        $files = Storage::disk('data')->allFiles();
        $files = preg_grep('#result$#', $files);
        $response = [];

        foreach ($files as $f) {
            $id = explode('/', $f);
            $response[] = [
                'id' => $id[0],
                'task' => Task::find($id[0])->name,
                'good' => Task::getResult($id[0], 'good'),
                'bad' => Task::getResult($id[0], 'bad'),
            ];
        }

        return response()->json($response);
    }

    public function result_downloader(Request $request)
    {
        $post = $request->all();

        preg_match('#^(?:ftp\:\/\/)?(.*)\:(.*)@([\d\w-_\.]+)(\/.*)$#', $post['ftp'], $ftp);
        try {
            $exists = Storage::createFtpDriver([
                'host' => $ftp[3],
                'username' => $ftp[1],
                'password' => $ftp[2],
            ])->exists($ftp[4]);
            if ($exists) {
                return response()->json(['error' => 'File already exists'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }

        $client = Helper::getClient();
        $data = json_encode($post);
        $client->addTaskBackground("sendmail:email:list:resolve:downloader", $data, null, md5($data));
        $client->runTasks();
    }

    public function email_list()
    {
        $model = Storage::disk('emails')->allFiles();
        $gitignore = array_search('.gitignore', $model);
        if ($gitignore !== false) {
            unset($model[$gitignore]);
        }

        $response['total_items'] = count($model);
        $response['current_page'] = LengthAwarePaginator::resolveCurrentPage();
        $response['items_per_page'] = 100;
        $currentPageSearchResults = collect($model)->slice(
            ($response['current_page'] - 1) * $response['items_per_page'],
            $response['items_per_page']
        );
        $model = new LengthAwarePaginator(
            $currentPageSearchResults,
            count($model),
            $response['items_per_page']
        );

        foreach ($model as $m) {
            $response['data'][] = [
                'email' => base64_decode($m),
                'base64' => $m,
                'result' => Storage::disk('resolving')->exists($m.'/result'),
                'count' => Storage::exists('email_list.json') ? json_decode(Storage::get('email_list.json'), true)[$m]['count'] : 0,
                'good' => Task::getResult($m, 'good', 'resolve.json') ?: 0,
                'bad' => Task::getResult($m, 'bad', 'resolve.json') ?: 0,
            ];
        }

        return response()->json($response);
    }

    public function email_list_download(Request $request)
    {
        $post = $request->all();

        preg_match('#^(?:ftp\:\/\/)?(.*)\:(.*)@([\d\w-_\.]+)(\/.*)$#', $post['ftp'], $ftp);
        try {
            $exists = Storage::createFtpDriver([
                'host' => $ftp[3],
                'username' => $ftp[1],
                'password' => $ftp[2],
            ])->exists($ftp[4]);
            if ($exists) {
                return response()->json(['error' => 'File already exists'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }

        $client = Helper::getClient();
        $data = json_encode($post);
        $client->addTaskBackground("sendmail:email:list:downloader", $data, null, md5($data));
        $client->runTasks();
    }

    public function email_list_delete($base64)
    {
        Storage::disk('emails')->delete($base64);
        $dirs = Storage::disk('data')->allDirectories();
        foreach ($dirs as $dir) {
            if (Storage::disk('data')->exists($dir.'/email_list')) {
                $link = is_link(storage_path('data/'.$dir.'/email_list')) ? readlink(storage_path('data/'.$dir.'/email_list')) : storage_path('data/'.$dir.'/email_list');

                $link = basename($link);
                if ($link == $base64) {
                    Storage::disk('data')->delete($dir.'/email_list');
                }
            }
        }

        if (Storage::disk('resolving')->exists($base64)) {
            exec('rm -rf'.storage_path('resolving/'.$base64));
        }
    }

    public function result_delete($id)
    {
        $storage = Storage::disk('data');

        if ($storage->exists($id.'/result')) {
            $storage->delete($id.'/result');
        }

        if ($storage->exists($id.'/result.err')) {
            $storage->delete($id.'/result.err');
        }

        if ($storage->exists($id.'/result.email')) {
            $storage->delete($id.'/result.email');
        }
    }

    public function delete($id)
    {
        Task::destroy($id);
        if (Storage::disk('data')->exists($id)) {
            File::cleanDirectory(storage_path('data/'.$id));
            Storage::disk('data')->deleteDirectory($id);
        }
        $tasks = Task::where('email_list', $id)->get();
        foreach ($tasks as $task) {
            Storage::disk('data')->delete($id.'/email_list');
            $task->update(['email_list' => '']); // TODO null
        }
    }

    public function queue_stop($task_id)
    {
        $client = new \GuzzleHttp\Client();

        $status = json_decode($client->get(config('api.task_queue_stat').$task_id)->getBody()->getContents(), true);
        $history = new TaskQueueHistory();
        $history->id = $task_id;
        $history->fill($status);
        $history->save();

        $client->get(config('api.task_queue_stop').$task_id);
    }

    public function queue_delete($task_id)
    {
        $client = new \GuzzleHttp\Client();
        $client->get(config('api.task_queue_stop').$task_id);
        $client->get(config('api.task_queue_delete').$task_id);

        TaskQueueHistory::destroy($task_id);
    }

    public function queue_add($task_id, $start_from = null)
    {
        $url = config('api.task_queue_add').$task_id.'/'.$task_id;
        if ($start_from) {
            $path = is_link(storage_path('data/'.$task_id.'/email_list')) ? readlink(storage_path('data/'.$task_id.'/email_list')) : storage_path();
            $start_from = round(filesize($path) / 100 * $start_from);
            $url .= '/'.$start_from;
        }
        $client = new \GuzzleHttp\Client();
        $client->get($url);
    }

    public function queue_active()
    {
        $client = new \GuzzleHttp\Client();

        $task_queue_active = array_values(json_decode(
            $client->get(config('api.task_queue_active'))
                ->getBody()
                ->getContents(),
            true
        ));

        // TODO check "id" param in response
        $task_queue_active_ids = collect(array_pluck($task_queue_active, 'body'))->map(function ($value) {
            return intval($value);
        })->toArray();

        $tasks = Task::whereIn('id', $task_queue_active_ids)->pluck('name', 'id')->map(function ($v, $k) {
            return [
                'id' => $k,
                'name' => $v
            ];
        })->toArray();

        foreach ($tasks as $k => $t) {
            $uri = config('api.task_queue_stat').$t['id'];
            $status = $client->get($uri)->getBody()->getContents();
            $tasks[$k]['status'] = json_decode($status, true);
        }

        return response()->json($tasks);
    }

    // support

    public function getID($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
