<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @package    Kohana/Codebench
 * @category   Tests
 * @author     Geert De Deckere <geert@idoe.be>
 */
class Bench_MDDoImageURL extends Codebench {

	public $description =
		'Optimization for the <code>doImageURL()</code> method of <code>Kohana_Kodoc_Markdown</code>
		 for the Kohana Userguide.';

	public $loops = 10000;

	public $subjects = array
	(
		// Valid matches
		'![Alt text](http://img.skitch.com/20091019-rud5mmqbf776jwua6hx9nm1n.png)',
		'![Alt text](https://img.skitch.com/20091019-rud5mmqbf776jwua6hx9nm1n.png)',
		'![Alt text](otherprotocol://image.png "Optional title")',
		'![Alt text](img/install.png "Optional title")',
		'![Alt text containing [square] brackets](img/install.png)',
		'![Empty src]()',

		// Invalid matches
		'![Alt text](img/install.png                 "No closing parenthesis"',
	);

	public function bench_original($subject)
	{
		return preg_replace_callback('~!\[(.+?)\]\((\S*(?:\s*".+?")?)\)~', array($this, '_add_image_url_original'), $subject);
	}
	protected function _add_image_url_original($matches)
	{
		if ($matches[2] AND strpos($matches[2], '://') === FALSE)
		{
			// Add the base url to the link URL
			$matches[2] = 'http://BASE/'.$matches[2];
		}

		// Recreate the link
		return "![{$matches[1]}]({$matches[2]})";
	}

	public function bench_optimized_callback($subject)
	{
		// Moved the check for "://" to the regex, simplifying the callback function
		return preg_replace_callback('~!\[(.+?)\]\((?!\w++://)(\S*(?:\s*+".+?")?)\)~', array($this, '_add_image_url_optimized'), $subject);
	}
	protected function _add_image_url_optimized($matches)
	{
		// Add the base url to the link URL
		$matches[2] = 'http://BASE/'.$matches[2];

		// Recreate the link
		return "![{$matches[1]}]({$matches[2]})";
	}

	public function bench_callback_gone($subject)
	{
		// All the optimized callback was doing now, is prepend some text to the URL.
		// We don't need a callback for that, and that should be clearly faster.
		return preg_replace('~(!\[.+?\]\()(?!\w++://)(\S*(?:\s*+".+?")?\))~', '$1http://BASE/$2', $subject);
	}

}