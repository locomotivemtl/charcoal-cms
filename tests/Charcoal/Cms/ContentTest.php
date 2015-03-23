<?php

namespace Charcoal\Cms\Tests;

use \Charcoal\Cms\Content as Content;

class ContentTest extends \PHPUnit_Framework_TestCase
{
	/**
	* Hello world
	*/
	public function testConstructor()
	{
		$obj = new Content();
		$this->assertInstanceOf('\Charcoal\Cms\Content', $obj);
	}
}
