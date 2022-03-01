<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSimpleBodyColumnInEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('emails', 'simple_body'))
        {
            Schema::table('emails', function(Blueprint $table) {
                $table->text('simple_body')->nullable();
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
        Schema::table('emails', function(Blueprint $table) {
            $table->dropColumn('simple_body');
        });
    }
}
