<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Migrations
 *
 * An open source utility for s/Code Igniter/Kohana inspired by Ruby on Rails
 *
 * Note: This is a work in progress. Merely a wrapper for all the currently
 * existing DBUtil class, and a CI adaptation of all the RoR conterparts.
 * many of the methods in this helper might not function properly in some DB
 * engines and other are not yet finished developing.
 * This helper is being released as a complement for the Migrations utility.
 *
 * Reworked for Kohana by Jamie Madill
 *
 * @package		Migrations
 * @author      Vladimir Zyablitskiy
 * @author		Matías Montes
 * @author      Jamie Madill
 */
class Kohana_Migration
{

    protected $driver;
    protected $db;
    
    // Override these two parameters to set behaviour of your migration
    private $group = 'default';
    private $output = FALSE;

    public function __construct($output = FALSE, $group = 'default')
    {
        $this->db = Database::instance($group);
        $db_config = Kohana::$config->load('database')->$group;

        // if need call driver with specific name
        $platform = strtolower($db_config['type']);
        switch ($platform)
        {
            case 'mysql':
            case 'mysqli':
                $platform = 'MySQL';
                break;
            case 'postgresql':
                $platform = 'PostgreSQL';
                break;
        }

        // Set driver name
        $driver = 'Drivers_' . $platform;

        $this->driver = new $driver($group, $this->db);
        $this->output = $output;
        $this->group = $group;
    }
    
    protected function log($string)
    {
        if ($this->output)
        {
            Minion_CLI::write($string);
        }
    }
    
    public function up()
    {
        throw new Kohana_Exception('migrations.abstract');
    }

    public function down()
    {
        throw new Kohana_Exception('migrations.abstract');
    }
    
    /**
     * Create Table
     *
     * Creates a new table
     *
     * $fields:
     *
     * 		Associative array containing the name of the field as a key and the
     * 		value could be either a string indicating the type of the field, or an
     * 		array containing the field type at the first position and any optional
     * 		arguments the field might require in the remaining positions.
     * 		Refer to the TYPES function for valid type arguments.
     * 		Refer to the FIELD_ARGUMENTS function for valid optional arguments for a
     * 		field.
     *
     * @example
     *
     * 		create_table (
     * 			'blog',
     * 			array (
     * 				'title' => array ( 'string[50]', default => "The blog's title." ),
     * 				'date' => 'date',
     * 				'content' => 'text'
     * 			),
     * 		)
     *
     * @param	string   Name of the table to be created
     * @param	array
     * @param	mixed    Primary key, false if not desired, not specified sets to 'id' column.
     *                   Will be set to auto_increment, serial, etc.
     * @return	boolean
     */
    public function create_table($table_name, $fields, $primary_key = TRUE)
    {
        $this->log("Creating table '$table_name'...");
        $ret = $this->driver->create_table($table_name, $fields, $primary_key);
        $this->log("DONE<br />");
        
        return $ret;
    }

    /**
     * Drop a table
     *
     * @param string    Name of the table
     * @return boolean
     */
    public function drop_table($table_name)
    {
        $this->log("Dropping table '$table_name'...");
        $ret = $this->driver->drop_table($table_name);
        $this->log("DONE<br />");
        
        return $ret;
    }

    /**
     * Rename a table
     *
     * @param   string    Old table name
     * @param   string    New name
     * @return  boolean
     */
    public function rename_table($old_name, $new_name)
    {
        $this->log("Renaming table '$old_name' to '$new_name'...");
        $ret = $this->driver->rename_table($old_name, $new_name);
        $this->log("DONE<br />");
        
        return $ret;
    }
    
    /**
     * Add a column to a table
     *
     * @example add_column ( "the_table", "the_field", array('string', 'limit[25]', 'not_null') );
     * @example add_coumnn ( "the_table", "int_field", "integer" );
     *
     * @param   string  Name of the table
     * @param   string  Name of the column
     * @param   array   Column arguments array
     * @return  bool
     */
    public function add_column($table_name, $column_name, $params)
    {
        $this->log("Adding column '$column_name' to table '$table_name'...");
        $ret = $this->driver->add_column($table_name, $column_name, $params);
        $this->log("DONE<br />");
        
        return $ret;
    }
    
    /**
     * Rename a column
     *
     * @param   string  Name of the table
     * @param   string  Name of the column
     * @param   string  New name
     * @return  bool
     */
    public function rename_column($table_name, $column_name, $new_column_name, $params)
    {
        $this->log("Renaming column '$column_name' in table '$table_name' to '$new_column_name'...");
        $ret = $this->driver->rename_column($table_name, $column_name, $new_column_name, $params);
        $this->log("DONE<br />");
        
        return $ret;
    }
    
    /**
     * Alter a column
     *
     * @param   string  Table name
     * @param   string  Columnn ame
     * @param   array   Column arguments
     * @return  bool
     */
    public function change_column($table_name, $column_name, $params)
    {
        $this->log("Changing column '$column_name' in table '$table_name'...");
        $ret = $this->driver->change_column($table_name, $column_name, $params);
        $this->log("DONE<br />");
        
        return $ret;
    }
    
    /**
     * Remove a column from a table
     *
     * @param   string  Name of the table
     * @param   string  Name of the column
     * @return  bool
     */
    public function remove_column($table_name, $column_name)
    {
        $this->log("Removing column '$column_name' in table '$table_name'...");
        $ret = $this->driver->remove_column($table_name, $column_name);
        $this->log("DONE<br />");
        
        return $ret;
    }

    /**
     * Add an index
     *
     * @param   string  Name of the table
     * @param   string  Name of the index
     * @param   string|array  Name(s) of the column(s)
     * @param   string  Type of the index (unique/normal/primary)
     * @return  bool
     */
    public function add_index($table_name, $index_name, $columns, $index_type = 'normal')
    {
        $this->log("Adding index '$index_name' to table '$table_name'...");
        $ret = $this->driver->add_index($table_name, $index_name, $columns, $index_type);
        $this->log("DONE<br />");
        
        return $ret;
    }

    /**
     * Remove an index
     *
     * @param   string  Name of the table
     * @param   string  Name of the index
     * @return  bool
     */
    public function remove_index($table_name, $index_name = NULL)
    {
        $this->log("Removing index '$index_name' from table '$table_name'...");
        $ret = $this->driver->remove_index($table_name, $index_name);
        $this->log("DONE<br />");
        
        return $ret;
    }

    /**
     * Add foreign key
     * 
     * @param string $from_table
     * @param string $to_table
     * @param string $to_column if NULL then is primary_key
     * @param string $from_column if NULL then is fk_#{$to_table}_#{$to_column}
     * @return bool
     */
    public function belongs_to($from_table, $to_table, $to_column = NULL, $from_column = NULL)
    {
        if (!$this->change_exists)
        {
            $ret = $this->driver->belongs_to($from_table, $to_table, $to_column, $from_column);
        }
        else
        {
            $constraint = 'fk_' . $to_table . '_' . $from_column;
            $ret = $this->driver->remove_index($from_table, $constraint);
        }
        
        return $ret;
    }

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
        $ret = $this->driver->has_one($from_table, $to_table, $from_column, $to_column);
        
        return $ret;
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
        $this->change_exception();
        $ret = $this->driver->has_one_trough($from_table, $to_table, $trough_table);
        
        return $ret;
        // remove columns $from_table and $trough_table
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
        $this->change_exception();
        $ret = $this->driver->has_many_trough($from_table, $to_table, $trough_table);
        
        return $ret;
        // remove columns $trough_table
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
        $this->change_exception();
        $ret = $this->driver->has_many($from_table, $to_table);
        
        return $ret;
        // drop table
    }

    /**
     * Execute custom query
     *
     * @param   string  SQL query to execute
     * @return  bool
     */
    public function run_query($query)
	{
        return $this->driver->run_query($query);
    }

    public function set_schema($schema)
    {
        return $this->driver->set_schema($schema);
    }
    
    public function commit()
    {
        $this->driver->commit();
    }

}
