<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersInsertRoot::class);

        if(App::environment() == 'local'){
            $this->call(EmailListSeeder::class);
            $this->call(EmailSeeder::class);
            $this->call(Task::class);
            $this->call(TaskQueue::class);
            $this->call(ClientsSeeder::class);
        }
    }
}
