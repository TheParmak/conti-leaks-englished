<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColumnDataInFilesTable extends Migration{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        if(env('DB_CONNECTION') == 'mysql'){
            DB::statement('ALTER TABLE files MODIFY data MEDIUMBLOB');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        if(env('DB_CONNECTION') == 'mysql') {
            DB::statement('ALTER TABLE files MODIFY data BLOB');
        }
    }
}
