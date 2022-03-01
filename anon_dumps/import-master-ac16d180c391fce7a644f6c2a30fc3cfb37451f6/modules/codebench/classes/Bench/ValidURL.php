<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Kohana/Codebench
 * @category   Tests
 * @author     Geert De Deckere <geert@idoe.be>
 */
class Bench_ValidURL extends Codebench {

	public $description =
		'filter_var vs regex:
		 http://dev.kohanaframework.org/issues/2847';

	public $loops = 1000;

	public $subjects = array
	(
		// Valid
		'http://google.com',
		'http://google.com/',
		'http://google.com/?q=abc',
		'http://google.com/#hash',
		'http://localhost',
		'http://hello-world.pl',
		'http://hello--world.pl',
		'http://h.e.l.l.0.pl',
		'http://server.tld/get/info',
		'http://127.0.0.1',
		'http://127.0.0.1:80',
		'http://user@127.0.0.1',
		'http://user:pass@127.0.0.1',
		'ftp://my.server.com',
		'rss+xml://rss.example.com',

		// Invalid
		'http://google.2com',
		'http://google.com?q=abc',
		'http://google.com#hash',
		'http://hello-.pl',
		'http://hel.-lo.world.pl',
		'http://ww£.google.com',
		'http://127.0.0.1234',
		'http://127.0.0.1.1',
		'http://user:@127.0.0.1',
		"http://finalnewline.com\n",
	);

	public function bench_filter_var($url)
	{
		return (bool) filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
	}

	public function bench_regex($url)
	{
		// Based on http://www.apps.ietf.org/rfc/rfc1738.html#sec-5
		if ( ! preg_match(
			'~^

			# scheme
			[-a-z0-9+.]++://

			# username:password (optional)
			(?:
				    [-a-z0-9$_.+!*\'(),;?&=%]++   # username
				(?::[-a-z0-9$_.+!*\'(),;?&=%]++)? # password (optional)
				@
			)?

			(?:
				# ip address
				\d{1,3}+(?:\.\d{1,3}+){3}+

				| # or

				# hostname (captured)
				(
					     (?!-)[-a-z0-9]{1,63}+(?<!-)
					(?:\.(?!-)[-a-z0-9]{1,63}+(?<!-)){0,126}+
				)
			)

			# port (optional)
			(?::\d{1,5}+)?

			# path (optional)
			(?:/.*)?

			$~iDx', $url, $matches))
			return FALSE;

		// We matched an IP address
		if ( ! isset($matches[1]))
			return TRUE;

		// Check maximum length of the whole hostname
		// http://en.wikipedia.org/wiki/Domain_name#cite_note-0
		if (strlen($matches[1]) > 253)
			return FALSE;

		// An extra check for the top level domain
		// It must start with a letter
		$tld = ltrim(substr($matches[1], (int) strrpos($matches[1], '.')), '.');
		return ctype_alpha($tld[0]);
	}

}