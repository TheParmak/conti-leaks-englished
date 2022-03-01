<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Kohana/Codebench
 * @category   Tests
 * @author     Geert De Deckere <geert@idoe.be>
 */
class Bench_GruberURL extends Codebench {

	public $description =
		'Optimization for http://daringfireball.net/2009/11/liberal_regex_for_matching_urls';

	public $loops = 10000;

	public $subjects = array
	(
		'http://foo.com/blah_blah',
		'http://foo.com/blah_blah/',
		'(Something like http://foo.com/blah_blah)',
		'http://foo.com/blah_blah_(wikipedia)',
		'(Something like http://foo.com/blah_blah_(wikipedia))',
		'http://foo.com/blah_blah.',
		'http://foo.com/blah_blah/.',
		'<http://foo.com/blah_blah>',
		'<http://foo.com/blah_blah/>',
		'http://foo.com/blah_blah,',
		'http://www.example.com/wpstyle/?p=364.',
		'http://✪df.ws/e7l',
		'rdar://1234',
		'rdar:/1234',
		'x-yojimbo-item://6303E4C1-xxxx-45A6-AB9D-3A908F59AE0E',
		'message://%3c330e7f8409726r6a4ba78dkf1fd71420c1bf6ff@mail.gmail.com%3e',
		'http://➡.ws/䨹',
		'www.➡.ws/䨹',
		'<tag>http://example.com</tag>',
		'Just a www.example.com link.',
		// To test the use of possessive quatifiers:
		'httpppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppp',
	);

	public function bench_daringfireball($subject)
	{
		// Original regex by John Gruber
		preg_match('~\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))~', $subject, $matches);
		return (empty($matches)) ? FALSE : $matches[0];
	}

	public function bench_daringfireball_v2($subject)
	{
		// Removed outer capturing parentheses, made another pair non-capturing
		preg_match('~\b(?:[\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|(?:[^[:punct:]\s]|/))~', $subject, $matches);
		return (empty($matches)) ? FALSE : $matches[0];
	}

	public function bench_daringfireball_v3($subject)
	{
		// Made quantifiers possessive where possible
		preg_match('~\b(?:[\w-]++://?+|www[.])[^\s()<>]+(?:\([\w\d]++\)|(?:[^[:punct:]\s]|/))~', $subject, $matches);
		return (empty($matches)) ? FALSE : $matches[0];
	}

}