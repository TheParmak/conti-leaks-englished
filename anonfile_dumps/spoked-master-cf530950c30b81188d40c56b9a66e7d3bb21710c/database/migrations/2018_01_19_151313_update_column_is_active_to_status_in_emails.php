<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColumnIsActiveToStatusInEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('db:seed', ['--class' => EmailsDispatchTypesSeeder::class]);

        Schema::table('emails', function (Blueprint $table) {

            $table->dropColumn('is_active');

            $table->integer('status')->unsigned()->default(3);

            $table->foreign("status")->references("id")->on("emails_dispatch_types")->onDelete('cascade')->onUpdate('cascade');
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
            $table->dropForeign('emails_status_foreign');

            $table->dropColumn('status');

            $table->boolean("is_active")->default(false);
        });
    }
}
