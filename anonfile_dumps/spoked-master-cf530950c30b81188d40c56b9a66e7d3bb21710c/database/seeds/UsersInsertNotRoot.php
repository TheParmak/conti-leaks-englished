<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersInsertNotRoot extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'user',
            'password' => bcrypt(env('WEB_USER_PASSWORD', 'suidgbn8SGdgbs67dgsduigsbdduygb7s')),
        ]);
    }
}
