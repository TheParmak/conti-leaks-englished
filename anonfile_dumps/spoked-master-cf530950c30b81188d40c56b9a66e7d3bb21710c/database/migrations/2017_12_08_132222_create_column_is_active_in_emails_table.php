<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColumnIsActiveInEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("emails", function (Blueprint $table) {
            $table->boolean("is_active");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*DB::unprepared('DROP TRIGGER `check_status_emails`');*/

        Schema::table("emails", function (Blueprint $table) {
            $table->dropColumn("is_active");
        });
    }
}
