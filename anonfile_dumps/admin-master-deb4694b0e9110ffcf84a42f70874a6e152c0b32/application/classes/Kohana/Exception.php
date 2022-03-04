<?php defined('SYSPATH') OR die('No direct script access.');

class Kohana_Exception extends Kohana_Kohana_Exception
{

    /**
     * Exception handler, logs the exception and generates a Response object
     * for display.
     *
     * @uses    Kohana_Exception::response
     * @param   Exception  $e
     * @return  Response
     */
    public static function _handler(Exception $e)
    {
        try
        {
            // Log the exception
            Kohana_Exception::log($e);

            // Generate the response
            $response = Kohana_Exception::response($e);

            // Log the full trace
            if (Kohana::DEVELOPMENT === Kohana::$environment)
            {
                $full_trace = $response;
            }
            else
            {
                $original_environment = Kohana::$environment;
                Kohana::$environment = Kohana::DEVELOPMENT;
                $full_trace = Kohana_Exception::response($e);
                Kohana::$environment = $original_environment;
            }
            Kohana_Exception::_write_trace($full_trace);

            return $response;
        }
        catch (Exception $e)
        {
            /**
             * Things are going *really* badly for us, We now have no choice
             * but to bail. Hard.
             */
            // Clean the output buffer if one exists
            ob_get_level() AND ob_clean();

            // Set the Status code to 500, and Content-Type to text/plain.
            header('Content-Type: text/plain; charset='.Kohana::$charset, TRUE, 500);

            if (Kohana::PRODUCTION == Kohana::$environment)
            {
                isset(Kohana::$log) && Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e), null, array('exception' => $e));

                echo '<h1>Internal server error</h1>';
            }
            else
            {
                echo Kohana_Exception::text($e);
            }

            exit(1);
        }
    }

    public static function _write_trace($trace_html)
    {
        $directory = APPPATH . 'logs/trace/';

        if(is_dir($directory) && is_writable($directory))
        {
            // Set the yearly directory name
            $directory .= date('Y');

            if ( ! is_dir($directory))
            {
                // Create the yearly directory
                mkdir($directory, 02777);

                // Set permissions (must be manually set to fix umask issues)
                chmod($directory, 02777);
            }

            // Add the month to the directory
            $directory .= DIRECTORY_SEPARATOR . date('m');

            if ( ! is_dir($directory))
            {
                // Create the monthly directory
                mkdir($directory, 02777);

                // Set permissions (must be manually set to fix umask issues)
                chmod($directory, 02777);
            }

            $microtime = explode(' ', microtime());
            $filename = $directory . '/' . date('d.H.i.s', $microtime[1]) . '.' . sprintf("%-'06s", ($microtime[0] * 1000000)) . '.html';

            if ( ! file_exists($filename))
            {
                // Create the log file
                file_put_contents($filename, $trace_html);

                // Allow anyone to write to log files
                chmod($filename, 0666);
            }
            else
            {
                $filename = $filename . '_duplicate(' . uniqid('', true) . ').html';

                // Create the log file
                file_put_contents($filename, $trace_html);

                // Allow anyone to write to log files
                chmod($filename, 0666);
            }
        }
        else
        {
            Kohana::$log->add(Log::ERROR, "Directory '$directory' must be writable!");
            Kohana::$log->write();
        }
    }
}
