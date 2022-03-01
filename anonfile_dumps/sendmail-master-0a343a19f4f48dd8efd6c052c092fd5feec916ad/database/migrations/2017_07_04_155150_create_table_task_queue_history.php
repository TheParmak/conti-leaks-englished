<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTaskQueueHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('task_queue_history', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('email_number_fail');
            $table->integer('email_number_right');
            $table->integer('in_process');
            $table->float('in_process_pr', 3, 12);
            $table->integer('processed');
            $table->float('processed_pr', 3, 12);
            $table->integer('size');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('task_queue_history');
    }
}
