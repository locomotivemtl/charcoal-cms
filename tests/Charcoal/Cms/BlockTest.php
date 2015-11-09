<?php

namespace Charcoal\Cms\Tests;

use \Charcoal\Cms\Block as Block;

class BlockTest extends \PHPUnit_Framework_TestCase
{
    /**
    * Hello world
    */
    public function testConstructor()
    {
        $obj = new Block();
        $this->assertInstanceOf('\Charcoal\Cms\Block', $obj);
    }

    public function testSetBlockType()
    {
        $obj = new Block();
        $ret = $obj->set_block_type('foo');
        $this->assertSame($ret, $obj);

        $this->assertEquals('foo', $obj->block_type());
    }

    public function testSetParentType()
    {
        $obj = new Block();
//		$this->assertSame();
        $ret = $obj->set_parent_type('foo');
        $this->assertSame($ret, $obj);

        $this->assertEquals('foo', $obj->parent_type());
    }
}
