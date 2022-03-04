<?php defined('SYSPATH') or die('No direct script access.');

class Model_CommandAlias extends ORM
{

    protected $_table_name = 'commandaliases';
    protected $_table_columns = array(
        'id' => NULL,
        'alias' => NULL,
        'command' => NULL,
        'param' => NULL,
        'name' => NULL,
    );

    protected $errors;
    protected $clientID;
    
    public function getErrors(){ return $this->errors; }

    public function addCommandAlias(array $post)
    {
        $data = Arr::extract($post, [
            ':argAlias',
            ':argCommand',
            ':argParam',
            ':argName',
        ]);

        $validation = Validation::factory($data)
            ->label(':argAlias', 'Alias')
            ->rule(':argAlias', 'not_empty')

            ->label(':argCommand', 'Command')
            ->rule(':argCommand', 'not_empty')
            ->rule(':argCommand', 'digit')

            ->label(':argParam', 'Param')
            ->rule(':argParam', 'not_empty')

            ->label(':argName', 'Name')
            ->rule(':argName', 'not_empty')
            ->rule(':argName', array($this, 'checkRemoteUserExists'), array(':validation', ':field'));

        if ( ! $validation->check())
        {
            $this->errors = $validation->errors("validation");
            return false;
        }
        
        $query = "SELECT AddCommandAlias(:argAlias, :argCommand, :argParam, :argName)";
        $query = DB::query(Database::SELECT, $query);
        $query->parameters($data);
        $query->execute();

        ORM::factory('Userslogs')
            ->createLog2('add CommandAlias', $data);
        
        return true;
    }

    public function checkRemoteUserExists(Validation $validation, $field)
    {
        $remoteUserName = $validation[$field];
        $isRemoteUserExists = ORM::factory('Remoteuser', $remoteUserName);
        if ( ! $isRemoteUserExists->loaded() )
        {
            $validation->error($field, 'check_remote_user_exists');
        }
    }
    
    public function deleteCommandAliases(array $checks)
    {
        foreach($checks as $item)
        {
            $query = "SELECT DeleteCommandAlias(:argID)";
            $query = DB::query(Database::SELECT, $query);
            $query->parameters($item);
            $query->execute();
            
            ORM::factory('Userslogs')
                ->createLog2('delete CommandAlias', $item);
        }
    }
    
    public function getCommandAliases()
    {
        $query = "SELECT GetCommandAliases()";
        $query = DB::query(Database::SELECT, $query);
        $result = $query->execute()->as_array();
        $commandAliases = array();
        foreach($result as $item)
        {
            $tmp = str_getcsv(trim($item['getcommandaliases'], '()'));
            
            $link = [];
            $link['id'] = $tmp[0];
            $link['alias'] = $tmp[1];
            $link['command'] = $tmp[2];
            $link['param'] = $tmp[3];
            $link['name'] = $tmp[4];
            
            $commandAliases[] = $link;
        }
        
        return $commandAliases;
    }

}
