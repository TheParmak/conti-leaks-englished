<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @package    Kohana/Codebench
 * @category   Tests
 * @author     Geert De Deckere <geert@idoe.be>
 */
class Bench_ExplodeLimit extends Codebench {

	public $description =
		'Having a look at the effect of adding a limit to the <a href="http://php.net/explode">explode</a> function.<br />
		 http://stackoverflow.com/questions/1308149/how-to-get-a-part-of-url-between-4th-and-5th-slashes';

	public $loops = 10000;

	public $subjects = array
	(
		'http://example.com/articles/123a/view',
		'http://example.com/articles/123a/view/x/x/x/x/x',
		'http://example.com/articles/123a/view/x/x/x/x/x/x/x/x/x/x/x/x/x/x/x/x/x/x',
	);

	public function bench_explode_without_limit($subject)
	{
		$parts = explode('/', $subject);
		return $parts[4];
	}

	public function bench_explode_with_limit($subject)
	{
		$parts = explode('/', $subject, 6);
		return $parts[4];
	}

}