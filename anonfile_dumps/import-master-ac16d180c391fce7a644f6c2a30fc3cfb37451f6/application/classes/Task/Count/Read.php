<?php defined('SYSPATH') or die('No direct script access.');

class Task_Count_Read extends Minion_Task {

    protected function _execute(array $params){
        Task_Helper::createWorker('Count', $this);
    }

    public function Worker(GearmanJob $job){
        $file = null;
        $fp = fopen(
            Kohana::$config->load('init.file.count'), "r+"
        );

        if (flock($fp, LOCK_EX)) {
            $file = fread($fp, 1024);
            flock($fp, LOCK_UN);
        }

        fclose($fp);
        return $file;
    }
}