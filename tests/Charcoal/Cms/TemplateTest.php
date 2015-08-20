<?php

namespace Charcoal\Cms\Tests;

use \Charcoal\Cms\Template as Template;

class TemplateTest extends \PHPUnit_Framework_TestCase
{

    /**
    * Asser
    */
    public function testSetData()
    {
        $obj = new Template();
        $ret = $obj->set_data([
            'ident'=>'foo',
            'title'=>'bar',
            'options'=>['foo'=>'bar']
        ]);
        $this->assertSame($ret, $obj);

        $this->assertEquals('foo', $obj->ident());
        $this->assertEquals('bar', (string)$obj->title());
        $this->assertEquals(['foo'=>'bar'], $obj->options());
    }

    /**
    *
    */
    public function testSetIdent()
    {
        $obj = new Template();
        $ret = $obj->set_ident('bar');
        $this->assertSame($ret, $obj);

        $this->assertEquals('bar', $obj->ident());

        $this->setExpectedException('\InvalidArgumentException');
        $obj->set_ident(false);
    }
}
