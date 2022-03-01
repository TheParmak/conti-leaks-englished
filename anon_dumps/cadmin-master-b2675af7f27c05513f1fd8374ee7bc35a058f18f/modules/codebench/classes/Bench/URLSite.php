<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Kohana/Codebench
 * @category   Tests
 * @author     Geert De Deckere <geert@idoe.be>
 */
class Bench_URLSite extends Codebench {

	public $description = 'http://dev.kohanaframework.org/issues/3110';

	public $loops = 1000;

	public $subjects = array
	(
		'',
		'news',
		'news/',
		'/news/',
		'news/page/5',
		'news/page:5',
		'http://example.com/',
		'http://example.com/hello',
		'http://example.com:80/',
		'http://user:pass@example.com/',
	);

	public function __construct()
	{
		foreach ($this->subjects as $subject)
		{
			// Automatically create URIs with query string and/or fragment part appended
			$this->subjects[] = $subject.'?query=string';
			$this->subjects[] = $subject.'#fragment';
			$this->subjects[] = $subject.'?query=string#fragment';
		}

		parent::__construct();
	}

	public function bench_original($uri)
	{
		// Get the path from the URI
		$path = trim(parse_url($uri, PHP_URL_PATH), '/');

		if ($query = parse_url($uri, PHP_URL_QUERY))
		{
			$query = '?'.$query;
		}

		if ($fragment = parse_url($uri, PHP_URL_FRAGMENT))
		{
			$fragment = '#'.$fragment;
		}

		return $path.$query.$fragment;
	}

	public function bench_explode($uri)
	{
		// Chop off possible scheme, host, port, user and pass parts
		$path = preg_replace('~^[-a-z0-9+.]++://[^/]++/?~', '', trim($uri, '/'));

		$fragment = '';
		$explode = explode('#', $path, 2);
		if (isset($explode[1]))
		{
			$path = $explode[0];
			$fragment = '#'.$explode[1];
		}

		$query = '';
		$explode = explode('?', $path, 2);
		if (isset($explode[1]))
		{
			$path = $explode[0];
			$query = '?'.$explode[1];
		}

		return $path.$query.$fragment;
	}

	public function bench_regex($uri)
	{
		preg_match('~^(?:[-a-z0-9+.]++://[^/]++/?)?([^?#]++)?(\?[^#]*+)?(#.*)?~', trim($uri, '/'), $matches);
		$path = Arr::get($matches, 1, '');
		$query = Arr::get($matches, 2, '');
		$fragment = Arr::get($matches, 3, '');

		return $path.$query.$fragment;
	}

	public function bench_regex_without_arrget($uri)
	{
		preg_match('~^(?:[-a-z0-9+.]++://[^/]++/?)?([^?#]++)?(\?[^#]*+)?(#.*)?~', trim($uri, '/'), $matches);
		$path = isset($matches[1]) ? $matches[1] : '';
		$query = isset($matches[2]) ? $matches[2] : '';
		$fragment = isset($matches[3]) ? $matches[3] : '';

		return $path.$query.$fragment;
	}

	// And then I thought, why do all the work of extracting the query and fragment parts and then reappending them?
	// Just leaving them alone should be fine, right? As a bonus we get a very nice speed boost.
	public function bench_less_is_more($uri)
	{
		// Chop off possible scheme, host, port, user and pass parts
		$path = preg_replace('~^[-a-z0-9+.]++://[^/]++/?~', '', trim($uri, '/'));

		return $path;
	}

	public function bench_less_is_more_with_strpos_optimization($uri)
	{
		if (strpos($uri, '://') !== FALSE)
		{
			// Chop off possible scheme, host, port, user and pass parts
			$uri = preg_replace('~^[-a-z0-9+.]++://[^/]++/?~', '', trim($uri, '/'));
		}

		return $uri;
	}

}