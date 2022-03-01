<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueIndexBase64ForBlacklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blacklists', function (Blueprint $table) {
            if(App::environment() != 'local'){
                $table->unique('base64');
            }
            $table->index('updated_at');
            $table->index(['updated_at', 'valid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blacklists', function (Blueprint $table) {
            if(App::environment() != 'local') {
                $table->dropUnique('base64');
            }
            $table->dropIndex('updated_at');
            $table->dropIndex(['updated_at', 'valid']);
        });
    }
}
