<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Kohana/Codebench
 * @category   Tests
 * @author     Geert De Deckere <geert@idoe.be>
 */
class Bench_Transliterate extends Codebench {

	public $description =
		'Inspired by:
		 http://forum.kohanaframework.org/comments.php?DiscussionID=6113';

	public $loops = 10;

	public $subjects = array
	(
		// ASCII
		'a', 'b', 'c', 'd', '1', '2', '3',

		// Non-ASCII
		'à', 'ô', 'ď', 'ḟ', 'ë', 'š', 'ơ',
		'ß', 'ă', 'ř', 'ț', 'ň', 'ā', 'ķ',
		'ŝ', 'ỳ', 'ņ', 'ĺ', 'ħ', 'ṗ', 'ó',
		'ú', 'ě', 'é', 'ç', 'ẁ', 'ċ', 'õ',
		'ṡ', 'ø', 'ģ', 'ŧ', 'ș', 'ė', 'ĉ',
		'ś', 'î', 'ű', 'ć', 'ę', 'ŵ', 'ṫ',
		'ū', 'č', 'ö', 'è', 'ŷ', 'ą', 'ł',
		'ų', 'ů', 'ş', 'ğ', 'ļ', 'ƒ', 'ž',
		'ẃ', 'ḃ', 'å', 'ì', 'ï', 'ḋ', 'ť',
		'ŗ', 'ä', 'í', 'ŕ', 'ê', 'ü', 'ò',
		'ē', 'ñ', 'ń', 'ĥ', 'ĝ', 'đ', 'ĵ',
		'ÿ', 'ũ', 'ŭ', 'ư', 'ţ', 'ý', 'ő',
		'â', 'ľ', 'ẅ', 'ż', 'ī', 'ã', 'ġ',
		'ṁ', 'ō', 'ĩ', 'ù', 'į', 'ź', 'á',
		'û', 'þ', 'ð', 'æ', 'µ', 'ĕ', 'ı',
		'À', 'Ô', 'Ď', 'Ḟ', 'Ë', 'Š', 'Ơ',
		'Ă', 'Ř', 'Ț', 'Ň', 'Ā', 'Ķ', 'Ĕ',
		'Ŝ', 'Ỳ', 'Ņ', 'Ĺ', 'Ħ', 'Ṗ', 'Ó',
		'Ú', 'Ě', 'É', 'Ç', 'Ẁ', 'Ċ', 'Õ',
		'Ṡ', 'Ø', 'Ģ', 'Ŧ', 'Ș', 'Ė', 'Ĉ',
		'Ś', 'Î', 'Ű', 'Ć', 'Ę', 'Ŵ', 'Ṫ',
		'Ū', 'Č', 'Ö', 'È', 'Ŷ', 'Ą', 'Ł',
		'Ų', 'Ů', 'Ş', 'Ğ', 'Ļ', 'Ƒ', 'Ž',
		'Ẃ', 'Ḃ', 'Å', 'Ì', 'Ï', 'Ḋ', 'Ť',
		'Ŗ', 'Ä', 'Í', 'Ŕ', 'Ê', 'Ü', 'Ò',
		'Ē', 'Ñ', 'Ń', 'Ĥ', 'Ĝ', 'Đ', 'Ĵ',
		'Ÿ', 'Ũ', 'Ŭ', 'Ư', 'Ţ', 'Ý', 'Ő',
		'Â', 'Ľ', 'Ẅ', 'Ż', 'Ī', 'Ã', 'Ġ',
		'Ṁ', 'Ō', 'Ĩ', 'Ù', 'Į', 'Ź', 'Á',
		'Û', 'Þ', 'Ð', 'Æ', 'İ',
	);

	public function bench_utf8($subject)
	{
		return UTF8::transliterate_to_ascii($subject);
	}

	public function bench_iconv($subject)
	{
		// Note: need to suppress errors on iconv because some chars trigger the following notice:
		// "Detected an illegal character in input string"
		return preg_replace('~[^-a-z0-9]+~i', '', @iconv('UTF-8', 'ASCII//TRANSLIT', $subject));
	}

}