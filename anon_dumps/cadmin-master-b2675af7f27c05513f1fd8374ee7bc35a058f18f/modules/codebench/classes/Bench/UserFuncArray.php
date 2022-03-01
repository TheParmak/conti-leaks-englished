<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Kohana/Codebench
 * @category   Tests
 * @author     Woody Gilk <woody.gilk@kohanaphp.com>
 */
class Bench_UserFuncArray extends Codebench {

	public $description =
		'Testing the speed difference of using <code>call_user_func_array</code>
		 compared to counting args and doing manual calls.';

	public $loops = 100000;

	public $subjects = array
	(
		// Argument sets
		array(),
		array('one'),
		array('one', 'two'),
		array('one', 'two', 'three'),
	);

	public function bench_count_args($args)
	{
		$name = 'callme';
		switch (count($args))
		{
			case 1:
				$this->$name($args[0]);
			break;
			case 2:
				$this->$name($args[0], $args[1]);
			break;
			case 3:
				$this->$name($args[0], $args[1], $args[2]);
			break;
			case 4:
				$this->$name($args[0], $args[1], $args[2], $args[3]);
			break;
			default:
				call_user_func_array(array($this, $name), $args);
			break;
		}
	}

	public function bench_direct_call($args)
	{
		$name = 'callme';
		call_user_func_array(array($this, $name), $args);
	}

	protected function callme()
	{
		return count(func_get_args());
	}

}