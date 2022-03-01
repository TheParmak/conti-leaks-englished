<?php

use Illuminate\Database\Seeder;

class TaskQueue extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        $tasks = \App\Task::inRandomOrder()
            ->take(6)
            ->pluck('id')
            ->toArray();

        self::seedTaskQueue('task_queue.json', array_slice($tasks, 0, 3));
        self::seedTaskQueue('task_queue_active.json', array_slice($tasks, 3));
    }

    private function seedTaskQueue($file, $tasks){
        if(!Storage::exists($file)){
            $data = [];

            foreach($tasks as $id){
                $data[] = [
                    "body" => $id."/body",
                    "emails" => $id."/email_list",
                    "id" => "",
                    "output" => $id."/checked_emails",
                    "smtp" => $id."/smtp_list",
                    "start_from" => 0
                ];
            }
            Storage::put($file, json_encode($data));
        }
    }
}
