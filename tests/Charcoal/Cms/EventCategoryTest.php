<?php

namespace Charcoal\Cms\Tests;

use \Charcoal\Cms\EventCategory;

class EventCategoryTest extends \PHPUnit_Framework_TestCase
{

    public $obj;

    public function setUp()
    {
        $this->obj = new EventCategory();
    }

    public function testItemType()
    {
        $this->assertEquals('charcoal/cms/event', $this->obj->itemType());
    }
}
