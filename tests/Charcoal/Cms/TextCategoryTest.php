<?php

namespace Charcoal\Cms\Tests;

use \Charcoal\Cms\TextCategory;

class TextCategoryTest extends \PHPUnit_Framework_TestCase
{

    public $obj;

    public function setUp()
    {
        $this->obj = new TextCategory();
    }

    public function testItemType()
    {
        $this->assertEquals('charcoal/cms/text', $this->obj->itemType());
    }
}
