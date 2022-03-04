<?php defined('SYSPATH') or die('No direct script access.');

class Task_Autoclear_Session extends Minion_Task
{
    protected function _execute(array $params)
    {
        $type = Session::$default;
        if ('database' != $type) {
            return;
        }
        
        // Load the configuration for this type
        $config = Kohana::$config->load('session')->get($type);

        // Set the session class name
        $class = 'Session_'.ucfirst($type);
        
        // Create a new session instance
        $session = new $class($config);
        
        $session->gc();
        
        sleep(Date::MINUTE);
    }
}