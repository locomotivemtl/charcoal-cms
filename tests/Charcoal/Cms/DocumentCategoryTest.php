<?php

namespace Charcoal\Cms\Tests;

use \Charcoal\Cms\DocumentCategory;

class DocumentCategoryTest extends \PHPUnit_Framework_TestCase
{

    public $obj;

    public function setUp()
    {
        $this->obj = new DocumentCategory();
    }

    public function testItemType()
    {
        $this->assertEquals('charcoal/cms/document', $this->obj->itemType());
    }
}
