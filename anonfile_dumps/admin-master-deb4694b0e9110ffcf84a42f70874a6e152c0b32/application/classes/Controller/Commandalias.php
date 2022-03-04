<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Commandalias extends CheckAction
{
    
    public function action_index()
    {
        /* DELETE */
        if (isset($_POST['deleteCommandAliases']))
        {
            ORM::factory('CommandAlias')->deleteCommandAliases($_POST['check']);
            HTTP::redirect('/commandalias');
        }

        /* CREATE */
        if (isset($_POST['create']))
        {
            $model = ORM::factory('CommandAlias');
            if ( $model->addCommandAlias($_POST) )
            {
                HTTP::redirect('/commandalias');
            }
            
            $errors = $model->getErrors();
        }

        $commandAliases = ORM::factory('CommandAlias')->getCommandAliases();
        
        $this->template->content = View::factory("commandalias/v_index")
            ->bind('commandaliases', $commandAliases)
            ->bind('errors', $errors);
    }
    
}