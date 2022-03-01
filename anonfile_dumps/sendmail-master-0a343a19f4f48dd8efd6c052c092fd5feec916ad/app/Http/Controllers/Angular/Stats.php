<?php

namespace App\Http\Controllers\Angular;

use App;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class Stats extends Controller
{
    public function index(Request $request, $id = null)
    {
        $response = [];

        if (App::environment() != 'local') {
            $client = new Client();
            $response = $client->get(config('api.stat'), ['http_errors' => false])
                ->getBody()
                ->getContents();
        } else {
            $response = Storage::get('resolve_stat.json');
        }

        $response = json_decode($response, true);
        $response['in_process_pr'] = intval($response['in_process_pr']);
        $response['processed_pr'] = intval($response['processed_pr']);

        return json_encode($response);
    }
}
