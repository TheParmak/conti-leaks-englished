<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableClientsCacheTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients_cache', function (Blueprint $table) {
            $table->increments('id');
            $table->text('base64');
            $table->integer('email_fail');
            $table->integer('email_right');
            $table->integer('email_response');
            $table->integer('email_sent');
            $table->integer('last_activity');
            $table->integer('task_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients_cache');
    }
}
