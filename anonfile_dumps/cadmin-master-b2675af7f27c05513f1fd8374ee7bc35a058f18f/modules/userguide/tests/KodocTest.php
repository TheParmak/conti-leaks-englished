<?php

/**
 * @group kohana
 * @group kohana.userguide
 *
 * @package    Kohana/Userguide
 * @author     Kohana Team
 * @copyright  (c) 2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Kohana_KodocTest extends PHPUnit_Framework_TestCase
{
	public function provider_parse_basic()
	{
		return array(
			array(
<<<'COMMENT'
/**
 * Description
 */
COMMENT
,
				array("<p>Description</p>\n", array()),
			),
			array(
<<<'COMMENT'
/**
 * Description spanning
 * multiple lines
 */
COMMENT
,
				array("<p>Description spanning\nmultiple lines</p>\n", array()),
			),
			array(
<<<'COMMENT'
/**
 * Description including
 *
 *     a code block
 */
COMMENT
,
				array("<p>Description including</p>\n\n<pre><code>a code block\n</code></pre>\n", array()),
			),
			array(
<<<'COMMENT'
	/**
	 * Indented
	 */
COMMENT
,
				array("<p>Indented</p>\n", array()),
			),
			array(
<<<'COMMENT'
/**
 * @tag Content
 */
COMMENT
,
				array('', array('tag' => array('Content'))),
			),
			array(
<<<'COMMENT'
/**
 * @tag Multiple
 * @tag Tags
 */
COMMENT
,
				array('', array('tag' => array('Multiple', 'Tags'))),
			),
			array(
<<<'COMMENT'
/**
 * Description with tag
 * @tag Content
 */
COMMENT
,
				array(
					"<p>Description with tag</p>\n",
					array('tag' => array('Content')),
				),
			),
			array(
<<<'COMMENT'
/**
 * @trailingspace 
 */
COMMENT
,
				array('', array('trailingspace' => array(''))),
			),
			array(
<<<'COMMENT'
/**
 * @tag Content that spans
 * multiple lines
 */
COMMENT
,
				array(
					'',
					array('tag' => array("Content that spans\nmultiple lines")),
				),
			),
			array(
<<<'COMMENT'
/**
 * @tag Content that spans
 *    multiple lines indented
 */
COMMENT
,
				array(
					'',
					array('tag' => array("Content that spans\n   multiple lines indented")),
				),
			),
		);
	}

	/**
	 * @covers  Kohana_Kodoc::parse
	 *
	 * @dataProvider    provider_parse_basic
	 *
	 * @param   string  $comment    Argument to the method
	 * @param   array   $expected   Expected result
	 */
	public function test_parse_basic($comment, $expected)
	{
		$this->assertSame($expected, Kodoc::parse($comment));
	}

	public function provider_parse_tags()
	{
		$route_api = Route::get('docs/api');

		return array(
			array(
<<<'COMMENT'
/**
 * @access public
 */
COMMENT
,
				array('', array()),
			),
			array(
<<<'COMMENT'
/**
 * @copyright Some plain text
 */
COMMENT
,
				array('', array('copyright' => array('Some plain text'))),
			),
			array(
<<<'COMMENT'
/**
 * @copyright (c) 2012 Kohana Team
 */
COMMENT
,
				array('', array('copyright' => array('&copy; 2012 Kohana Team'))),
			),
			array(
<<<'COMMENT'
/**
 * @license Kohana
 */
COMMENT
,
				array('', array('license' => array('Kohana'))),
			),
			array(
<<<'COMMENT'
/**
 * @license http://kohanaframework.org/license
 */
COMMENT
,
				array('', array('license' => array('<a href="http://kohanaframework.org/license">http://kohanaframework.org/license</a>'))),
			),
			array(
<<<'COMMENT'
/**
 * @link http://kohanaframework.org
 */
COMMENT
,
				array('', array('link' => array('<a href="http://kohanaframework.org">http://kohanaframework.org</a>'))),
			),
			array(
<<<'COMMENT'
/**
 * @link http://kohanaframework.org Description
 */
COMMENT
,
				array('', array('link' => array('<a href="http://kohanaframework.org">Description</a>'))),
			),
			array(
<<<'COMMENT'
/**
 * @see MyClass
 */
COMMENT
,
				array(
					'',
					array(
						'see' => array(
							'<a href="'.URL::site(
								$route_api->uri(array('class' => 'MyClass'))
							).'">MyClass</a>',
						),
					),
				),
			),
			array(
<<<'COMMENT'
/**
 * @see MyClass::method()
 */
COMMENT
,
				array(
					'',
					array(
						'see' => array(
							'<a href="'.URL::site(
								$route_api->uri(array('class' => 'MyClass')).'#method'
							).'">MyClass::method()</a>',
						),
					),
				),
			),
			array(
<<<'COMMENT'
/**
 * @throws Exception
 */
COMMENT
,
				array(
					'',
					array(
						'throws' => array(
							'<a href="'.URL::site(
								$route_api->uri(array('class' => 'Exception'))
							).'">Exception</a>',
						),
					),
				),
			),
			array(
<<<'COMMENT'
/**
 * @throws Exception During failure
 */
COMMENT
,
				array(
					'',
					array(
						'throws' => array(
							'<a href="'.URL::site(
								$route_api->uri(array('class' => 'Exception'))
							).'">Exception</a> During failure',
						),
					),
				),
			),
			array(
<<<'COMMENT'
/**
 * @uses MyClass
 */
COMMENT
,
				array(
					'',
					array(
						'uses' => array(
							'<a href="'.URL::site(
								$route_api->uri(array('class' => 'MyClass'))
							).'">MyClass</a>',
						),
					),
				),
			),
			array(
<<<'COMMENT'
/**
 * @uses MyClass::method()
 */
COMMENT
,
				array(
					'',
					array(
						'uses' => array(
							'<a href="'.URL::site(
								$route_api->uri(array('class' => 'MyClass')).'#method'
							).'">MyClass::method()</a>',
						),
					),
				),
			),
		);
	}

	/**
	 * @covers  Kohana_Kodoc::format_tag
	 * @covers  Kohana_Kodoc::parse
	 *
	 * @dataProvider    provider_parse_tags
	 *
	 * @param   string  $comment    Argument to the method
	 * @param   array   $expected   Expected result
	 */
	public function test_parse_tags($comment, $expected)
	{
		$this->assertSame($expected, Kodoc::parse($comment));
	}
	
	/**
	 * Provides test data for test_transparent_classes
	 * @return array
	 */
	public function provider_transparent_classes()
	{
		return array(
			// Kohana_Core is a special case
			array('Kohana','Kohana_Core',NULL),
			array('Controller_Template','Kohana_Controller_Template',NULL),
			array('Controller_Template','Kohana_Controller_Template',
				array('Kohana_Controller_Template'=>'Kohana_Controller_Template',
					'Controller_Template'=>'Controller_Template')
			),
			array(FALSE,'Kohana_Controller_Template',
				array('Kohana_Controller_Template'=>'Kohana_Controller_Template')),
			array(FALSE,'Controller_Template',NULL),
		);
	}

	/**
	 * Tests Kodoc::is_transparent
	 *
	 * Checks that a selection of transparent and non-transparent classes give expected results
	 *
	 * @group kohana.userguide.3529-configurable-transparent-classes
	 * @dataProvider provider_transparent_classes
	 * @param mixed $expected
	 * @param string $class
	 * @param array $classes
	 */
	public function test_transparent_classes($expected, $class, $classes)
	{
		$result = Kodoc::is_transparent($class, $classes);
		$this->assertSame($expected,$result);
	}
}
