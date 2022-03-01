<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Kohana/Codebench
 * @category   Tests
 * @author     Geert De Deckere <geert@idoe.be>
 */
class Bench_AutoLinkEmails extends Codebench {

	public $description =
		'Fixing <a href="http://dev.kohanaphp.com/issues/2772">#2772</a>, and comparing some possibilities.';

	public $loops = 1000;

	public $subjects = array
	(
		'<ul>
		    <li>voorzitter@xxxx.com</li>
		    <li>vicevoorzitter@xxxx.com</li>
		</ul>',
	);

	// The original function, with str_replace replaced by preg_replace. Looks clean.
	public function bench_match_all_loop($subject)
	{
		if (preg_match_all('~\b(?<!href="mailto:|">|58;)(?!\.)[-+_a-z0-9.]++(?<!\.)@(?![-.])[-a-z0-9.]+(?<!\.)\.[a-z]{2,6}\b~i', $subject, $matches))
		{
			foreach ($matches[0] as $match)
			{
				$subject = preg_replace('!\b'.preg_quote($match).'\b!', HTML::mailto($match), $subject);
			}
		}

		return $subject;
	}

	// The "e" stands for "eval", hmm... Ugly and slow because it needs to reinterpret the PHP code upon each match.
	public function bench_replace_e($subject)
	{
		return preg_replace(
			'~\b(?<!href="mailto:|">|58;)(?!\.)[-+_a-z0-9.]++(?<!\.)@(?![-.])[-a-z0-9.]+(?<!\.)\.[a-z]{2,6}\b~ie',
			'HTML::mailto("$0")', // Yuck!
			$subject
		);
	}

	// This one should be quite okay, it just requires an otherwise useless single-purpose callback.
	public function bench_replace_callback_external($subject)
	{
		return preg_replace_callback(
			'~\b(?<!href="mailto:|">|58;)(?!\.)[-+_a-z0-9.]++(?<!\.)@(?![-.])[-a-z0-9.]+(?<!\.)\.[a-z]{2,6}\b~i',
			array($this, '_callback_external'),
			$subject
		);
	}
	protected function _callback_external($matches)
	{
		return HTML::mailto($matches[0]);
	}

	// This one clearly is the ugliest, the slowest and consumes a lot of memory!
	public function bench_replace_callback_internal($subject)
	{
		return preg_replace_callback(
			'~\b(?<!href="mailto:|">|58;)(?!\.)[-+_a-z0-9.]++(?<!\.)@(?![-.])[-a-z0-9.]+(?<!\.)\.[a-z]{2,6}\b~i',
			create_function('$matches', 'return HTML::mailto($matches[0]);'), // Yuck!
			$subject
		);
	}

}