<?php defined('SYSPATH') or die('No direct script access.');

class Task_Autoclear_CookiesArchive extends Minion_Task{

    protected function _execute(array $params){
        Kohana::$profiling = false;
        Log::$write_on_add = true;
        
        $interval = Kohana::$config->load('init.cookies_archive.autoremove_ttl');

        $subquery = DB::select('id_low', 'id_high', 'created_at')
            ->from('cookies_archive')
            ->where('created_at', '<', DB::expr("NOW() - INTERVAL '$interval SECOND'"))
            ->order_by('created_at', 'ASC')
            ->limit(10000);

        DB::delete('cookies_archive')
            ->where(DB::expr('(id_low, id_high, created_at)'), 'IN', $subquery)
            ->execute();
    }
}
