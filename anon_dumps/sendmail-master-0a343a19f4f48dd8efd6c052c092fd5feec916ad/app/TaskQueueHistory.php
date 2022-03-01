<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskQueueHistory extends Model
{
    public $timestamps = false;
    protected $fillable = ['email_number_fail', 'email_number_right', 'in_process', 'in_process_pr', 'processed', 'processed_pr', 'size'];
    protected $table = 'task_queue_history';
}
