<?php

namespace Charcoal\Cms\Tests;

use \Charcoal\Cms\Faq;

class FaqTest extends \PHPUnit_Framework_TestCase
{

    public $obj;

    public function setUp()
    {
        $this->obj = new Faq();
    }

    public function testSetData()
    {
        $ret = $this->obj->setData([
            'question'=>'Foo?',
            'answer'=>'Bar'
        ]);
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Foo?', (string)$this->obj->question());
        $this->assertEquals('Bar', (string)$this->obj->answer());
    }

    public function testSetQuestion()
    {
        $ret = $this->obj->setQuestion('Foo?');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Foo?', $this->obj->question());
    }

    public function testSetAnswer()
    {
        $ret = $this->obj->setAnswer('Bar');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Bar', $this->obj->answer());
    }

    public function testCategoryType()
    {
        $this->assertEquals('charcoal/cms/faq-category', $this->obj->categoryType());
    }
}
