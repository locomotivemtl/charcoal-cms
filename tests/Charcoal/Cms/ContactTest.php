<?php

namespace Charcoal\Cms\Tests;

use \Charcoal\Cms\Contact as Contact;

class ContactTest extends \PHPUnit_Framework_TestCase
{
    /**
    * Hello world
    */
    public function testConstructor()
    {
        $obj = new Contact();
        $this->assertInstanceOf('\Charcoal\Cms\Contact', $obj);
    }
}
