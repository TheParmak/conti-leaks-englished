<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFakeTableFroStatistics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(App::environment() == 'local') {
            Schema::create('mail_address', function (Blueprint $table) {
                $table->increments('id');
                $table->char('address', 164);
                $table->integer('connection_id');
            });

            Schema::create('connection_results', function (Blueprint $table) {
                $table->increments('id');
                $table->bigInteger('connection_id');
                $table->dateTime('add_date');
                $table->integer('outlook_total_address');
                $table->integer('outlook_additional_address');
                $table->integer('outlook_sent_address');
                $table->integer('thunderbird_version');
                $table->boolean('is_tb_addons_installed');
                $table->integer('outlook_email_blocked_by_name');
                $table->integer('outlook_email_blocked_by_domain');
                $table->integer('conn_addr_recv');
                $table->integer('conn_error_code');
                $table->char('sys_ver', 18);
                $table->char('outlook_ver', 18);
                $table->char('outlook_platform', 8);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(App::environment() == 'local'){
            Schema::dropIfExists('mail_address');
            Schema::dropIfExists('connection_results');
        }
    }
}
