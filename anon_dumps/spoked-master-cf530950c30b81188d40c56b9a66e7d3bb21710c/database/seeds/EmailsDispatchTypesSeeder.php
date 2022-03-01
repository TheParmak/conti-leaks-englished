<?php

use Illuminate\Database\Seeder;

class EmailsDispatchTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('emails_dispatch_types')->insert([
            'name' => "actively"
        ]);

        DB::table('emails_dispatch_types')->insert([
            'name' => "sent"
        ]);

        DB::table('emails_dispatch_types')->insert([
            'name' => "draft"
        ]);
    }
}
