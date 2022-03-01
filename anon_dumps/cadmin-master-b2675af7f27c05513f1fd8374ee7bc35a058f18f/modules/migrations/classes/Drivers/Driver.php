<?php defined('SYSPATH') or die('No direct script access.');

abstract class Drivers_Driver
{

    /**
     * Valid types
     * @var array
     */
    protected $types = array(
        'big_primary_key' => array(
            'MySQL' => 'big int primary key NOT NULL AUTO_INCREMENT',
            'PostgreSQL' => 'bigserial primary key',
            'length' => false,
        ),
        'big_integer' => array(
            'MySQL' => 'big int',
            'PostgreSQL' => 'bigint',
            'length' => true,
        ),
        'binary' => array(
            'MySQL' => '',
            'PostgreSQL' => 'bytea',
            'length' => false,
        ),
        'boolean' => array(
            'MySQL' => 'tinyint(1)',
            'PostgreSQL' => 'boolean',
            'length' => false,
        ),
        'character' => array(
            'MySQL' => 'character',
            'PostgreSQL' => 'character',
            'length' => true,
        ),
        'date' => array(
            'MySQL' => 'date',
            'PostgreSQL' => 'date',
            'length' => false,
        ),
        'datetime' => array(
            'MySQL' => 'datetime',
            'PostgreSQL' => 'timestamp without time zone',
            'length' => false,
        ),
        'decimal' => array(
            'MySQL' => 'numeric',
            'PostgreSQL' => 'numeric',
            'length' => true,
        ),
        'double' => array(
            'MySQL' => 'double',
            'PostgreSQL' => 'double precision',
            'length' => true,
        ),
        'float' => array(
            'MySQL' => 'float',
            'PostgreSQL' => 'real',
            'length' => true,
        ),
        'integer' => array(
            'MySQL' => 'int',
            'PostgreSQL' => 'integer',
            'length' => true,
        ),
        'inet' => array(
            'MySQL' => 'varchar(45)',
            'PostgreSQL' => 'inet',
            'length' => false,
        ),
        'primary_key' => array(
            'MySQL' => 'int primary key NOT NULL AUTO_INCREMENT',
            'PostgreSQL' => 'serial primary key',
            'length' => false,
        ),
        'string' => array(
            'MySQL' => 'varchar',
            'PostgreSQL' => 'character varying',
            'length' => true,
        ),
        'text' => array(
            'MySQL' => 'text',
            'PostgreSQL' => 'text',
            'length' => true,
        ),
        'time' => array(
            'MySQL' => 'time',
            'PostgreSQL' => 'time with time zone',
            'length' => false,
        ),
        'timestamp' => array(
            'MySQL' => 'timestamp',
            'PostgreSQL' => 'timestamp with time zone',
            'length' => false,
        ),
    );

    /**
     * @var Database_Core
     */
    protected $db;
    protected $group;
    protected $primary_key = 'id';

    /**
     * Copy database object
     *
     * @param  Database_Core
     */
    public function __construct($group, $db)
    {
        $db_config = Kohana::$config->load('database')->$group;

        $this->group = $group;
        $this->db = $db;

        if ( ! empty($db_config['primary_key']))
            $this->primary_key = $db_config['primary_key'];
    }

    /**
     * Get primary key
     * 
     * @return string primary key
     */
    public function get_primary_key()
    {
        return $this->primary_key;
    }

    /**
     * Create Table
     *
     * Creates a new table
     *
     * $fields:
     *
     *      Associative array containing the name of the field as a key and the
     *      value could be either a string indicating the type of the field, or an
     *      array containing the field type at the first position and any optional
     *      arguments the field might require in the remaining positions.
     *      Refer to the TYPES function for valid type arguments.
     *      Refer to the FIELD_ARGUMENTS function for valid optional arguments for a
     *      field.
     *
     * @example
     *
     *      create_table (
     *          'blog',
     *          array (
     *              'title' => array ( 'string[50]', 'default' => "The blog's title." ),
     *              'date' => 'date',
     *              'content' => 'text'
     *          )
     *      )
     *
     * @param   string   Name of the table to be created
     * @param   array
     * @param   mixed    Primary key, false if not desired, not specified sets to 'id' column.
     *                   Will be set to auto_increment by default.
     * @return  boolean
     */
    abstract public function create_table($table_name, $fields, $primary_key = TRUE);

    /**
     * Drop a table
     *
     * @param string    Name of the table
     * @return boolean
     */
    abstract public function drop_table($table_name);

    /**
     * Rename a table
     *
     * @param   string    Old table name
     * @param   string    New name
     * @return  boolean
     */
    abstract public function rename_table($old_name, $new_name);

    /**
     * Add a column to a table
     *
     * @example add_column ( "the_table", "the_field", array('string[25]', 'null' => FALSE) );
     * @example add_coumnn ( "the_table", "int_field", "integer" );
     *
     * @param   string  Name of the table
     * @param   string  Name of the column
     * @param   array   Column arguments array, or just a type for a simple column
     * @return  bool
     */
    abstract public function add_column($table_name, $column_name, $params);

    /**
     * Rename a column
     *
     * @param   string  Name of the table
     * @param   string  Name of the column
     * @param   string  New name
     * @return  bool
     */
    abstract public function rename_column($table_name, $column_name, $new_column_name, $params);

    /**
     * Alter a column
     *
     * @param   string  Table name
     * @param   string  Columnn ame
     * @param   string  New column type
     * @param   array   Column argumetns
     * @return  bool
     */
    abstract public function change_column($table_name, $column_name, $params);

    /**
     * Remove a column from a table
     *
     * @param   string  Name of the table
     * @param   string  Name of the column
     * @return  bool
     */
    abstract public function remove_column($table_name, $column_name);

    /**
     * Add an index
     *
     * @param   string  Name of the table
     * @param   string  Name of the index
     * @param   string|array  Name(s) of the column(s)
     * @param   string  Type of the index (unique/normal/primary)
     * @return  bool
     */
    abstract public function add_index($table_name, $index_name, $columns, $index_type = 'normal');

    /**
     * Remove an index
     *
     * @param   string  Name of the table
     * @param   string  Name of the index
     * @return  bool
     */
    abstract public function remove_index($table_name, $index_name);

    /**
     * Add a foreign key
     * 
     * @param string table
     * @param string foreign table
     * @param string column of foreign table
     * @param string column name, 'default' => $to_table_$foreign_column
     * @return bool
     */
    abstract public function belongs_to($from_table, $to_table, $to_column = NULL, $from_column = NULL);

    /**
     * Add foreign key - reversive belongs_to
     * 
     * @param string $from_table
     * @param string $to_table
     * @param string $from_column
     * @param string $to_column
     * @return bool
     */
    public function has_one($from_table, $to_table, $from_column = NULL, $to_column = NULL)
    {
        return $this->belongs_to($to_table, $from_table, $from_column, $to_column);
    }

    /**
     * Add foreign key trough table
     * 
     * @param string $from_table
     * @param string $to_table
     * @param string $trough_table
     * @return bool
     */
    public function has_one_trough($from_table, $to_table, $trough_table)
    {
        $result = true;
        $result = $result && $this->belongs_to($to_table, $trough_table);
        $result = $result && $this->belongs_to($trough_table, $from_table);
        
        return $result;
    }

    /**
     * Add foreign keys, Multi-Multi trough $trough_table
     * 
     * @param string $from_table
     * @param string $to_table
     * @param string $trough_table
     * @return bool
     */
    public function has_many_trough($from_table, $to_table, $trough_table)
    {
        $result = true;
        $result = $result && $this->belongs_to($trough_table, $from_table);
        $result = $result && $this->belongs_to($trough_table, $to_table);
        
        return $result;
    }

    /**
     * Add foreign keys, Multi-Multi trough new table, will be created
     * 
     * @param string $from_table
     * @param string $to_table
     * @return bool
     */
    public function has_many($from_table, $to_table)
    {
        $result = true;
        $trough_table = $from_table . '_' . $to_table;
        $result = $result && $this->create_table($trough_table, array());
        $result = $result && $this->belongs_to($trough_table, $from_table);
        $result = $result && $this->belongs_to($trough_table, $to_table);
        
        return $result;
    }

    /**
     * Catch query exceptions
     *
     * @return bool
     */
    public function run_query($sql)
    {
        try
        {
            $test = $this->db->query($this->group, $sql, false);
        }
        catch (Database_Exception $e)
        {
            // Kohana::log('error', 'Migration Failed: ' . $e);
            echo $e->getMessage();
            exit();
            
            return FALSE;
        }
        
        return TRUE;
    }

    /**
     * Is this a valid type?
     *
     * @return bool
     */
    protected function is_type($type, $platform)
    {
        if ( ! isset($this->types[$type][$platform]) )
        {
            throw new InvalidArgumentException('Unsupported type `' . $type . '\'');
        }

        return $this->types[$type][$platform];
    }

    /**
     * If need length - true, else - false
     * 
     * @param string type
     * @return bool, int
     */
    protected function is_length($type)
    {
        if (isset($this->types[$type]['length']))
            return $this->types[$type]['length'];
        
        return false;
    }

    /**
     * Return quote string
     * 
     * @param string value
     * @return string
     */
    protected function quote($value)
    {
        return ('\'' . $value . '\'');
    }

    /**
     * Return default column length
     * 
     * @param type $type
     * @return int
     */
    protected function getDefault($type)
    {
        $default = Arr::get($this->defaults, $type);
        if (empty($default))
            return 0;
        else
            return $default;
    }

    /**
     * Get string for add column
     * 
     * @param string $field_name
     * @param string $params
     * @param string $driver
     * @return string
     * @throws Kohana_Exception
     */
    protected function compile_column($field_name, $params, $driver)
    {
        if (empty($params))
        {
            throw new Kohana_Exception('migrations.missing_argument');
        }

        $sql = $this->db->quote_column($field_name);

        $params = (array) $params;
        $query_params = array(
            'type' => 'integer',
            'null' => 'NULL',
            'default' => '',
        );
        $type = 'integer';
        $length = 0;

        foreach ($params as $key => $param)
        {
            if ($key === 0)
            {
                $query_params['type'] = $this->is_type($param, $driver);
                $type = $param;
                if ( in_array($param, array('primary_key', 'big_primary_key')) )
                    $query_params['null'] = '';
            }
            else
            {
                switch ($key)
                {
                    case 'type':
                        $query_params['type'] = $this->is_type($param, $driver);
                        break;
                    case 'null':
                        if (!$param)
                            $query_params['null'] = 'NOT NULL';
                        break;
                    case 'default':
                        if (is_string($param))
                            $param = $this->quote($param);
                        $query_params['default'] = $param;
                        break;
                    case 'length':
                        if ($param > 0 && $this->is_length($type))
                            $length = $param;
                        break;
                }
            }
        }

        foreach ($query_params as $key => $param)
        {
            if ($param !== '')
            {
                if ($key === 'default')
                    $sql .= ' default';
                $sql .= ' ' . $param;
                if ($key === 'type')
                {
                    if ($length == 0)
                        $length = $this->getDefault($type);
                    if ($length > 0)
                        $sql .= '(' . $length . ')';
                }
            }
        }

        return $sql;
    }
    
    /**
     * Start transaction
     * 
     */
    public function begin()
    {
        $this->db->begin();
    }
    
    /**
     * End Transaction
     * 
     */
    public function commit()
    {
        $this->db->commit();
    }

}
