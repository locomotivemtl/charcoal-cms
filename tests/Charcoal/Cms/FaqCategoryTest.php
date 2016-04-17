<?php

namespace Charcoal\Cms\Tests;

use \Charcoal\Cms\FaqCategory;

class FaqCategoryTest extends \PHPUnit_Framework_TestCase
{

    public $obj;

    public function setUp()
    {
        $this->obj = new FaqCategory();
    }

    public function testItemType()
    {
        $this->assertEquals('charcoal/cms/faq', $this->obj->itemType());
    }
}
