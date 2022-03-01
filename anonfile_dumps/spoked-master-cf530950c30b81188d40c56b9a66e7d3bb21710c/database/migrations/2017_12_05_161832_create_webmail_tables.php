<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebmailTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(App::environment() == 'local') {
            Schema::enableForeignKeyConstraints();

            // WEBMAIL_HOSTS
            Schema::create("webmail_hosts", function (Blueprint $table) {
                $table->mediumIncrements('id');
                $table->string("name", 255)->default("");
            });

            // WEBMAIL COOKIES
            Schema::create("webmail_cookies", function (Blueprint $table) {
                $table->mediumIncrements('id');
                $table->mediumInteger("idhost")->default(0)->unsigned();
                $table->string("name", 255)->default("");
                $table->string("domain", 255)->default("");
            });

            // WEBMAIL COOKIES - references
            Schema::table("webmail_cookies", function (Blueprint $table) {
                $table->foreign("idhost")->references("id")->on("webmail_hosts")->onDelete("cascade")->onUpdate("cascade");
            });

            // WEBMAIL_SCRIPT TYPES
            Schema::create("webmail_script_types", function (Blueprint $table) {
                $table->tinyIncrements('id');
                $table->string("name", 255)->default("");
            });

            // WEBMAIL SCRIPTS
            Schema::create("webmail_scripts", function (Blueprint $table) {
                $table->mediumIncrements('id');
                $table->tinyInteger("idtype")->default(0)->unsigned();
                $table->mediumInteger("idhost")->default(0)->unsigned();
                $table->mediumText("script");
            });

            // WEBMAIL SCRIPTS - references
            Schema::table("webmail_scripts", function (Blueprint $table) {
                $table->foreign("idhost")->references("id")->on("webmail_hosts")->onDelete("cascade")->onUpdate("cascade");
                $table->foreign("idtype")->references("id")->on("webmail_script_types")->onDelete("cascade")->onUpdate("cascade");
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
        if(App::environment() == 'local') {
            Schema::dropIfExists('webmail_scripts');
            Schema::dropIfExists('webmail_cookies');
            Schema::dropIfExists('webmail_hosts');
            Schema::dropIfExists('webmail_script_types');
        }
    }
}
