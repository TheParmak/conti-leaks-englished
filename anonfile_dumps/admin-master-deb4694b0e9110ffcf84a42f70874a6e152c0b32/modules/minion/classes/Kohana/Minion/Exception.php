<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Minipn exception
 *
 * @package    Kohana
 * @category   Minion
 * @author     Kohana Team
 * @copyright  (c) 2009-2011 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Kohana_Minion_Exception extends Kohana_Exception {
	/**
	 * Inline exception handler, displays the error message, source of the
	 * exception, and the stack trace of the error.
	 *
	 * Should this display a stack trace? It's useful.
	 *
	 * Should this still log? Maybe not as useful since we'll see the error on the screen.
	 *
	 * @uses    Kohana_Exception::text
	 * @param   Exception   $e
	 * @return  boolean
	 */
	public static function handler(Exception $e)
	{
		try
		{
			if ($e instanceof Minion_Exception)
			{
				echo $e->format_for_cli();
			}
			else
			{
				echo Kohana_Exception::text($e);
			}

			$exit_code = $e->getCode();

			// Never exit "0" after an exception.
			if ($exit_code == 0)
			{
				$exit_code = 1;
			}

			exit($exit_code);
		}
		catch (Exception $e)
		{
			// Clean the output buffer if one exists
			ob_get_level() and ob_clean();

			// Display the exception text
			echo Kohana_Exception::text($e), "\n";

			// Exit with an error status
			exit(1);
		}
	}

	public function format_for_cli()
	{
		return Kohana_Exception::text($this);
	}
}
