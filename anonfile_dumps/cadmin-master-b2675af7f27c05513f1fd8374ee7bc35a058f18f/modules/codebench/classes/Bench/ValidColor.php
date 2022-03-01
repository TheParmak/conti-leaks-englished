<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Kohana/Codebench
 * @category   Tests
 * @author     Geert De Deckere <geert@idoe.be>
 */
class Bench_ValidColor extends Codebench {

	public $description =
		'Optimization for <code>Validate::color()</code>.
		 See: http://forum.kohanaphp.com/comments.php?DiscussionID=2192.

		 Note that the methods with an <em>_invalid</em> suffix contain flawed regexes and should be
		 completely discarded. I left them in here for educational purposes, and to remind myself
		 to think harder and test more thoroughly. It can\'t be that I only found out so late in
		 the game. For the regex explanation have a look at the forum topic mentioned earlier.';

	public $loops = 10000;

	public $subjects = array
	(
		// Valid colors
		'aaA',
		'123',
		'000000',
		'#123456',
		'#abcdef',

		// Invalid colors
		'ggg',
		'1234',
		'#1234567',
		"#000\n",
		'}§è!çà%$z',
	);

	// Note that I added the D modifier to corey's regexes. We need to match exactly
	// the same if we want the benchmarks to be of any value.
	public function bench_corey_regex_1_invalid($subject)
	{
		return (bool) preg_match('/^#?([0-9a-f]{1,2}){3}$/iD', $subject);
	}

	public function bench_corey_regex_2($subject)
	{
		return (bool) preg_match('/^#?([0-9a-f]){3}(([0-9a-f]){3})?$/iD', $subject);
	}

	// Optimized corey_regex_1
	// Using non-capturing parentheses and a possessive interval
	public function bench_geert_regex_1a_invalid($subject)
	{
		return (bool) preg_match('/^#?(?:[0-9a-f]{1,2}+){3}$/iD', $subject);
	}

	// Optimized corey_regex_2
	// Removed useless parentheses, made the remaining ones non-capturing
	public function bench_geert_regex_2a($subject)
	{
		return (bool) preg_match('/^#?[0-9a-f]{3}(?:[0-9a-f]{3})?$/iD', $subject);
	}

	// Optimized geert_regex_1a
	// Possessive "#"
	public function bench_geert_regex_1b_invalid($subject)
	{
		return (bool) preg_match('/^#?+(?:[0-9a-f]{1,2}+){3}$/iD', $subject);
	}

	// Optimized geert_regex_2a
	// Possessive "#"
	public function bench_geert_regex_2b($subject)
	{
		return (bool) preg_match('/^#?+[0-9a-f]{3}(?:[0-9a-f]{3})?$/iD', $subject);
	}

	// Using \z instead of $
	public function bench_salathe_regex_1($subject)
	{
		return (bool) preg_match('/^#?+[0-9a-f]{3}(?:[0-9a-f]{3})?\z/i', $subject);
	}

	// Using \A instead of ^
	public function bench_salathe_regex_2($subject)
	{
		return (bool) preg_match('/\A#?+[0-9a-f]{3}(?:[0-9a-f]{3})?\z/i', $subject);
	}

	// A solution without regex
	public function bench_geert_str($subject)
	{
		if ($subject[0] === '#')
		{
			$subject = substr($subject, 1);
		}

		$strlen = strlen($subject);
		return (($strlen === 3 OR $strlen === 6) AND ctype_xdigit($subject));
	}

	// An ugly, but fast, solution without regex
	public function bench_salathe_str($subject)
	{
		if ($subject[0] === '#')
		{
			$subject = substr($subject, 1);
		}

		// TRUE if:
		// 1. $subject is 6 or 3 chars long
		// 2. $subject contains only hexadecimal digits
		return (((isset($subject[5]) AND ! isset($subject[6])) OR
			(isset($subject[2]) AND ! isset($subject[3])))
			AND ctype_xdigit($subject));
	}
}