<?php defined('SYSPATH') or die('No direct script access.');

class Task_Db_Rollback extends Minion_Task
{

    protected $_options = array(
        'db' => 'default',
        'step' => 1,
    );

    /**
     * Task to rollback last executed migration
     *
     * @return null
     */
    protected function _execute(array $params)
    {
        $migrations = new MigrationManager();
        Database::$default = $params['db'];
        
        if ( ! ORM::factory('Migration')->is_installed() )
        {
            Minion_CLI::write('Migrations is not installed. Please Run the migrations.sql script in your mysql server');
            exit();
        }

        $migrations->rollback($params['db'], $params['step']);
    }

}