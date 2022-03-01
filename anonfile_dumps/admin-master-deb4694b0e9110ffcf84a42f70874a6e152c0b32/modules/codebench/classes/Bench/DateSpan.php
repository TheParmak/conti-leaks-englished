<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Kohana/Codebench
 * @category   Tests
 * @author     Woody Gilk <woody.gilk@kohanaphp.com>
 */
class Bench_DateSpan extends Codebench {

	public $description =
		'Optimization for <code>Date::span()</code>.';

	public $loops = 1000;

	public $subjects = array();

	public function __construct()
	{
		parent::__construct();

		$this->subjects = array(
			time(),
			time() - Date::MONTH,
			time() - Date::YEAR,
			time() - Date::YEAR * 10,
		);
	}

	// Original method
	public static function bench_span_original($remote, $local = NULL, $output = 'years,months,weeks,days,hours,minutes,seconds')
	{
		// Array with the output formats
		$output = preg_split('/[^a-z]+/', strtolower( (string) $output));

		// Invalid output
		if (empty($output))
			return FALSE;

		// Make the output values into keys
		extract(array_flip($output), EXTR_SKIP);

		if ($local === NULL)
		{
			// Calculate the span from the current time
			$local = time();
		}

		// Calculate timespan (seconds)
		$timespan = abs($remote - $local);

		if (isset($years))
		{
			$timespan -= Date::YEAR * ($years = (int) floor($timespan / Date::YEAR));
		}

		if (isset($months))
		{
			$timespan -= Date::MONTH * ($months = (int) floor($timespan / Date::MONTH));
		}

		if (isset($weeks))
		{
			$timespan -= Date::WEEK * ($weeks = (int) floor($timespan / Date::WEEK));
		}

		if (isset($days))
		{
			$timespan -= Date::DAY * ($days = (int) floor($timespan / Date::DAY));
		}

		if (isset($hours))
		{
			$timespan -= Date::HOUR * ($hours = (int) floor($timespan / Date::HOUR));
		}

		if (isset($minutes))
		{
			$timespan -= Date::MINUTE * ($minutes = (int) floor($timespan / Date::MINUTE));
		}

		// Seconds ago, 1
		if (isset($seconds))
		{
			$seconds = $timespan;
		}

		// Remove the variables that cannot be accessed
		unset($timespan, $remote, $local);

		// Deny access to these variables
		$deny = array_flip(array('deny', 'key', 'difference', 'output'));

		// Return the difference
		$difference = array();
		foreach ($output as $key)
		{
			if (isset($$key) AND ! isset($deny[$key]))
			{
				// Add requested key to the output
				$difference[$key] = $$key;
			}
		}

		// Invalid output formats string
		if (empty($difference))
			return FALSE;

		// If only one output format was asked, don't put it in an array
		if (count($difference) === 1)
			return current($difference);

		// Return array
		return $difference;
	}

	// Using an array for the output
	public static function bench_span_use_array($remote, $local = NULL, $output = 'years,months,weeks,days,hours,minutes,seconds')
	{
		// Array with the output formats
		$output = preg_split('/[^a-z]+/', strtolower( (string) $output));

		// Invalid output
		if (empty($output))
			return FALSE;

		// Convert the list of outputs to an associative array
		$output = array_combine($output, array_fill(0, count($output), 0));

		// Make the output values into keys
		extract(array_flip($output), EXTR_SKIP);

		if ($local === NULL)
		{
			// Calculate the span from the current time
			$local = time();
		}

		// Calculate timespan (seconds)
		$timespan = abs($remote - $local);

		if (isset($output['years']))
		{
			$timespan -= Date::YEAR * ($output['years'] = (int) floor($timespan / Date::YEAR));
		}

		if (isset($output['months']))
		{
			$timespan -= Date::MONTH * ($output['months'] = (int) floor($timespan / Date::MONTH));
		}

		if (isset($output['weeks']))
		{
			$timespan -= Date::WEEK * ($output['weeks'] = (int) floor($timespan / Date::WEEK));
		}

		if (isset($output['days']))
		{
			$timespan -= Date::DAY * ($output['days'] = (int) floor($timespan / Date::DAY));
		}

		if (isset($output['hours']))
		{
			$timespan -= Date::HOUR * ($output['hours'] = (int) floor($timespan / Date::HOUR));
		}

		if (isset($output['minutes']))
		{
			$timespan -= Date::MINUTE * ($output['minutes'] = (int) floor($timespan / Date::MINUTE));
		}

		// Seconds ago, 1
		if (isset($output['seconds']))
		{
			$output['seconds'] = $timespan;
		}

		if (count($output) === 1)
		{
			// Only a single output was requested, return it
			return array_pop($output);
		}

		// Return array
		return $output;
	}

}