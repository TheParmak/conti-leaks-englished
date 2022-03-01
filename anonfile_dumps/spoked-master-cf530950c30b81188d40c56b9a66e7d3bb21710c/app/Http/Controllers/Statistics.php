<?php

namespace App\Http\Controllers;

use App\ConnectionResults;
use App\MailAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\File;
use PhpParser\ErrorHandler\Collecting;

class Statistics extends Controller
{
    public function index(){
        $records = ConnectionResults::all();
        return view('statistics.list', compact([
            'records'
        ]));
    }

    public function get(Request $request, $id){
        /*$record = ConnectionResults::find($id);
        // TODO relation on mysql don't work, wtf?*/
        ini_set('memory_limit', '-1');

        $activeDispatch = \App\Email::where("id", "=", $id)->first(["updated_at", 'message_subject', 'status']);
        $name = $activeDispatch['message_subject'];
        $isActive = $activeDispatch['status'] === 1;
        $activeDispatch = $activeDispatch["updated_at"];

        $nextItem = \App\Email::whereIn("status", [1, 2])->where("updated_at", ">", $activeDispatch)->orderBy("updated_at", "ASC")->first(["updated_at"])['updated_at'];

        // if ($isActive) $nextItem = null;

        if ($nextItem == null)
        {

            $sys_ver = DB::select("select case when sys_ver is null or sys_ver = '' then 'Unknown' else substring(sys_ver,1,6) end as vers,count(*) as c from connection_results WHERE add_date > '{$activeDispatch}' GROUP BY vers ORDER BY c DESC;");
            $outlook_ver = DB::select("select case when outlook_ver = 'Webmail' then outlook_ver when outlook_ver is null or outlook_ver = '(null)' or outlook_ver = '' then 'Unknown' else substring(outlook_ver,1,6) end as vers,count(*) as c from connection_results WHERE add_date > '{$activeDispatch}' GROUP BY vers ORDER BY c DESC;");
            $outlook_platform = DB::select("select case when outlook_platform = 'Webmail' then outlook_platform when outlook_platform is null or outlook_platform = '(null)' or outlook_platform = 'none' or outlook_platform = '' then 'Unknown' else outlook_platform end as vers,count(*) as c from connection_results WHERE add_date > '{$activeDispatch}' GROUP BY vers ORDER BY c DESC; ");


            $stats = \App\ConnectionResults::where("add_date", ">", $activeDispatch)->get();
            $clients = DB::select("select count(*) as count from connection_results where add_date > '{$activeDispatch}';")[0]->count;
            $blocked_by_timeout = DB::select("select count(*) as count from connection_results where add_date > '{$activeDispatch}' and outlook_total_address is null;")[0]->count;

            $finished = $stats->max('add_date');

            $total_mail_address = DB::select("select count(*) as count from mail_address where connection_id in (select connection_id from connection_results where add_date >= '{$activeDispatch}')")[0]->count;
        } else {

            $sys_ver = DB::select("select case when sys_ver is null or sys_ver = '' then 'Unknown' else substring(sys_ver,1,6) end as vers,count(*) as c from connection_results WHERE add_date BETWEEN '{$activeDispatch}' AND '{$nextItem}' GROUP BY vers ORDER BY c DESC;");
            $outlook_ver = DB::select("select case when outlook_ver = 'Webmail' then outlook_ver when outlook_ver is null or outlook_ver = '(null)' or outlook_ver = '' then 'Unknown' else substring(outlook_ver,1,6) end as vers,count(*) as c from connection_results WHERE add_date BETWEEN '{$activeDispatch}' AND '{$nextItem}' GROUP BY vers ORDER BY c DESC;");
            $outlook_platform = DB::select("select case when outlook_platform = 'Webmail' then outlook_platform when outlook_platform is null or outlook_platform = '(null)' or outlook_platform = 'none' or outlook_platform = '' then 'Unknown' else outlook_platform end as vers,count(*) as c from connection_results WHERE add_date BETWEEN '{$activeDispatch}' AND '{$nextItem}' GROUP BY vers ORDER BY c DESC; ");


            $stats = \App\ConnectionResults::whereBetween("add_date", [$activeDispatch, $nextItem])->get();
            $clients = DB::select("select count(*) as count from connection_results where add_date between '{$activeDispatch}' AND '{$nextItem}';")[0]->count;
            $blocked_by_timeout = DB::select("select count(*) as count from connection_results where add_date between '{$activeDispatch}' AND '{$nextItem}' and outlook_total_address is null;")[0]->count;

            $finished = \App\ConnectionResults::where("add_date", '<', $nextItem)->max('add_date');

            $total_mail_address = DB::select("select count(*) as count from mail_address where connection_id in (select connection_id from connection_results where add_date >= '{$activeDispatch}' and add_date <= '{$nextItem}')")[0]->count;
        }

        $res = [
            'clients' => $clients,
            'blocked_by_timeout' => $blocked_by_timeout,
            "total" => $total_mail_address,
            "sent" => $stats->sum('outlook_sent_address'),
            "blocked_by_name" => $stats->sum('outlook_email_blocked_by_name'),
            "sys_ver" => $sys_ver,
            "outlook_ver" => $outlook_ver,
            "outlook_platform" => $outlook_platform,
            'name' => $name,
            'started' => $activeDispatch,
            'finished' => $finished
        ];

        unset($sys_ver);
        unset($outlook_ver);
        unset($outlook_platform);


        if($request->isMethod('post')){

            $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Content-type'        => 'text/plain',
                'Content-Disposition' => 'attachment; filename=StatEmails.txt',
                'Expires'             => '0',
                'Pragma'              => 'public'
            ];

            $callback = function() use ($stats)
            {
                $stream = fopen('php://output', 'w');

                // converting int to string because mysql wherein not worked with int
                $stats = $stats->pluck("connection_id")->map(function ($item, $key) { return (string)$item; });

                \App\MailAddress::whereIn('connection_id', $stats->all())->select('address')->distinct()->orderBy('address')->chunk(50000, function ($emails) use ($stream) {
                    foreach ($emails as $item) {
                        fputs($stream, $item->address."\r\n");
                        unset($item);
                    }
                });

                fclose($stream);
            };

            return response()->stream($callback, 200, $headers);
        }

        return view('statistics.get', compact([
            'res'
        ]));
    }
}
