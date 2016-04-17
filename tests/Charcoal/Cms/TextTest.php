<?php

namespace Charcoal\Cms\Tests;

use \Charcoal\Cms\Text;

class TextTest extends \PHPUnit_Framework_TestCase
{
    public $obj;

    public function setUp()
    {
        $this->obj = new Text();
    }

    public function testSetData()
    {
        $ret = $this->obj->setData([
            'title'=>'Example title',
            'subtitle'=>'Subtitle',
            'content'=>'foobar'
        ]);

        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Example title', (string)$this->obj->title());
        $this->assertEquals('Subtitle', (string)$this->obj->subtitle());
        $this->assertEquals('foobar', (string)$this->obj->content());
    }

    public function testCategoryType()
    {
        $this->assertEquals('charcoal/cms/text-category', $this->obj->categoryType());
    }
}
