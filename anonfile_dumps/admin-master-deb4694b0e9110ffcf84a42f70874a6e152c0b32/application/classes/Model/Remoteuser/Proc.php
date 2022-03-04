<?php defined('SYSPATH') or die('No direct script access.');

class Model_Remoteuser_Proc extends ORM
{

    protected $_table_name = 'remoteproc';
	protected $_primary_key = 'name';
	protected $_table_columns = array(
		'name' => NULL,
		'proc' => NULL,
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
	public function addRemoteuserProc(array $data)
    {
        $data = Arr::extract($data, [
            ':argName',
            ':argProc',
        ]);
        $data = array_map('trim', $data);

        $validation = Validation::factory($data)
            ->label(':argName', 'Name')
            ->rule(':argName', 'not_empty')
            ->label(':argProc', 'Proc')
            ->rule(':argProc', 'not_empty');

        if ( ! $validation->check() )
        {
            $this->errors = $validation->errors('validation');
            
            return false;
        }
        
        $query = "SELECT AddRemoteUserProc(:argName, :argProc)";
        $query = DB::query(Database::SELECT, $query);
        $query->parameters($data);
        $query->execute();
        
        return true;
	}
    
}
