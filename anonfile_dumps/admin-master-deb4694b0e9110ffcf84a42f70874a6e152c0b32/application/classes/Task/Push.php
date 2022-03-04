<?php defined('SYSPATH') or die('No direct script access.');

class Task_Push extends Minion_Task{
    
    protected function _execute(array $params){
        Task_Helper::createWorker('PushBack', $this);
    }

    public function Worker(GearmanJob $job){
        $workload = json_decode($job->workload(), true);
        $post = $workload['post'];
        $client = ORM::factory('Client')
            ->selectByFilter($post, $workload['user_id']);

        $array_log = array();
        foreach($post as $key => $item){
            $array_log[$key] = $item;
        }
        $array_log['command'] = $workload['incode'];
        $array_log['param'] = $workload['params'];

        ORM::factory('Userslogs')->createLog2Task(
            "&laquo;Push Back&raquo",
            $array_log,
            $workload['user_id']
        );

        $model = ORM::factory('Command');
        $clients = $client->find_all()->as_array();

        foreach($clients as $c){
            $model->addCommandWithValidation([
                'client_id' => $c->id,
                'incode' => $workload['incode'],
                'params' => $workload['params']
            ]);
        }
    }
}