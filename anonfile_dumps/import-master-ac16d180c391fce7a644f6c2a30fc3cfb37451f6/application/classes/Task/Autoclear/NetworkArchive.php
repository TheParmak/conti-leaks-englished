<?php defined('SYSPATH') or die('No direct script access.');

class Task_Autoclear_NetworkArchive extends Minion_Task{

    protected function _execute(array $params){
        Kohana::$profiling = false;
        Log::$write_on_add = true;
        
        $interval = Kohana::$config->load('init.network_archive.autoremove_ttl');

        $subquery = DB::select('id_low', 'id_high', 'created_at')
            ->from('network_archive')
            ->where('created_at', '<', DB::expr("NOW() - INTERVAL '$interval SECOND'"))
            ->order_by('created_at', 'ASC')
            ->limit(10000);

        DB::delete('network_archive')
            ->where(DB::expr('(id_low, id_high, created_at)'), 'IN', $subquery)
            ->execute();
    }
}
