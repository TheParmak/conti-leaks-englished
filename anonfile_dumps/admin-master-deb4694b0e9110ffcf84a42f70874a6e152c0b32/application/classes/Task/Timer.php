<?php defined('SYSPATH') or die('No direct script access.');

class Task_Timer extends Minion_Task {

    protected function _execute(array $params){
        $records = DB::select()
            ->from('commands_idle')
            ->where('count', '=', 0)
            ->execute()
            ->as_array();

        $gearman = Task_Helper::getAdminClient();

        foreach ($records as $r){
            if($r['count'] == 0 && $r['timer'] > 0){
                $gearman->addTaskBackground(
                    "Timer:Trigger",
                    json_encode(Arr::extract($r, ['id', 'timer'])),
                    null,
                    strval($r['id'])
                );
            }
        }
        $gearman->runTasks();

        sleep(30);
    }
}