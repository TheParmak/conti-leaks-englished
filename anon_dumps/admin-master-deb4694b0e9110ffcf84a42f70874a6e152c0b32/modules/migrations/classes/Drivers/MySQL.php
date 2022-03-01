<?php defined('SYSPATH') or die('No direct script access.');

class Drivers_MySQL extends Drivers_Driver
{

    /**
     *
     * @var defaults 
     */
    protected $defaults = array(
        'binary' => '',
        'boolean' => '',
        'date' => '',
        'datetime' => '',
        'decimal' => '10,2',
        'float' => '',
        'integer' => 11,
        'primary_key' => '',
        'string' => 255,
        'text' => 4000,
        'time' => '',
        'timestamp' => '',
    );
    
    public function __construct($group, $db)
    {
        parent::__construct($group, $db);
        $this->begin();
    }

    public function __destruct()
    {
        $this->commit();
    }
    
    public function create_table($table_name, $fields, $primary_key = TRUE)
    {
        $sql = "CREATE TABLE " . $this->db->quote_table($table_name) . " (";

        // add a default id column if we don't say not to
        if ($primary_key === TRUE)
        {
            $primary_key = 'id';
            $fields = array_merge(array($this->primary_key => 'primary_key'), $fields);
        }

        foreach ($fields as $field_name => $params)
        {
            $params = (array) $params;

            $sql .= $this->compile_column($field_name, $params, 'MySQL');
            $sql .= ",";
        }
        $sql = rtrim($sql, ',') . ")";

        return $this->run_query($sql);
    }

    public function drop_table($table_name)
    {
        return $this->run_query("DROP TABLE $table_name");
    }

    public function rename_table($old_name, $new_name)
    {
        return $this->run_query("RENAME TABLE `$old_name`  TO `$new_name` ;");
    }

    public function add_column($table_name, $column_name, $params)
    {
        $sql = "ALTER TABLE `$table_name` ADD COLUMN " . $this->compile_column($column_name, $params, 'MySQL');

        return $this->run_query($sql);
    }

    public function rename_column($table_name, $column_name, $new_column_name, $params = NULL)
    {
        if ($params == NULL)
        { 
            $params = $this->get_column($table_name, $column_name);
        }
        $sql = "ALTER TABLE `$table_name` CHANGE `$column_name` " . $this->compile_column($new_column_name, $params, 'MySQL');

        return $this->run_query($sql);
    }

    public function change_column($table_name, $column_name, $params)
    {
        $sql = "ALTER TABLE `$table_name` MODIFY " . $this->compile_column($column_name, $params, 'MySQL');

        return $this->run_query($sql);
    }

    public function remove_column($table_name, $column_name)
    {
        return $this->run_query("ALTER TABLE $table_name DROP COLUMN $column_name ;");
    }

    public function add_index($table_name, $index_name, $columns, $index_type = 'normal')
    {
        $index_types = array(
            'normal' => 'INDEX',
            'unique' => 'UNIQUE KEY',
            'primary' => 'PRIMARY KEY',
        );
        $type = Arr::get($index_types, $index_type);
        if ( null === $type )
        {
            throw new InvalidArgumentException('Bad index type "' . $index_type . '"');
        }
        
        if ( 'primary' == $index_type )
        {
            $index_name = null;
        }

        $sql = "ALTER TABLE " . $this->db->quote_table($table_name) . " ADD $type " . ($index_name ? $this->db->quote_identifier($index_name) : "") . " (";

        foreach ((array) $columns as $column)
        {
            $sql .= $this->db->quote_column($column) . ", ";
        }

        $sql = rtrim($sql, ", ");
        $sql .= ")";

        return $this->run_query($sql);
    }

    public function remove_index($table_name, $index_name)
    {
        return $this->run_query("ALTER TABLE `$table_name` DROP INDEX `$index_name`");
    }
    
    public function belongs_to($from_table, $to_table, $from_column = NULL, $to_column = NULL)
    {
        if ($to_column === NULL)
            $to_column = $this->primary_key;
        if ($from_column === NULL)
            $from_column = $to_table . '_' . $to_column;
        $constraint = 'fk_' . $from_column;
        $sql = "ALTER TABLE $from_table ADD CONSTRAINT $constraint FOREIGN KEY ($from_column) REFERENCES $to_table ($to_column) ON DELETE RESTRICT ON UPDATE RESTRICT;";

        return $this->run_query($sql);
    }

    protected function get_common_type($type)
    {
        $length = '';
        if (strpos($type, '(') > 0)
            $length = substr($type, strpos($type, '(') + 1, strpos($type, ')') - strpos($type, '(') - 1);
        $type = substr($type, 0, strpos($type, '('));

        foreach ($this->types as $key => $value) {
            if ($value['MySQL'] === trim($type))
                return array(
                    'type' => $key,
                    'length' => $length
                );
        }

        return array(
            'type' => '',
            'length' => ''
        );
    }

    protected function get_column($table_name, $column_name)
    {
        $result = $this->db->query(Database::SELECT, "SHOW COLUMNS FROM `$table_name` LIKE '$column_name';", false);

        if ($result->count() !== 1)
        {
            throw new Kohana_Exception('migrations.column_not_found', $column_name, $table_name);
        }

        $result = $result->current();
        $type = $this->get_common_type($result['Type']);
        $params['type'] = $type['type'];
        if (!empty($type['length']))
            $params['length'] = $type['length'];

        if ($result['Null'] == 'NO')
            $params['null'] = FALSE;
        else
            $params['null'] = TRUE;

        if ($result['Default'])
            $params['default'] = $result['Default'];

        if ($result['Extra'] == 'auto_increment')
            $params['auto'] = TRUE;

        return $params;
    }

}