<?php

namespace Charcoal\Cms\Tests;

use \Charcoal\Cms\Section as Section;

class SectionTest extends \PHPUnit_Framework_TestCase
{
	/**
	* Hello world
	*/
	public function testConstructor()
	{
		$obj = new Section();
		$this->assertInstanceOf('\Charcoal\Cms\Section', $obj);
	}
}
