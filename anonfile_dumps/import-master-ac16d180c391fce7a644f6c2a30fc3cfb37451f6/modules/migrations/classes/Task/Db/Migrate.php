<?php defined('SYSPATH') or die('No direct script access.');
 
class Task_Db_Migrate extends Minion_Task
{

    protected $_options = array(
        'db' => 'default',
        'step' => 'all',
    );

    /**
     * Task to run pending migrations
     *
     * @return null
     */
    protected function _execute(array $params)
    {
        $migrations = new MigrationManager();
        Database::$default = $params['db'];
        $this->db = Database::instance();
        $db_config = Kohana::$config->load('database')->{$params['db']};

        if ( ! ORM::factory('Migration')->is_installed() )
        {
            /**
             * Get platform from database config
             */
            $platform = strtolower($db_config['type']);
            if ( 'mysqli' == $platform )
            {
                $platform = 'mysql';
            }

            /**
             * Get SQL from file for selected platform
             */
            $file = realpath(substr(__DIR__, 0, strlen(__DIR__) - strlen('classes/Task/Db')) . 'sql/' . $platform . '.sql');
            $handle = fopen($file, 'rb');
            $sql_create = fread($handle, filesize($file));

            $this->db->query(0, $sql_create);
            $msg = Minion_CLI::color("-----------------------------\n", 'green');
            $msg .= Minion_CLI::color("| Migration table create!!! |\n", 'green');
            $msg .= Minion_CLI::color("-----------------------------\n", 'green');
            Minion_CLI::write($msg);
        }

        $messages = $migrations->migrate($params['db'], $params['step']);

        if (empty($messages))
        {
            Minion_CLI::write("Nothing to migrate");
        }
        else
        {
            foreach ($messages as $message)
            {
                if (key($message) == 0)
                {
                    Minion_CLI::write($message[0]);
                    Minion_CLI::write("OK");
                }
                else
                { 
                    Minion_CLI::write($message[key($message)]);
                    Minion_CLI::write("ERROR");
                }
            }
        }
    }

}