<?php defined('SYSPATH') or die('No direct script access.');

class Task_Stats extends Minion_Task {
	protected function _execute(array $params){
	    $count = 0;
	    $list = DB::select()
            ->from('brow_archive')
            ->where('data', 'LIKE', '%paypal%')
            ->where('type', '=', 81)
            ->execute()
            ->as_array();

        foreach ($list as $l){
            $data = explode(PHP_EOL, pg_unescape_bytea($l['data']));
            $count += count(preg_grep('#paypal#', $data));
        }

        Minion_CLI::write($count);
    }
}