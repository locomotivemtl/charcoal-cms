<?php

namespace Charcoal\Cms\Tests;

use \Charcoal\Cms\Image;

class ImageTest extends \PHPUnit_Framework_TestCase
{

    public $obj;

    public function setUp()
    {
        $this->obj = new Image();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('\Charcoal\Cms\Image', $this->obj);
    }

    public function testCategoryType()
    {
        $this->assertEquals('charcoal/cms/image-category', $this->obj->categoryType());
    }
}
