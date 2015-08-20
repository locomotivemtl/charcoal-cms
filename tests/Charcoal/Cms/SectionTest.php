<?php

namespace Charcoal\Cms\Tests;

use \Charcoal\Cms\Section as Section;

class SectionTest extends \PHPUnit_Framework_TestCase
{
	/**
	* Hello world
	*/
	public function testConstructor()
	{
		$obj = new Section();
		$this->assertInstanceOf('\Charcoal\Cms\Section', $obj);
	}

    public function testSetData()
    {
        $obj = new Section();
        $ret = $obj->set_data([
            'section_type'=>Section::TYPE_EXTERNAL,
            'title'=>'foo',
            'subtitle'=>'bar',
            'content'=>'baz'
        ]);
        $this->assertSame($ret, $obj);

        $this->assertEquals(Section::TYPE_EXTERNAL, $obj->section_type());
        $this->assertEquals('foo', (string)$obj->title());
        $this->assertEquals('bar', (string)$obj->subtitle());
        $this->assertEquals('baz', (string)$obj->content());
    }
}
