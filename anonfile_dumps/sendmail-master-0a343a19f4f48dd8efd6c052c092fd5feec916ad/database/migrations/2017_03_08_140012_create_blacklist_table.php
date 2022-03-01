<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlacklistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blacklists', function (Blueprint $table) {
            $table->increments('id');
            $table->text('base64');
            $table->timestamps();
            $table->boolean('valid')->nullable(); // default null
        });
    }

    /**`
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blacklists');
    }
}
