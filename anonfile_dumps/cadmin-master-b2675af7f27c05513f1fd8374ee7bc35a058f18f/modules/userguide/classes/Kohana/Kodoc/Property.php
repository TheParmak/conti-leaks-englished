<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Class property documentation generator.
 *
 * @package    Kohana/Userguide
 * @category   Base
 * @author     Kohana Team
 * @copyright  (c) 2009-2012 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kohana_Kodoc_Property extends Kodoc {

	/**
	 * @var  object  ReflectionProperty
	 */
	public $property;

	/**
	 * @var  string   modifiers: public, private, static, etc
	 */
	public $modifiers = 'public';

	/**
	 * @var  string  variable type, retrieved from the comment
	 */
	public $type;

	/**
	 * @var  string  value of the property
	 */
	public $value;

	/**
	 * @var  string  default value of the property
	 */
	public $default;

	public function __construct($class, $property, $default = NULL)
	{
		$property = new ReflectionProperty($class, $property);

		list($description, $tags) = Kodoc::parse($property->getDocComment());

		$this->description = $description;

		if ($modifiers = $property->getModifiers())
		{
			$this->modifiers = '<small>'.implode(' ', Reflection::getModifierNames($modifiers)).'</small> ';
		}

		if (isset($tags['var']))
		{
			if (preg_match('/^(\S*)(?:\s*(.+?))?$/s', $tags['var'][0], $matches))
			{
				$this->type = $matches[1];

				if (isset($matches[2]))
				{
					$this->description = Kodoc_Markdown::markdown($matches[2]);
				}
			}
		}

		$this->property = $property;

		// Show the value of static properties, but only if they are public or we are php 5.3 or higher and can force them to be accessible
		if ($property->isStatic() AND ($property->isPublic() OR version_compare(PHP_VERSION, '5.3', '>=')))
		{
			// Force the property to be accessible
			if (version_compare(PHP_VERSION, '5.3', '>='))
			{
				$property->setAccessible(TRUE);
			}

			// Don't debug the entire object, just say what kind of object it is
			if (is_object($property->getValue($class)))
			{
				$this->value = '<pre>object '.get_class($property->getValue($class)).'()</pre>';
			}
			else
			{
				$this->value = Debug::vars($property->getValue($class));
			}
		}

		// Store the defult property
		$this->default = Debug::vars($default);;
	}

} // End Kodoc_Property
