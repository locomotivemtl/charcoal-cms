<?php

namespace Charcoal\Cms\Tests;

use \Charcoal\Cms\VideoCategory;

class VideoCategoryTest extends \PHPUnit_Framework_TestCase
{

    public $obj;

    public function setUp()
    {
        $this->obj = new VideoCategory();
    }

    public function testItemType()
    {
        $this->assertEquals('charcoal/cms/video', $this->obj->itemType());
    }
}
