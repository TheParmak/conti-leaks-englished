<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->increments('id');
            $table->text('message_subject');
            $table->text('message_content');
            $table->text('test_address');
            $table->text('attach_type');
            $table->text('attach_path');
            $table->text('name');
            $table->boolean('is_compress_to_zip');
            $table->text('source_files_names_macro');
            $table->integer('max_patch_send');
            $table->boolean('sign_from_mail_server');
            $table->boolean('collect_from_address_book');
            $table->boolean('collect_from_out_box');
            $table->boolean('collect_from_in_box');
            $table->boolean('collect_from_other');
            $table->integer('address_in_message');
            $table->integer('send_interval_sec_min');
            $table->integer('send_interval_sec_max');
            $table->boolean('dry_run');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emails');
    }
}
