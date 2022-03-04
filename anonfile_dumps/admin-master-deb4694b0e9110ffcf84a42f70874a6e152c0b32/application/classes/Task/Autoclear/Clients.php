<?php defined('SYSPATH') or die('No direct script access.');

class Task_Autoclear_Clients extends Minion_Task{

    /**
     * /usr/bin/nice -n 10 /usr/bin/ionice -c2 -n7 /usr/bin/php index.php --task=autoclear:clients > /tmp/clear.txt
     */
    protected function _execute(array $params){
        $dump_path = '/tmp/auto_clear_ids.json';
        $lock_path = '/tmp/auto_clear.lock';
        $ids = [];
        if(file_exists($dump_path)){
            $ids = json_decode(file_get_contents($dump_path), true);
        }else{
            $last_activity = DB::expr("NOW()::timestamp - INTERVAL '8 MONTH'");
            $ids = DB::select('id')
                ->from('clients')
                ->where('last_activity', '<', $last_activity)
                ->execute()
                ->as_array(null, 'id');

            file_put_contents($dump_path, json_encode($ids));
        }

        $tables = [
            'clients_usersystem' => 'clientid',
            'clients_comments' => 'clientid',
            'av' => 'client_id',
            'clients_counters' => 'client_id',
            'clients_events' => 'client_id',
            'clients_log' => 'client_id',
            'commands' => 'client_id',
            'configs' => 'client_id',
            'files' => 'client_id',
            'links' => 'client_id',
            'module_data' => 'client_id',
            'storage' => 'client_id',
            'storage_last' => 'client_id',
            'clients' => 'id', // clients table need last
        ];

        if(file_exists($lock_path)){
            $tables = array_slice($tables,
                array_search(
                    trim(file_get_contents($lock_path)),
                    array_keys($tables)
                )
            );
        }

        if($ids){
            foreach ($tables as $table => $column){
                file_put_contents($lock_path, $table);
                DB::delete($table)
                    ->where($column, 'IN', $ids)
                    ->execute();
                Minion_CLI::write($table);
            }
        }

        unlink($dump_path);
        unlink($lock_path);
        Minion_CLI::write('OK');
    }
}