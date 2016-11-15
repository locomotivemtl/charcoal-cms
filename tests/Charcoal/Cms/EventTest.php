<?php

namespace Charcoal\Cms\Tests;

use \Psr\Log\NullLogger;
use \Cache\Adapter\Void\VoidCachePool;

use \Pimple\Container;

use \Charcoal\Model\Service\MetadataLoader;

use \Charcoal\Cms\Event;

use \Charcoal\Tests\Cms\ContainerProvider;

class EventTest extends \PHPUnit_Framework_TestCase
{

    public $obj;

    public function setUp()
    {
        $provider = new ContainerProvider();

        $container = new Container();
        $provider->registerBaseServices($container);
        $provider->registerMetadataLoader($container);
        $provider->registerModelFactory($container);
        $provider->registerSourceFactory($container);
        $provider->registerPropertyFactory($container);
        $provider->registerModelCollectionLoader($container);

        $this->obj = new Event([
            'container'         => $container,
            'logger'            => $container['logger'],
            'metadata_loader'   => $container['metadata/loader'],
            'property_factory'  => $container['property/factory'],
            'source_factory'    => $container['source/factory']
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

    public function testSetSummary()
    {
        $this->assertEquals('', (string)$this->obj->summary());
        $ret = $this->obj->setSummary('Bar foo baz');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Bar foo baz', (string)$this->obj->summary());
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

    public function testSaveGeneratesSlug()
    {
        $this->obj->setData([
            'title'=>'foo'
        ]);
        $this->obj->save();

        $this->assertEquals('en/events/foo', (string)$this->obj->slug());
    }
}
