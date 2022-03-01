<?php defined('SYSPATH') or die('No direct script access.');

class Model_ImportanceEvent extends ORM
{
    
	protected $_table_name = 'importanceevents';
	protected $_table_columns = [
		'id' => null,
		'enabled' => ['type' => 'bool'],
		'name' => null,
		'command' => null,
		'params' => null,
		'count' => null,
		'priority' => null,
		'add' => null,
		'mul' => null,
		'const' => null,
	];

    protected $errors;
    
    public function getErrors(){ return $this->errors; }

	public function addImportanceEvent(array $post)
    {
        $data = Arr::extract($post, [
            ':argName',
            ':argCommand',
            ':argParams',
            ':argCount',
            ':argPriority',
            ':argAdd',
            ':argMul',
            ':argConst',
            ':argEnabled',
        ]);
        $data[':argEnabled'] = '1' == Arr::get($data, ':argEnabled');
        $data[':argName'] = '' != $data[':argName'] ? $data[':argName'] : null;
        $data[':argCommand'] = '' != $data[':argCommand'] ? $data[':argCommand'] : null;
        $data[':argParams'] = '' != $data[':argParams'] ? $data[':argParams'] : null;
        $data[':argCount'] = '' != $data[':argCount'] ? $data[':argCount'] : null;
        $data[':argAdd'] = '' != $data[':argAdd'] ? $data[':argAdd'] : null;
        $data[':argMul'] = '' != $data[':argMul'] ? $data[':argMul'] : null;
        $data[':argConst'] = '' != $data[':argConst'] ? $data[':argConst'] : null;

        $validation = Validation::factory($data)
            ->label(':argName', 'Name')

            ->label(':argCommand', 'Command')
            ->rule(':argCommand', 'digit')

            ->label(':argParams', 'Params')

            ->label(':argCount', 'Count')
            ->rule(':argCount', 'digit')

            ->label(':argPriority', 'Priority')
            ->rule(':argPriority', 'not_empty')
            ->rule(':argPriority', 'digit')
            ->rule(':argPriority', array($this, 'unique_priority'), array(':validation', ':field'))

            ->label(':argAdd', 'Add')
            ->rule(':argAdd', 'numeric')

            ->label(':argMul', 'Mul')
            ->rule(':argMul', 'numeric')

            ->label(':argConst', 'Const')
            ->rule(':argConst', 'numeric')
                
            ->label(':argEnabled', 'Enabled');

        if ( ! $validation->check() )
        {
            $this->errors = $validation->errors('validation');
            
            return false;
        }
        
        $query = "SELECT AddImportanceEvent(:argEnabled, :argName, :argCommand, :argParams, :argCount, :argPriority, :argAdd, :argMul, :argConst)";
        $query = DB::query(Database::SELECT, $query);
        $query->parameters($data);
        $query->execute();

        ORM::factory('Userslogs')
            ->createLog2('add Importance Event', $data);

        return true;
	}
    
	public function updateImportanceEvent(array $post)
    {
        $data = Arr::extract($post, [
            ':argEventID',
            ':argName',
            ':argCommand',
            ':argParams',
            ':argCount',
            ':argPriority',
            ':argAdd',
            ':argMul',
            ':argConst',
            ':argEnabled',
        ]);
        $data[':argEnabled'] = '1' == Arr::get($data, ':argEnabled');
        $data[':argName'] = '' != $data[':argName'] ? $data[':argName'] : null;
        $data[':argCommand'] = '' != $data[':argCommand'] ? $data[':argCommand'] : null;
        $data[':argParams'] = '' != $data[':argParams'] ? $data[':argParams'] : null;
        $data[':argCount'] = '' != $data[':argCount'] ? $data[':argCount'] : null;
        $data[':argAdd'] = '' != $data[':argAdd'] ? $data[':argAdd'] : null;
        $data[':argMul'] = '' != $data[':argMul'] ? $data[':argMul'] : null;
        $data[':argConst'] = '' != $data[':argConst'] ? $data[':argConst'] : null;

        $validation = Validation::factory($data)
            ->label(':argEventID', 'EventID')
            ->rule(':argEventID', 'not_empty')
            ->rule(':argEventID', 'digit')
                
            ->label(':argName', 'Name')

            ->label(':argCommand', 'Command')
            ->rule(':argCommand', 'digit')

            ->label(':argParams', 'Params')

            ->label(':argCount', 'Count')
            ->rule(':argCount', 'digit')

            ->label(':argPriority', 'Priority')
            ->rule(':argPriority', 'not_empty')
            ->rule(':argPriority', 'digit')
            ->rule(':argPriority', array($this, 'unique_priority'), array(':validation', ':field'))

            ->label(':argAdd', 'Add')
            ->rule(':argAdd', 'numeric')

            ->label(':argMul', 'Mul')
            ->rule(':argMul', 'numeric')

            ->label(':argConst', 'Const')
            ->rule(':argConst', 'numeric')
                
            ->label(':argEnabled', 'Enabled');

        if ( ! $validation->check() )
        {
            $this->errors = $validation->errors('validation');
            
            return false;
        }
        
        $query = "SELECT ChangeImportanceEvent(:argEventID, :argEnabled, :argName, :argCommand, :argParams, :argCount, :argPriority, :argAdd, :argMul, :argConst)";
        $query = DB::query(Database::SELECT, $query);
        $query->parameters($data);
        $query->execute();

        ORM::factory('Userslogs')
            ->createLog2('change Importance Event', $data);

        return true;
	}

    public function unique_priority(Validation $validation, $field)
    {
        $priority = $validation[$field];
        $importanceevent = ORM::factory('ImportanceEvent')
            ->where('id', '<>', Arr::get($validation, ':argEventID', 0))
            ->where('priority', '=', $priority)
            ->find();
        if ( $importanceevent->loaded() )
        {
            $validation->error($field, 'unique_priority', [
                $priority,
                '<a target="_blank" href="/crud/importanceevent/editor/' . $importanceevent->id . '">Importance Event #' . $importanceevent->id . '</a>',
            ]);
        }
    }
    
	public function GetImportanceEvents()
    {
		$query = "SELECT GetImportanceEvents()";
		$query = DB::query(Database::SELECT, $query);
		$result = $query->execute()->as_array();
		$importanceEvents = [];
		foreach($result as $item)
        {
			$tmp = str_getcsv(trim($item['getimportanceevents'], '()'));
            
            $importanceEvent = [];
            $importanceEvent['id'] = $tmp[0];
            $importanceEvent['enabled'] = 't' == $tmp[1];
            $importanceEvent['name'] = $tmp[2];
            $importanceEvent['command'] = $tmp[3];
            $importanceEvent['params'] = $tmp[4];
            $importanceEvent['count'] = $tmp[5];
            $importanceEvent['priority'] = $tmp[6];
            $importanceEvent['add'] = $tmp[7];
            $importanceEvent['mul'] = $tmp[8];
            $importanceEvent['const'] = $tmp[9];
            
            $importanceEvents[] = $importanceEvent;
		}
        
		return $importanceEvents;
	}

	public function enableImportanceEvent($importanceevent_id, $enable)
    {
        $data = [];
        $data[':argEventID'] = $importanceevent_id;
        $data[':argEnabled'] = $enable;
        
        $query = "SELECT EnableImportanceEvent(:argEventID, :argEnabled)";
        $query = DB::query(Database::SELECT, $query);
        $query->parameters($data);
        $query->execute();

        ORM::factory('Userslogs')->createLog(($enable ? 'enable' : 'disable') . ' ImportanceEvent ' . $importanceevent_id);
	}
    
	public function deleteImportanceEvents()
    {
		foreach($_POST['check'] as $item)
        {
			$query = "SELECT DeleteImportanceEvent(:argEventID)";
			$query = DB::query(Database::SELECT, $query);
			$query->parameters($item);
			$query->execute();
            
            ORM::factory('Userslogs')
                ->createLog2('delete Importance Event', $item);
		}
	}

}
