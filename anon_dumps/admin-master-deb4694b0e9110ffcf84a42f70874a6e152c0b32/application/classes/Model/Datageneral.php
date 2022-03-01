<?php defined('SYSPATH') or die('No direct script access.');

class Model_Datageneral extends ORM {
	protected $_primary_key = 'id';
	protected $_table_name = 'datageneral';
	protected $_table_columns = array(
		'id' => NULL,
		'datetime' => NULL,
		'clientid' => NULL,
		'data' => NULL,
	);
    
    public function getLastDatageneral(Model_Client $client)
    {
        $datagenerals = $client
            ->datagenerals
            ->order_by('id', 'DESC')
            ->limit(5)
            ->find_all();
        $lastDatageneral = null;
        foreach($datagenerals as $datageneral)
        {
            if ( null === $lastDatageneral || $lastDatageneral->datetime < $datageneral->datetime )
            {
                $lastDatageneral = $datageneral;
            }
        }
        
        if ( null === $lastDatageneral )
        {
            $lastDatageneral = ORM::factory('Datageneral');
        }
        
        return $lastDatageneral;
    }
    
}
