<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Codebench — A benchmarking module.
 *
 * @package    Kohana/Codebench
 * @category   Base
 * @author     Kohana Team
 * @copyright  (c) 2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
abstract class Kohana_Codebench {

	/**
	 * @var  string  Some optional explanatory comments about the benchmark file.
	 *               HTML allowed. URLs will be converted to links automatically.
	 */
	public $description = '';

	/**
	 * @var  integer  How many times to execute each method per subject.
	 */
	public $loops = 1000;

	/**
	 * @var  array  The subjects to supply iteratively to your benchmark methods.
	 */
	public $subjects = array();

	/**
	 * @var  array  Grade letters with their maximum scores. Used to color the graphs.
	 */
	public $grades = array
	(
		125 => 'A',
		150 => 'B',
		200 => 'C',
		300 => 'D',
		500 => 'E',
		'default' => 'F',
	);

	/**
	 * Constructor.
	 *
	 * @return  void
	 */
	public function __construct()
	{
		// Set the maximum execution time
		set_time_limit(Kohana::$config->load('codebench')->max_execution_time);
	}

	/**
	 * Runs Codebench on the extending class.
	 *
	 * @return  array  benchmark output
	 */
	public function run()
	{
		// Array of all methods to loop over
		$methods = array_filter(get_class_methods($this), array($this, '_method_filter'));

		// Make sure the benchmark runs at least once,
		// also if no subject data has been provided.
		if (empty($this->subjects))
		{
			$this->subjects = array('NULL' => NULL);
		}

		// Initialize benchmark output
		$codebench = array
		(
			'class'       => get_class($this),
			'description' => $this->description,
			'loops'       => array
			(
				'base'    => (int) $this->loops,
				'total'   => (int) $this->loops * count($this->subjects) * count($methods),
			),
			'subjects'    => $this->subjects,
			'benchmarks'  => array(),
		);

		// Benchmark each method
		foreach ($methods as $method)
		{
			// Initialize benchmark output for this method
			$codebench['benchmarks'][$method] = array('time' => 0, 'memory' => 0);

			// Using Reflection because simply calling $this->$method($subject) in the loop below
			// results in buggy benchmark times correlating to the length of the method name.
			$reflection = new ReflectionMethod(get_class($this), $method);

			// Benchmark each subject on each method
			foreach ($this->subjects as $subject_key => $subject)
			{
				// Prerun each method/subject combo before the actual benchmark loop.
				// This way relatively expensive initial processes won't be benchmarked, e.g. autoloading.
				// At the same time we capture the return here so we don't have to do that in the loop anymore.
				$return = $reflection->invoke($this, $subject);

				// Start the timer for one subject
				$token = Profiler::start('codebench', $method.$subject_key);

				// The heavy work
				for ($i = 0; $i < $this->loops; ++$i)
				{
					$reflection->invoke($this, $subject);
				}

				// Stop and read the timer
				$benchmark = Profiler::total($token);

				// Benchmark output specific to the current method and subject
				$codebench['benchmarks'][$method]['subjects'][$subject_key] = array
				(
					'return' => $return,
					'time'   => $benchmark[0],
					'memory' => $benchmark[1],
				);

				// Update method totals
				$codebench['benchmarks'][$method]['time']   += $benchmark[0];
				$codebench['benchmarks'][$method]['memory'] += $benchmark[1];
			}
		}

		// Initialize the fastest and slowest benchmarks for both methods and subjects, time and memory,
		// these values will be overwritten using min() and max() later on.
		// The 999999999 values look like a hack, I know, but they work,
		// unless your method runs for more than 31 years or consumes over 1GB of memory.
		$fastest_method = $fastest_subject = array('time' => 999999999, 'memory' => 999999999); 
		$slowest_method = $slowest_subject = array('time' => 0, 'memory' => 0);

		// Find the fastest and slowest benchmarks, needed for the percentage calculations
		foreach ($methods as $method)
		{
			// Update the fastest and slowest method benchmarks
			$fastest_method['time']   = min($fastest_method['time'],   $codebench['benchmarks'][$method]['time']);
			$fastest_method['memory'] = min($fastest_method['memory'], $codebench['benchmarks'][$method]['memory']);
			$slowest_method['time']   = max($slowest_method['time'],   $codebench['benchmarks'][$method]['time']);
			$slowest_method['memory'] = max($slowest_method['memory'], $codebench['benchmarks'][$method]['memory']);

			foreach ($this->subjects as $subject_key => $subject)
			{
				// Update the fastest and slowest subject benchmarks
				$fastest_subject['time']   = min($fastest_subject['time'],   $codebench['benchmarks'][$method]['subjects'][$subject_key]['time']);
				$fastest_subject['memory'] = min($fastest_subject['memory'], $codebench['benchmarks'][$method]['subjects'][$subject_key]['memory']);
				$slowest_subject['time']   = max($slowest_subject['time'],   $codebench['benchmarks'][$method]['subjects'][$subject_key]['time']);
				$slowest_subject['memory'] = max($slowest_subject['memory'], $codebench['benchmarks'][$method]['subjects'][$subject_key]['memory']);
			}
		}

		// Percentage calculations for methods
		foreach ($codebench['benchmarks'] as & $method)
		{
			// Calculate percentage difference relative to fastest and slowest methods
			$method['percent']['fastest']['time']   = (empty($fastest_method['time']))   ? 0 : ($method['time']   / $fastest_method['time']   * 100);
			$method['percent']['fastest']['memory'] = (empty($fastest_method['memory'])) ? 0 : ($method['memory'] / $fastest_method['memory'] * 100);
			$method['percent']['slowest']['time']   = (empty($slowest_method['time']))   ? 0 : ($method['time']   / $slowest_method['time']   * 100);
			$method['percent']['slowest']['memory'] = (empty($slowest_method['memory'])) ? 0 : ($method['memory'] / $slowest_method['memory'] * 100);

			// Assign a grade for time and memory to each method
			$method['grade']['time']   = $this->_grade($method['percent']['fastest']['time']);
			$method['grade']['memory'] = $this->_grade($method['percent']['fastest']['memory']);

			// Percentage calculations for subjects
			foreach ($method['subjects'] as & $subject)
			{
				// Calculate percentage difference relative to fastest and slowest subjects for this method
				$subject['percent']['fastest']['time']   = (empty($fastest_subject['time']))   ? 0 : ($subject['time']   / $fastest_subject['time']   * 100);
				$subject['percent']['fastest']['memory'] = (empty($fastest_subject['memory'])) ? 0 : ($subject['memory'] / $fastest_subject['memory'] * 100);
				$subject['percent']['slowest']['time']   = (empty($slowest_subject['time']))   ? 0 : ($subject['time']   / $slowest_subject['time']   * 100);
				$subject['percent']['slowest']['memory'] = (empty($slowest_subject['memory'])) ? 0 : ($subject['memory'] / $slowest_subject['memory'] * 100);

				// Assign a grade letter for time and memory to each subject
				$subject['grade']['time']   = $this->_grade($subject['percent']['fastest']['time']);
				$subject['grade']['memory'] = $this->_grade($subject['percent']['fastest']['memory']);
			}
		}

		return $codebench;
	}

	/**
	 * Callback for array_filter().
	 * Filters out all methods not to benchmark.
	 *
	 * @param   string   method name
	 * @return  boolean
	 */
	protected function _method_filter($method)
	{
		// Only benchmark methods with the "bench" prefix
		return (substr($method, 0, 5) === 'bench');
	}

	/**
	 * Returns the applicable grade letter for a score.
	 *
	 * @param   integer|double  score
	 * @return  string  grade letter
	 */
	protected function _grade($score)
	{
		foreach ($this->grades as $max => $grade)
		{
			if ($max === 'default')
				continue;

			if ($score <= $max)
				return $grade;
		}

		return $this->grades['default'];
	}
}
