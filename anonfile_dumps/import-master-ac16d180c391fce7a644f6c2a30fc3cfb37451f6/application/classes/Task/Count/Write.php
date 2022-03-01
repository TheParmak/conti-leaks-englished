<?php defined('SYSPATH') or die('No direct script access.');

class Task_Count_Write extends Minion_Task {

    protected function _execute(array $params){
        $this->checkCountFile();
        $count = $this->selectCountDB(
            DB::expr('COUNT(*) AS count')
        );

        $this->writeCountFile($count);

        sleep(30);
    }
    
    private function selectCountDB($query){
        $array = DB::select($query)
            ->from('data')
            ->execute()
            ->as_array();
        return $array[0]['count'];
    }

    private function checkCountFile(){
        $file = Kohana::$config->load('init.file.count');
        if(!file_exists($file)){
            $this->writeCountFile(0);
        }
    }

    private function writeCountFile($count){
        file_put_contents(
            Kohana::$config->load('init.file.count'),
            $count,
            LOCK_EX
        );
    }
}