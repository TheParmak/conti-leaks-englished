<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Migration extends ORM
{

    protected $_table_columns = array(
        'id'         => array('type' => 'int'),
        'hash'       => array('type' => 'string'),
        'name'       => array('type' => 'string'),
    );
    
    public function is_installed()
    {
        try
        {
            $this->count_all();
        }
        catch (Database_Exception $a)
        {
            return false;
        }
        
        return true;
    }

}
