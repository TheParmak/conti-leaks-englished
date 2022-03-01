<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColumnIsCompressedToZipInEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->boolean('is_compress_to_zip')->nullable()->change();
            $table->text('source_files_names_macro')->nullable()->change();
            $table->integer('max_patch_send')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->boolean('is_compress_to_zip')->change();
            $table->text('source_files_names_macro')->change();
            $table->integer('max_patch_send')->change();
        });
    }
}
