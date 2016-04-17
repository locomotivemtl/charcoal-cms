<?php

namespace Charcoal\Cms\Tests;

use \Charcoal\Cms\ImageCategory;

class ImageCategoryTest extends \PHPUnit_Framework_TestCase
{

    public $obj;

    public function setUp()
    {
        $this->obj = new ImageCategory();
    }

    public function testItemType()
    {
        $this->assertEquals('charcoal/cms/image', $this->obj->itemType());
    }
}
