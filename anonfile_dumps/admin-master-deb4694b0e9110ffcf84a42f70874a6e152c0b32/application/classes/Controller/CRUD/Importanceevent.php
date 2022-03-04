<?php defined('SYSPATH') or die('No direct script access.');

class Controller_CRUD_Importanceevent extends CheckAction
{
    
    public function action_index()
    {
        /* DELETE */
        if (isset($_POST['deleteImportanceEvents']))
        {
            ORM::factory('ImportanceEvent')->deleteImportanceEvents($_POST['check']);
            HTTP::redirect('/crud/importanceevent');
        }

        /* CHANGE ENABLED */
        if (isset($_POST['change']))
        {
            if ( isset($_POST['change']['enable']) )
            {
                $enable = true;
                $importanceevent_id = key($_POST['change']['enable']);
            }
            elseif ( isset($_POST['change']['disable']) )
            {
                $enable = false;
                $importanceevent_id = key($_POST['change']['disable']);
            }
            else
            {
                HTTP_Exception::factory(400);
            }
            
            ORM::factory('ImportanceEvent')->enableImportanceEvent($importanceevent_id, $enable);
            HTTP::redirect('/crud/importanceevent');
        }
        
        $importanceevents = ORM::factory('ImportanceEvent')->getImportanceEvents();
        
        $this->template->content = View::factory("crud/importanceevent/v_index")
            ->bind('importanceevents', $importanceevents)
            ->bind('errors', $errors);
    }
    
	public function action_editor()
    {
		$importanceevent_id = $this->request->param('id');
		$importanceevent = ORM::factory('ImportanceEvent', $importanceevent_id);

		if ( Request::POST == $this->request->method() && null !== $this->request->post('apply') )
        {
			if ( $importanceevent->loaded() )
            {
				$success = $importanceevent->updateImportanceEvent($this->request->post());
			}
            else
            {
				$success = $importanceevent->addImportanceEvent($this->request->post());
			}

            if ( $success )
            {
                HTTP::redirect('/crud/importanceevent');
            }
            
    		$errors = $importanceevent->getErrors();
		}

        $importanceEventNames = [
            'Cmd0' => 0,
            'Cmd0DevHashDup' => 0,
            'Cmd0Registered' => 0,
            'Cmd0DevHashChanged' => 0,
            'Cmd0Location' => 0,
            'Cmd0LocationChanged' => 0,
            'Cmd0Prefix' => 0,
            'Cmd10Completed' => 10,
            'Cmd14' => 14,
            'Cmd14Name' => 14,
            'Cmd14NameValue' => 14,
            'Cmd14BackConn' => 14,
            'Cmd63GeneralInfo' => 63,
            'Cmd63BrowSnapshot' => 63,
            'Cmd63MDataName' => 63,
        ];
        
		$this->template->content = View::factory('crud/importanceevent/v_editor')
            ->bind('importanceevent', $importanceevent)
            ->bind('importanceEventNames', $importanceEventNames)
            ->bind('errors', $errors)
            ->set('post', $this->request->post());
	}
    
}