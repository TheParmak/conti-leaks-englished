<?php defined('SYSPATH') or die('No direct script access.');

class Task_Parser_Storage extends Minion_Task{

    protected function _execute(array $params){
        $ids = DB::select('client_id')
            ->from('storage')
            ->where('key', '=', 'exc')
            ->execute()
            ->as_array(null, 'client_id');

        $tmp = DB::select('client_id')
            ->from('storage')
            ->where('key', '=', 'autorun')
            ->where('value', '=', 'SYSTEM')
            ->execute()
            ->as_array(null, 'client_id');

        $ids = array_intersect($ids, $tmp);

        $tmp = DB::select('client_id')
            ->from('storage')
            ->where('key', '=', 'NAT status')
            ->where('value', '=', 'client is behind NAT')
            ->execute()
            ->as_array(null, 'client_id');

        $ids = array_intersect($ids, $tmp);

        if($ids){
            $client = DB::expr("upper(lpad(to_hex(id_high), 16, '0') || lpad(to_hex(id_low), 16, '0')) AS client");

            $result = DB::select($client)
                ->from('clients')
                ->where('id', 'IN', $ids)
                ->execute()
                ->as_array(null, 'client');

            file_put_contents('/tmp/clients.txt', implode(PHP_EOL, $result));
        }else{
            Minion_CLI::write($ids);
            Minion_CLI::write('Empty');
        }
    }
}