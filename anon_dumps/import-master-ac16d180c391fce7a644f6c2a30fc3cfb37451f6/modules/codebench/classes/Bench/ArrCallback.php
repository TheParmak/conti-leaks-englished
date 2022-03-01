<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Kohana/Codebench
 * @category   Tests
 * @author     Geert De Deckere <geert@idoe.be>
 */
class Bench_ArrCallback extends Codebench {

	public $description =
		'Parsing <em>command[param,param]</em> strings in <code>Arr::callback()</code>:
		 http://github.com/shadowhand/kohana/commit/c3aaae849164bf92a486e29e736a265b350cb4da#L0R127';

	public $loops = 10000;

	public $subjects = array
	(
		// Valid callback strings
		'foo',
		'foo::bar',
		'foo[apple,orange]',
		'foo::bar[apple,orange]',
		'[apple,orange]', // no command, only params
		'foo[[apple],[orange]]', // params with brackets inside

		// Invalid callback strings
		'foo[apple,orange', // no closing bracket
	);

	public function bench_shadowhand($subject)
	{
		// The original regex we're trying to optimize
		if (preg_match('/([^\[]*+)\[(.*)\]/', $subject, $match))
			return $match;
	}

	public function bench_geert_regex_1($subject)
	{
		// Added ^ and $ around the whole pattern
		if (preg_match('/^([^\[]*+)\[(.*)\]$/', $subject, $matches))
			return $matches;
	}

	public function bench_geert_regex_2($subject)
	{
		// A rather experimental approach using \K which requires PCRE 7.2 ~ PHP 5.2.4
		// Note: $matches[0] = params, $matches[1] = command
		if (preg_match('/^([^\[]*+)\[\K.*(?=\]$)/', $subject, $matches))
			return $matches;
	}

	public function bench_geert_str($subject)
	{
		// A native string function approach which beats all the regexes
		if (strpos($subject, '[') !== FALSE AND substr($subject, -1) === ']')
			return explode('[', substr($subject, 0, -1), 2);
	}
}