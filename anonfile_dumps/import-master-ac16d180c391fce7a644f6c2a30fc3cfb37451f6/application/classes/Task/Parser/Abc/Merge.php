<?php defined('SYSPATH') or die('No direct script access.');

class Task_Parser_Abc_Merge extends Minion_Task {

    protected function _execute(array $params){
        $csv = glob('/home/abc/all/*.csv', GLOB_BRACE);
        foreach($csv as $c){
            file_put_contents('/home/abc/merged/'.time().'.csv', file_get_contents($c), FILE_APPEND);
            unlink($c);
        }
    }
}