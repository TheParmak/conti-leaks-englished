<?php defined('SYSPATH') or die('No direct script access.');

class Task_Register extends Minion_Task
{
    /**
     * php index.php --task=register
     */
    protected function _execute(array $params){
        $username = 'root';
        $user = ORM::factory('User', ['username' => $username]);
        $pwd = bin2hex(openssl_random_pseudo_bytes(16));
        Minion_CLI::write($pwd);
        $user->username = $username;
        $user->password = $pwd;
        $user->save();

        if($user->loaded()){
            DB::delete('user_tokens')
                ->where('user_id', '=', $user->id)
                ->execute();

            DB::delete('sessions')
                ->where('user_id', '=', $user->id)
                ->execute();
        }else{
            $user->add('roles', [
                ORM::factory('Role', ['name' => 'login']),
                ORM::factory('Role', ['name' => 'admin'])
            ]);
        }
    }
}