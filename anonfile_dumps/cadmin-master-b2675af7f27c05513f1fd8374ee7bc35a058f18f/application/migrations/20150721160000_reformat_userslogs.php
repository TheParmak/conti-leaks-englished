<?php defined('SYSPATH') or die('No direct script access.');

class Reformat_userslogs extends Migration
{
    public function up()
    {
        $userlogs = ORM::factory('Userslogs')->find_all();
        foreach($userlogs as $userlog)
        {
            if ( preg_match('/^&laquo;(?P<username>.+?)&raquo; logged in$/', $userlog->data, $matches) )
            {
                $userlog->user = $matches['username'];
                $userlog->data = 'Logged in';
            }
            elseif ( preg_match('/^&laquo;(?P<username>.+?)&raquo; create user &laquo;(?P<param1>.*)&raquo;$/', $userlog->data, $matches) )
            {
                $userlog->user = $matches['username'];
                $userlog->data = 'Create user &laquo;' . $matches['param1'] . '&raquo;';
            }
            elseif ( preg_match('/^&laquo;(?P<username>.+?)&raquo; edit user &laquo;(?P<param1>.*)&raquo;$/', $userlog->data, $matches) )
            {
                $userlog->user = $matches['username'];
                $userlog->data = 'Edit user &laquo;' . $matches['param1'] . '&raquo;';
            }
            elseif ( preg_match('/^&laquo;(?P<username>.+?)&raquo; create role &laquo;(?P<param1>.*)&raquo;$/', $userlog->data, $matches) )
            {
                $userlog->user = $matches['username'];
                $userlog->data = 'Create role &laquo;' . $matches['param1'] . '&raquo;';
            }
            elseif ( preg_match('/^&laquo;(?P<username>.+?)&raquo; reset password for user &laquo;(?P<param1>.*)&raquo;$/', $userlog->data, $matches) )
            {
                $userlog->user = $matches['username'];
                $userlog->data = 'Reset password for user &laquo;' . $matches['param1'] . '&raquo;';
            }
            elseif ( preg_match('/^&laquo;(?P<username>.+?)&raquo; download file &laquo;(?P<param1>.*)&raquo;$/', $userlog->data, $matches) )
            {
                $userlog->user = $matches['username'];
                $userlog->data = 'Download file &laquo;' . $matches['param1'] . '&raquo;';
            }
            elseif ( preg_match('/^&laquo;(?P<username>.+?)&raquo; download file &laquo;(?P<param1>.*)&raquo;$/', $userlog->data, $matches) )
            {
                $userlog->user = $matches['username'];
                $userlog->data = 'Download file &laquo;' . $matches['param1'] . '&raquo;';
            }
            elseif ( preg_match('/^upload update &laquo;(?P<param1>.*)&raquo; on filename &laquo;(?P<param2>.*)&raquo; file &laquo;(?P<param3>.*)&raquo;$/', $userlog->data, $matches) )
            {
                $userlog->data = 'Upload update &laquo;' . $matches['param1'] . '&raquo; on filename &laquo;' . $matches['param2'] . '&raquo; file &laquo;' . $matches['param3'] . '&raquo;';
            }
            elseif ( preg_match('/^upload filename &laquo;(?P<param1>.*)&raquo; file &laquo;(?P<param2>.*)&raquo;$/', $userlog->data, $matches) )
            {
                $userlog->data = 'Upload filename &laquo;' . $matches['param1'] . '&raquo; file &laquo;' . $matches['param2'] . '&raquo;';
            }

            $userlog->save();
        }
    }

    public function down()
    {
        // This is one way only
    }
}
