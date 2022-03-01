<?php defined('SYSPATH') or die('No direct script access.');

class Task_Helper
{

    public static function getAdminClient()
    {
        return Task_Helper::getInitClient(
            'init.ip.admin'
        );
    }
    
    public static function getInitClient($config_name)
    {
        return Task_Helper::getClient(
            Kohana::$config->load($config_name)
        );
    }
    
    public static function getClient($host = null)
    {
        if ( null === $host )
        {
            $host = Kohana::$config->load('init.ip.admin');
        }

        $client = new GearmanClient();
        $client->addServer($host, 4730);
        $client->setExceptionCallback(function(GearmanTask $task)
        {
            return GEARMAN_WORK_EXCEPTION;
        });
        
        return $client;
    }

    public static function prepare(){
        // This is default value in php5-cli/php.ini, but to be sure...
        ini_set('max_execution_time', 0);
        /**
         * Disable "memory leak" in workers
         * If you still need them, when enable in your Worker function like this:
         *
         *     Kohana::$profiling = true;
         *
         */
        Kohana::$profiling = false;
        Log::$write_on_add = true;
    }
    
    public static function createWorker($name, Minion_Task &$handler, $function = 'Worker', $count = 500)
    {
        self::prepare();
        /**
         * Set up SIGTERM and SIGINT signal handlers
         */
        $terminate = false;
        pcntl_signal(SIGINT, function() use (&$terminate)
        {
            //Minion_CLI::write('Received SIGINT');
            $terminate = true;
        });
        pcntl_signal(SIGTERM, function() use (&$terminate)
        {
            //Minion_CLI::write('Received SIGTERM');
            $terminate = true;
        });
        
        /**
         * Connect to local gearman-job-server and setup function
         */
        $worker = new GearmanWorker();
        $worker->addOptions(GEARMAN_WORKER_NON_BLOCKING);
        $worker->setTimeout(1000);
        if ( ! $worker->addServer(Kohana::$config->load('init.ip.admin'), 4730) )
        {
            throw new GearmanException($worker->error());
        }

        $worker->addFunction($name, function(GearmanJob $job) use(&$handler, $function)
        {
            try
            {
                return $handler->$function($job);
            }
            catch(Exception $e)
            {
                Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e), null, array('exception' => $e));
                throw $e;
            }
        });
        
        /**
         * Worker loop for non-blocking mode
         */
        $count += rand(0, 100);
        for($i = 0; $i < $count; $i++)
        {
            if ( $terminate )
            {
                //Minion_CLI::write('Exiting worker loop');
                break;
            }
            else
            {
                pcntl_signal_dispatch();
            }

            $worker->work();

            if ( $terminate )
            {
                //Minion_CLI::write('Exiting worker loop');
                break;
            }
            else
            {
                pcntl_signal_dispatch();
            }
            
            if ( GEARMAN_SUCCESS == $worker->returnCode() )
            {
                continue;
            }
            
            if ( GEARMAN_IO_WAIT != $worker->returnCode() && GEARMAN_NO_JOBS != $worker->returnCode() )
            {
                //Minion_CLI::write('Error [ ' . $worker->returnCode() . ' ]: ' . $worker->error());
                $e = new Kohana_Exception($worker->error(), null, $worker->returnCode());
                Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e), null, array('exception' => $e));
                break;
            }
            
            $worker->wait(); $i--;
        }
        
        $worker->unregisterAll();
    }
    
}