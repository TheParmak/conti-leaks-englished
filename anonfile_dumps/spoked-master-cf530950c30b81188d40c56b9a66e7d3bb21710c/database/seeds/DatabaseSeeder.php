<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
         $this->call(UsersInsertRoot::class);
         $this->call(UsersInsertNotRoot::class);
         $this->call(WebmailSeeder::class);

        if(App::environment() == 'local'){
            $this->call(SeederStatistics::class);
        }
    }
}
