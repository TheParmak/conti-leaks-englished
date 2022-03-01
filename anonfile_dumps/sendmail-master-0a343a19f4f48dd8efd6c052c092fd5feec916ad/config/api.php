<?php
$data = [
//return [
    'local' => [
        'task_queue' => 'http://sendmail.local/task_queue/',
        'task_queue_active' => 'http://sendmail.local/task_active/',
        'task_queue_stat' => 'http://sendmail.local/task_stat/',     # /task_stat/{task_id}
        'task_queue_add' => 'http://sendmail.local/task_add/',       # /task_add/{task_id}/{folder}/{start_from?}
        'task_queue_delete' => 'http://sendmail.local/task_delete/', # /task_delete/{task_id}
        'task_queue_stop' => 'http://sendmail.local/task_stop/',     # /task_stop/{task_id}
    ],
    'production' => [
        'get_online' => 'http://localhost:8092/get_online',
        'white_list' => 'http://localhost:8092/client_list/white',
        'black_list' => 'http://localhost:8092/client_list/black',
        /* Resolving? */
        'stat' => 'http://localhost:8095/stat',
        /* TASK QUEUE */
        'task_queue' => 'http://localhost:8092/task_queue/',
        'task_queue_active' => 'http://localhost:8092/task_active/',
        'task_queue_stat' => 'http://localhost:8092/task_stat/',     # /task_stat/{task_id}
        'task_queue_add' => 'http://localhost:8092/task_add/',       # /task_add/{task_id}/{folder}/{start_from?}
        'task_queue_delete' => 'http://localhost:8092/task_delete/', # /task_delete/{task_id}
        'task_queue_stop' => 'http://localhost:8092/task_stop/',     # /task_stop/{task_id}
    ],
];

return $data[env('APP_ENV')];