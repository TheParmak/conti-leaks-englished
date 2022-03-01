# Writing Tasks

Writing a task in minion is very easy. Simply create a new class called `Task_<Taskname>` and put it inside `classes/task/<taskname>.php`.

	<?php defined('SYSPATH') or die('No direct script access.');

	class Task_Demo extends Minion_Task
	{
		protected $_options = array(
			'foo' => 'bar',
			'bar' => NULL,
		);

		/**
		 * This is a demo task
		 *
		 * @return null
		 */
		protected function _execute(array $params)
		{
			var_dump($params);
			echo 'foobar';
		}
	}

You'll notice a few things here:

 - You need a main `_execute()` method. It should take one array parameter.
   - This parameter contains any command line options passed to the task.
   - For example, if you call the task above with `./minion --task=demo --foo=foobar` then `$params` will contain: `array('foo' => 'foobar', 'bar' => NULL)`
 - It needs to have a `protected $_options` array. This is a list of parameters you want to accept for this task. Any parameters passed to the task not in this list will be rejected.

## Namespacing Tasks

You can "namespace" tasks by placing them all in a subdirectory: `classes/task/database/generate.php`. This task will be named `database:generate` and can be called with this task name.

# Parameter Validations

To add validations to your command line options, simply overload the `build_validation()` method in your task:

	public function build_validation(Validation $validation)
	{
		return parent::build_validation($validation)
			->rule('foo', 'not_empty') // Require this param
			->rule('bar', 'numeric'); // This param should be numeric
	}

These validations will run for every task call unless `--help` is passed to the task.

# Task Help

Tasks can have built-in help. Minion will read class docblocks that you specify:

	<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * This is a demo task.
	 * 
	 * It can accept the following options:
	 *  - foo: this parameter does something. It is required.
	 *  - bar: this parameter does something else. It should be numeric.
	 *
	 * @package    Kohana
	 * @category   Helpers
	 * @author     Kohana Team
	 * @copyright  (c) 2009-2011 Kohana Team
	 * @license    http://kohanaframework.org/license
	 */
	class Minion_Task_Demo extends Minion_Task

The `@` tags in the class comments will also be displayed in a human readable format. When writing your task comments, you should specify how to use it, and any parameters it accepts.
