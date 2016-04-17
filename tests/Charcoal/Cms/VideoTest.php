<?php

namespace Charcoal\Cms\Tests;

use \Charcoal\Cms\Video;

class VideoTest extends \PHPUnit_Framework_TestCase
{

    public $obj;

    public function setUp()
    {
        $this->obj = new Video();
    }

    public function testSetData()
    {
        $ret = $this->obj->setData([
            'name'=>'foo',
            'file'=>'foobar',
            'base_path'=>'baz',
            'base_url'=>'http://example.com/c'
        ]);
        $this->assertSame($ret, $this->obj);

        $this->assertEquals('foo', (string)$this->obj->name());
        $this->assertEquals('foobar', $this->obj->file());
        $this->assertEquals('baz/', $this->obj->basePath());
        $this->assertEquals('http://example.com/c/', $this->obj->baseUrl());
    }

    public function testCategoryType()
    {
        $this->assertEquals('charcoal/cms/video-category', $this->obj->categoryType());
    }
}
