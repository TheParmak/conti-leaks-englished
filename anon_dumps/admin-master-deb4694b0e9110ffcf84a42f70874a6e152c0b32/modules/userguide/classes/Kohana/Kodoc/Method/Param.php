<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Class method parameter documentation generator.
 *
 * @package    Kohana/Userguide
 * @category   Base
 * @author     Kohana Team
 * @copyright  (c) 2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kohana_Kodoc_Method_Param extends Kodoc {

	/**
	 * @var  object  ReflectionParameter for this property
	 */
	public $param;

	/**
	 * @var  string  name of this var
	 */
	public $name;

	/**
	 * @var  string  variable type, retrieved from the comment
	 */
	public $type;

	/**
	 * @var  string  default value of this param
	 */
	public $default;

	/**
	 * @var  string  description of this parameter
	 */
	public $description;

	/**
	 * @var  boolean  is the parameter passed by reference?
	 */
	public $reference = FALSE;

	/**
	 * @var  boolean  is the parameter optional?
	 */
	public $optional = FALSE;

	public function __construct($method, $param)
	{
		$this->param = new ReflectionParameter($method, $param);

		$this->name = $this->param->name;

		if ($this->param->isDefaultValueAvailable())
		{
			$this->default = Debug::dump($this->param->getDefaultValue());
		}

		if ($this->param->isPassedByReference())
		{
			$this->reference = TRUE;
		}

		if ($this->param->isOptional())
		{
			$this->optional = TRUE;
		}
	}

	public function __toString()
	{
		$display = '';

		if ($this->type)
		{
			$display .= '<small>'.$this->type.'</small> ';
		}

		if ($this->reference)
		{
			$display .= '<small><abbr title="passed by reference">&</abbr></small> ';
		}

		if ($this->description)
		{
			$display .= '<span class="param" title="'.preg_replace('/\s+/', ' ', $this->description).'">$'.$this->name.'</span> ';
		}
		else
		{
			$display .= '$'.$this->name.' ';
		}

		if ($this->default)
		{
			$display .= '<small>= '.$this->default.'</small> ';
		}

		return $display;
	}

} // End Kodoc_Method_Param
