<?php

namespace Charcoal\Cms\Tests;

use \Charcoal\Cms\Document as Document;

class DocumentTest extends \PHPUnit_Framework_TestCase
{
	/**
	* Hello world
	*/
	public function testConstructor()
	{
		$obj = new Document();
		$this->assertInstanceOf('\Charcoal\Cms\Document', $obj);
	}
}
