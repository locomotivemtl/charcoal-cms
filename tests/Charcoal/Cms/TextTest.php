<?php

namespace Charcoal\Cms\Tests;

use \Charcoal\Cms\Text as Text;

class TextTest extends \PHPUnit_Framework_TestCase
{
	/**
	* Hello world
	*/
	public function testConstructor()
	{
		$obj = new Text();
		$this->assertInstanceOf('\Charcoal\Cms\Text', $obj);
	}
}
