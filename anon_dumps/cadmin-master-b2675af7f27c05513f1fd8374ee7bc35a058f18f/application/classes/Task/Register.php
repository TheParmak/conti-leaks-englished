<?php defined('SYSPATH') or die('No direct script access.');

class Task_Register extends Minion_Task
{
    /**
     * php index.php --task=register
     */
    protected function _execute(array $params)
    {
        $user = ORM::factory('User', ['username' => 'root']);

        if($user->loaded()){
            $pwd = bin2hex(openssl_random_pseudo_bytes(16));
            Minion_CLI::write($pwd);
            $user->username = 'root';
            $user->password = $pwd;
            $user->save();

            DB::delete('user_tokens')
                ->where('user_id', '=', $user->id)
                ->execute();

            DB::delete('sessions')
                ->where('user_id', '=', $user->id)
                ->execute();
        }else{
            $user->username = 'root';
            $user->password = 'ADSGH8dsgsdguyn54745sdgb32';
            $user->save();
            $user->add('roles', [
                ORM::factory('Role', ['name' => 'login']),
                ORM::factory('Role', ['name' => 'admin'])
            ]);
        }
    }
}