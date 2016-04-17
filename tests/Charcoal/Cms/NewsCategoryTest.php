<?php

namespace Charcoal\Cms\Tests;

use \Charcoal\Cms\NewsCategory;

class NewsCategoryTest extends \PHPUnit_Framework_TestCase
{

    public $obj;

    public function setUp()
    {
        $this->obj = new NewsCategory();
    }

    public function testItemType()
    {
        $this->assertEquals('charcoal/cms/news', $this->obj->itemType());
    }
}
