<?php defined('SYSPATH') or die('No direct script access.');

class Model_Remoteuser extends ORM
{

	protected $_primary_key = 'name';
	protected $_table_columns = array(
		'name' => NULL,
		'password' => NULL,
	);
    
    protected $errors;

    /**
     * Validation errors
     * 
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Add remote user
     * 
     * @param array $data
     */
	public function addRemoteuser(array $data)
    {
        $data = Arr::extract($data, [
            ':argName',
            ':argPassword',
        ]);

        $validation = Validation::factory($data)
            ->label(':argName', 'Name')
            ->rule(':argName', 'not_empty')
            ->label(':argPassword', 'Password')
            ->rule(':argPassword', 'not_empty');

        if ( ! $validation->check() )
        {
            $this->errors = $validation->errors('validation');
            
            return false;
        }
        
        $query = "SELECT AddRemoteUser(:argName, :argPassword)";
        $query = DB::query(Database::SELECT, $query);
        $query->parameters($data);
        $query->execute();
        
        return true;
	}
    
    
}
