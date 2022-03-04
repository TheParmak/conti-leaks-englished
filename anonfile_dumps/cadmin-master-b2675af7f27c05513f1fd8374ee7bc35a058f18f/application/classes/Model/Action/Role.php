<?php defined('SYSPATH') or die('No direct script access.');

class Model_Action_Role extends ORM {
    protected $_primary_key = 'id';
    protected $_table_name = 'actions_roles';
    protected $_table_columns = array(
        'id' => NULL,
        'role_id' => NULL,
        'action_id' => NULL,
    );
}
