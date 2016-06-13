<?php

namespace Charcoal\Cms\Tests;

use \Psr\Log\NullLogger;
use \Cache\Adapter\Void\VoidCachePool;

use \Charcoal\Cms\Event;

class EventTest extends \PHPUnit_Framework_TestCase
{

    public $obj;

    public function setUp()
    {
        $metadataLoader = new \Charcoal\Model\MetadataLoader([
            'logger' => new NullLogger(),
            'base_path' => __DIR__,
            'paths' => ['metadata'],
            'cache'  => new VoidCachePool()
        ]);

        $this->obj = new Event([
            'logger'=> new NullLogger(),
            'metadata_loader' => $metadataLoader
        ]);;
    }

    public function testSetData()
    {
        $ret = $this->obj->setData([
            'title'=>'Example title',
            'subtitle'=>'Subtitle',
            'content'=>'foobar',
            'start_date'=>'2015-01-01 20:00:00',
            'end_date'=>'2015-01-01 21:30:00'
        ]);

        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Example title', (string)$this->obj->title());
        $this->assertEquals('Subtitle', (string)$this->obj->subtitle());
        $this->assertEquals('foobar', (string)$this->obj->content());
        $this->assertEquals(new \DateTime('2015-01-01 20:00:00
            '), $this->obj->startDate());
    }

    public function testSetTitle()
    {
        $this->assertEquals('', (string)$this->obj->title());
        $ret = $this->obj->setTitle('Foo bar');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Foo bar', (string)$this->obj->title());
    }

    public function testSetSubtitle()
    {
        $this->assertEquals('', (string)$this->obj->subtitle());
        $ret = $this->obj->setSubtitle('Bar foo');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Bar foo', (string)$this->obj->subtitle());
    }

    public function testSetContent()
    {
        $this->assertEquals('', (string)$this->obj->content());
        $ret = $this->obj->setContent('Bar foo');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Bar foo', (string)$this->obj->content());
    }

    public function testSetStartDate()
    {
        $this->assertEquals(null, $this->obj->startDate());
        $ret = $this->obj->setStartDate('2016-02-02');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals(new \DateTime('2016-02-02'), $ret->startDate());

        $this->obj->setStartDate(null);
        $this->assertEquals(null, $this->obj->startDate());

        $this->setExpectedException('\InvalidArgumentException');
        $this->obj->setStartDate([]);
    }

    public function testSetEndDate()
    {
        $this->assertEquals(null, $this->obj->endDate());
        $ret = $this->obj->setEndDate('2016-02-02');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals(new \DateTime('2016-02-02'), $ret->endDate());

        $this->obj->setEndDate(null);
        $this->assertEquals(null, $this->obj->endDate());

        $this->setExpectedException('\InvalidArgumentException');
        $this->obj->setEndDate([]);
    }

    public function testCategoryType()
    {
        $this->assertEquals('charcoal/cms/event-category', $this->obj->categoryType());
    }

    public function testMetaTitleDefaultsToTitle()
    {
        $this->assertEquals('', (string)$this->obj->metaTitle());

        $this->obj->setTitle('Foo Bar');
        $this->assertSame($this->obj->title(), $this->obj->metaTitle());
        $this->assertEquals('Foo Bar', (string)$this->obj->metaTitle());

        $this->obj->setMetaTitle('Barfoo');
        $this->assertEquals('Barfoo', (string)$this->obj->metaTitle());
    }

    public function testMetaDescriptionDefaultsToDescription()
    {
        $this->assertEquals('', (string)$this->obj->metaDescription());

        $this->obj->setContent('Foo Bar');
        $this->assertSame($this->obj->content(), $this->obj->metaDescription());
        $this->assertEquals('Foo Bar', (string)$this->obj->metaDescription());

        $this->obj->setMetaDescription('Barfoo');
        $this->assertEquals('Barfoo', (string)$this->obj->metaDescription());
    }

    // public function testMetaImageDefaultsToImage()
    // {
    //     $this->assertEquals('', (string)$this->obj->metaImage());

    //     $this->obj->setImage('Foo.png');
    //     $this->assertSame($this->obj->image(), $this->obj->metaImage());
    //     $this->assertEquals('Foo.png', (string)$this->obj->metaImage());

    //     $this->obj->setMetaImage('Bar.jpg');
    //     $this->assertEquals('Bar.jpg', (string)$this->obj->metaImage());
    // }
}
