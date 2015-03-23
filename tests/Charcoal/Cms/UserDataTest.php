<?php

namespace Charcoal\Cms\Tests;

use \Charcoal\Cms\UserData as UserData;

class UserDataTest extends \PHPUnit_Framework_TestCase
{
	/**
	* Hello world
	*/
	public function testConstructor()
	{
		$obj = new UserData();
		$this->assertInstanceOf('\Charcoal\Cms\UserData', $obj);
	}
}
