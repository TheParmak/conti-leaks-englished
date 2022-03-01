<?php defined('SYSPATH') or die('No direct script access.');

class Model_User_Lastlogin extends ORM
{
    
    protected $_table_name = 'users_lastlogins';
    protected $_table_columns = [
        'id' => null,
        'user_id' => null,
        'ip' => null,
        'user_agent' => null,
        'is_restored_from_rememberme' => null,
        'logged_at' => null,
    ];

    public static function createLastlogin(Model_User $user, $ip, $user_agent, $is_restored_from_rememberme)
    {
        $user_lastlogin = ORM::factory('User_Lastlogin');
        $user_lastlogin->user_id = $user->id;
        $user_lastlogin->ip = $ip;
        $user_lastlogin->user_agent = $user_agent;
        $user_lastlogin->is_restored_from_rememberme = $is_restored_from_rememberme;
        $user_lastlogin->logged_at = time();
        $user_lastlogin->create();
        
        $last100Lastlogins = DB::select('id')
            ->from(ORM::factory('User_Lastlogin')->table_name())
            ->order_by('logged_at', 'DESC')
            ->limit(Kohana::$config->load('init.count_lastlogins'));
        DB::delete(ORM::factory('User_Lastlogin')->table_name())
            ->where('id', 'NOT IN', $last100Lastlogins)
            ->execute();
        
        return $user_lastlogin;
    }
    
    public function getLoggedAtColored()
    {
        $diff = time() - $this->logged_at;
        if ($diff < 15 * Date::MINUTE) {
            $color = 'success';
        } elseif ($diff < 2 * Date::HOUR) {
            $color = 'warning';
        } else {
            $color = 'danger';
        }
        
        return '<span class="text-' . $color . '">' . date("Y-m-d H:i:s", $this->logged_at) . '</span>';
    }
    
}
