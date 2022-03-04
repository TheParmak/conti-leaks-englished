<?php defined('SYSPATH') or die('No direct script access.');

class Model_Remoteuser_Ip extends ORM
{

    protected $_table_name = 'remoteip';
	protected $_table_columns = array(
		'id' => NULL,
		'name' => NULL,
		'addrfrom' => NULL,
		'addrto' => NULL,
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
	public function addRemoteuserIp(array $data)
    {
        $data = Arr::extract($data, [
            ':argName',
            ':argAddrFrom',
            ':argAddrTo',
        ]);

        $validation = Validation::factory($data)
            ->label(':argName', 'Name')
            ->rule(':argName', 'not_empty')
            ->label(':argAddrFrom', 'From')
            ->rule(':argAddrFrom', 'not_empty')
            ->rule(':argAddrFrom', array($this, 'checkInet'), array(':validation', ':field'))
            ->label(':argAddrTo', 'To')
            ->rule(':argAddrTo', 'not_empty')
            ->rule(':argAddrTo', array($this, 'checkInet'), array(':validation', ':field'));

        if ( ! $validation->check() )
        {
            $this->errors = $validation->errors('validation');
            
            return false;
        }
        
        $query = "SELECT AddRemoteUserIP(:argName, :argAddrFrom, :argAddrTo)";
        $query = DB::query(Database::SELECT, $query);
        $query->parameters($data);
        $query->execute();
        
        return true;
	}
    
    public static function checkInet(Validation $validation, $field)
    {
        $inet = $validation[$field];
        $inet = explode('/', $inet);
        if ( ! filter_var($inet[0], FILTER_VALIDATE_IP, ['flags' => FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6]) )
        {
            $validation->error($field, 'check_inet_invalid_ip');
        }
    }
    
}
