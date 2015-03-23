<?php

namespace Charcoal\Cms\Tests;

use \Charcoal\Cms\Template as Template;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
	/**
	* Hello world
	*/
	public function testConstructor()
	{
		$obj = new Template();
		$this->assertInstanceOf('\Charcoal\Cms\Template', $obj);
	}
}
