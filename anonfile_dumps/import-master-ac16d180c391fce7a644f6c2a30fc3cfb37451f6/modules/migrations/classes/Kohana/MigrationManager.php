<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Migrations
 *
 * An open source utility inspired by Ruby on Rails
 *
 * Reworked for Kohana by Fernando Petrelli
 *
 * Based on Migrations module by Jamie Madill
 *
 * @package     Migrations
 * @author      Vladimir Zyablitskiy
 * @author      Matías Montes
 * @author      Jamie Madill
 * @author      Fernando Petrelli
 */
class Kohana_MigrationManager
{

    protected $_config;

    public function __construct()
    {
        $this->_config = Kohana::$config->load('migrations')->as_array();
    }

    public function get_config()
    {
        return $this->_config;
    }

    /**
     * Run all pending migrations
     *
     */
    public function migrate($group = 'default', $step = 'all')
    {
        Database::$default = $group;
        $migration_keys = $this->get_migration_keys();
        $migrations = ORM::factory('Migration')->find_all();
        $messages = array();

        //Remove executed migrations from queue
        foreach ($migrations as $migration)
        {
            if (array_key_exists($migration->hash, $migration_keys))
            {
                unset($migration_keys[$migration->hash]);
            }
        }

        if (count($migration_keys) > 0)
        {
            if (strtolower($step) !== 'all')
                $migration_keys = array_slice($migration_keys, 0, (int) $step);
            $common_time = 0;
            foreach ($migration_keys as $key => $value)
            {
                $msg = "Executing migration: '" . $value . "' with hash: " . $key;
                $interval = microtime(true);

                try
                {
                    $migration_object = $this->load_migration($key, $group);
                    $migration_object->up();
                    $model = ORM::factory('Migration');
                    $model->hash = $key;
                    $model->name = $value;
                    $model->save();
                    $interval = microtime(true) - $interval;
                    $common_time += $interval;
                    $model ? $messages[] = array(0 => $msg) : $messages[] = array(1 => $msg);
                    array_push($messages, array(Minion_CLI::color("----------- with $interval s -----------\n", 'green')));
                }
                catch (Database_Exception $e)
                {
                    $messages[] = array(1 => $msg . "\n" . $e->getMessage());
                }
            }
            array_push($messages, array(Minion_CLI::color("\n----------- COMMON TIME IS $common_time s -----------", 'green')));
        }
        return $messages;
    }

    /**
     * Rollback last executed migration.
     *
     */
    public function rollback($group = 'default', $step = 1)
    {
        Database::$default = $group;
        //Get last executed migration
        if (strtolower($step) === 'all')
            $model = ORM::factory('Migration')->order_by('hash', 'DESC')->find_all();
        else
            $model = ORM::factory('Migration')->order_by('hash', 'DESC')->limit((int) $step)->find_all();
        $messages = array();

        $common_time = 0;
        foreach ($model as $key => $item)
        {
            if ($item->loaded())
            {
                $interval = microtime(true);
                try
                {
                    $migration_object = $this->load_migration($item->hash, $group);
                    $migration_object->down();

                    if ($item)
                    {
                        $interval = microtime(true) - $interval;
                        $common_time += $interval;
                        $msg = "Migration '" . $item->name . "' with hash: " . $item->hash . ' was succefully "rollbacked"';
                        $messages[] = array(0 => $msg);
                        array_push($messages, array(Minion_CLI::color("----------- with $interval s -----------\n", 'green')));
                    }
                    else
                    {
                        $messages[] = array(1 => "Error executing rollback");
                    }
                    $item->delete();
                }
                catch (Exception $e)
                {
                    $messages[] = array(1 => $e->getMessage());
                }
            }
            else
            {
                $messages[] = array(1 => "There's no migration to rollback");
            }
        }
        array_push($messages, array(Minion_CLI::color("\n----------- COMMON TIME IS $common_time s -----------", 'green')));

        return $messages;
    }

    /**
     * Rollback last executed migration.
     *
     */
    public function get_timestamp()
    {
        return date('YmdHis');
    }

    /**
     * Get all valid migrations file names
     *
     * @return array migrations_filenames
     */
    public function get_migrations()
    {
        $migrations = glob($this->_config['path'] . '*' . EXT);
        foreach ($migrations as $i => $file)
        {
            $name = basename($file, EXT);
            if (!preg_match('/^\d{8}\d{6}_(\w+)$/', $name)) //Check filename format
                unset($migrations[$i]);
        }
        sort($migrations);
        
        return $migrations;
    }

    /**
     * Generates a new migration file
     * TODO: Probably needs to be in outer class
     *
     * @return integer completion_code
     */
    public function generate_migration($migration_name)
    {
        try
        {
            //Creates the migration file with the timestamp and the name from params
            $file_name = $this->get_timestamp() . '_' . $migration_name . EXT;
            $config = $this->get_config();
            $file = fopen($config['path'] . $file_name, 'w+');

            $params = $this->migration_name_parse($migration_name);
            if ( ! $params )
            {
                $params = array(
                    array(
                        'method' => 'migration'
                    ),
                    array(
                        'table_name' => 'table_name',
                        'column_name' => 'column_name'
                    )
                );
            }
            $params[1]['migration_name'] = $migration_name;
            //Opens the template file and replaces the name
            if ( ! file_exists(__DIR__ . '/../../views/' . $params[0]['method'] . '_template.php') )
            {
                $params[0]['method'] = 'migration';
            }
            $view = new View($params[0]['method'] . '_template');
            $view->set_global('params', $params[1]);
            fwrite($file, $view);
            fclose($file);
            chmod($config['path'] . $file_name, 0770);
            
            return 0;
        }
        catch (Exception $e)
        {
            return 1;
        }
    }
    
    protected function migration_name_parse($migration_name)
    {
        $methods = array(
            array(
                'name' => 'create_table',
                'alt_name' => 'createtable',
                'delimeter' => '',
                'table_name' => true
            ),
            array(
                'name' => 'rename_table',
                'alt_name' => 'renametable',
                'delimeter' => '',
                'table_name' => true
            ),
            array(
                'name' => 'drop_table',
                'alt_name' => 'droptable',
                'delimeter' => '',
                'table_name' => true
            ),
            array(
                'name' => 'add_index',
                'alt_name' => 'addindex',
                'delimeter' => 'To',
                'table_name' => true
            ),
            array(
                'name' => 'remove_index',
                'alt_name' => 'remove_index',
                'delimeter' => '',
                'table_name' => false
            ),
            array(
                'name' => 'add_fk',
                'alt_name' => 'addfk',
                'delimeter' => 'To',
                'table_name' => true
            ),
            array(
                'name' => 'add',
                'alt_name' => 'addcolumn',
                'delimeter' => 'To',
                'table_name' => true
            ),
            array(
                'name' => 'rename_column',
                'alt_name' => 'renamecolumn',
                'delimeter' => '',
                'table_name' => false
            ),
            array(
                'name' => 'change_column',
                'alt_name' => 'changecolumn',
                'delimeter' => '',
                'table_name' => false
            ),
            array(
                'name' => 'remove_column',
                'alt_name' => 'removecolumn',
                'delimeter' => '',
                'table_name' => false
            )
        );
        
        $result = array();
        
        foreach ($methods as $key => $method)
        {
            $match = strpos(strtolower($migration_name), $method['name']);
            $offset = strlen($method['name']);
            
            if($match === false)
            {
                $match = strpos(strtolower($migration_name), $method['alt_name']);
                $offset = strlen($method['alt_name']);
            }
            if($match !== false)
            {
                $result[] = array('method' => $method['name']);
                if (!$method['table_name'])
                {
                    $result[] = array('table_name' => 'table_name');
                }
                else
                {
                    if(!empty($method['delimeter']))
                        $match = strpos($migration_name, $method['delimeter']);
                    else
                        $match = $offset;
                    if($match) {
                        $match += strlen($method['delimeter']);
                        $table = substr($migration_name, $match);
                        $result[] = array('table_name' => $table);
                        if($method['name'] === 'add') {
                            $result[1]['column_name'] = substr($migration_name, $offset, $match-strlen($method['delimeter'])-$offset);
                            $result[0]['method'] = 'add_column';
                        }
                    }
                }
                
                return $result;
            }
        }
        
        return false;
    }

    /**
     * Get all migration keys (timestamps)
     *
     * @return array migrations_keys
     */
    protected function get_migration_keys()
    {
        $migrations = $this->get_migrations();
        $keys = array();
        foreach ($migrations as $migration)
        {
            $sub_migration = substr(basename($migration, EXT), 0, strlen('YYYYmmddHHiiss'));
            $keys = Arr::merge($keys, array($sub_migration => substr(basename($migration, EXT), strlen('YYYYmmddHHiiss_'))));
        }
        
        return $keys;
    }

    /**
     * Load the migration file, and returns a Migration object
     *
     * @return Migration object with up and down functions
     */
    protected function load_migration($version, $group = 'default')
    {
        $f = glob($this->_config['path'] . $version . '*' . EXT);

        if (count($f) > 1) // Only one migration per step is permitted
            throw new Kohana_Exception('There are repeated migration names');

        if (count($f) == 0) // Migration step not found
            throw new Kohana_Exception("There's no migration to rollback");

        $file = basename($f[0]);
        $name = basename($f[0], EXT);

        // Filename validation
        if (!preg_match('/^\d{8}\d{6}_(\w+)$/', $name, $match))
            throw new Kohana_Exception('Invalid filename :file', array(':file' => $file));

        $match[1] = strtolower($match[1]);
        require $f[0]; //Includes migration class file

        $class = ucfirst($match[1]); //Get the class name capitalized

        if (!class_exists($class))
            throw new Kohana_Exception('Class :class doesn\'t exists', array(':class' => $class));

        if ( ! $this->method_defined($class, 'up') OR ! $this->method_defined($class, 'down') )
            throw new Kohana_Exception('Up/down functions missing on class :class', array(':class' => $class));

        return new $class(true, $group);
    }

    protected function method_defined($object, $method_name)
    {
        $ReflectionClass = new ReflectionClass($object);
        
        return method_exists($object, $method_name) && (strtolower($ReflectionClass->getMethod($method_name)->class) ===
                strtolower((is_object($object) ? get_class($object) : $object)));
    }

}