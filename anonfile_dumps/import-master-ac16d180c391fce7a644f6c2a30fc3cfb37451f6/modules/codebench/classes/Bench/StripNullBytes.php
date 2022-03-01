<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Kohana/Codebench
 * @category   Tests
 * @author     Geert De Deckere <geert@idoe.be>
 */
class Bench_StripNullBytes extends Codebench {

	public $description =
		'String replacement comparisons related to <a href="http://dev.kohanaphp.com/issues/2676">#2676</a>.';

	public $loops = 1000;

	public $subjects = array
	(
		"\0",
		"\0\0\0\0\0\0\0\0\0\0",
		"bla\0bla\0bla\0bla\0bla\0bla\0bla\0bla\0bla\0bla",
		"blablablablablablablablablablablablablablablabla",
	);

	public function bench_str_replace($subject)
	{
		return str_replace("\0", '', $subject);
	}

	public function bench_strtr($subject)
	{
		return strtr($subject, array("\0" => ''));
	}

	public function bench_preg_replace($subject)
	{
		return preg_replace('~\0+~', '', $subject);
	}

}