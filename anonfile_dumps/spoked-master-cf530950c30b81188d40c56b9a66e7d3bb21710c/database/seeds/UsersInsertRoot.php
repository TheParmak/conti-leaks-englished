<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersInsertRoot extends Seeder{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        User::create([
            'name' => 'root',
            'password' => bcrypt(env('WEB_ROOT_PASSWORD', 'SByidsg23Xbsdhg86sg7suyDtfg7sD')),
        ]);
    }
}
