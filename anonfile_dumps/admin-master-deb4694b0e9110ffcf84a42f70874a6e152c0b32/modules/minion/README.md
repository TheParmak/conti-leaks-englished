# Minion

Minion is a framework for running tasks via the CLI.

The system is inspired by ruckusing, which had a nice system for defining tasks but lacked the desired flexibility for kohana integration.

## Getting Started

First off, download and enable the module in your bootstrap

Then copy the bash script `minion` alongside your index.php (most likely the webroot).
If you'd rather the executable be in a different location to index.php then simply modify the bash script to point to index.php.

You can then run minion like so:

	./minion {task}

To view a list of minion tasks, run minion without any parameters, or with the `--help` option

	./minion
	./minion --help

To view help for a specific minion task run

	./minion {task} --help

For security reasons Minion will only run from the cli.  Attempting to access it over http will cause
a `Kohana_Exception` to be thrown.

If you're unable to use the binary file for whatever reason then simply replace `./minion {task}` in the above
examples with

	php index.php --uri=minion --task={task}

## Writing your own tasks

All minion tasks must be located in `classes/task/`.  They can be in any module, thus allowing you to
ship custom minion tasks with your own module / product.

Each task must extend the abstract class `Minion_Task` and implement `Minion_Task::_execute()`.

See `Minion_Task` for more details.

## Documentation

Code should be commented well enough not to need documentation, and minion can extract a class' doccomment to use
as documentation on the cli.

## Testing

This module is unittested using the [unittest module](http://github.com/kohana/unittest).
You can use the `minion` group to only run minion tests.

i.e.

	phpunit --group minion

Feel free to contribute tests(!), they can be found in the `tests/minion` directory. :)

## License

This is licensed under the [same license as Kohana](http://kohanaframework.org/license).
