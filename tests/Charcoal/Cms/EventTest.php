<?php

namespace Charcoal\Cms\Tests;

use DateTime;

// From 'charcoal-object'
use Charcoal\Object\ObjectRoute;

// From 'charcoal-cms'
use Charcoal\Cms\Event;
use Charcoal\Cms\EventCategory;
use Charcoal\Tests\AbstractTestCase;

/**
 *
 */
class EventTest extends AbstractTestCase
{
    use \Charcoal\Tests\Cms\ContainerIntegrationTrait;

    /**
     * Tested Class.
     *
     * @var Event
     */
    private $obj;

    /**
     * Set up the test.
     *
     * @return void
     */
    public function setUp()
    {
        $container = $this->getContainer();

        $route = $container['model/factory']->get(ObjectRoute::class);
        if ($route->source()->tableExists() === false) {
            $route->source()->createTable();
        }

        $this->obj = new Event([
            'container'         => $container,
            'logger'            => $container['logger'],
            'metadata_loader'   => $container['metadata/loader'],
            'property_factory'  => $container['property/factory'],
            'source_factory'    => $container['source/factory']
        ]);
    }

    /**
     * @return void
     */
    public function testSetData()
    {
        $ret = $this->obj->setData([
            'title'      => 'Example title',
            'subtitle'   => 'Subtitle',
            'content'    => 'foobar',
            'start_date' => '2015-01-01 20:00:00',
            'end_date'   => '2015-01-01 21:30:00'
        ]);

        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Example title', (string)$this->obj->title());
        $this->assertEquals('Subtitle', (string)$this->obj->subtitle());
        $this->assertEquals('foobar', (string)$this->obj->content());
        $this->assertEquals(new DateTime('2015-01-01 20:00:00'), $this->obj->startDate());
    }

    /**
     * @return void
     */
    public function testSetTitle()
    {
        $this->assertEquals('', (string)$this->obj->title());
        $ret = $this->obj->setTitle('Foo bar');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Foo bar', (string)$this->obj->title());

        $this->obj['title'] = 'Bar';
        $this->assertEquals('Bar', (string)$this->obj->title());

        $this->obj->set('title', 'Hello');
        $this->assertEquals('Hello', (string)$this->obj['title']);
    }

    /**
     * @return void
     */
    public function testSetSubtitle()
    {
        $this->assertEquals('', (string)$this->obj->subtitle());
        $ret = $this->obj->setSubtitle('Bar foo');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Bar foo', (string)$this->obj->subtitle());

        $this->obj['subtitle'] = 'Foobar';
        $this->assertEquals('Foobar', (string)$this->obj->subtitle());

        $this->obj->set('subtitle', 'foo');
        $this->assertEquals('foo', (string)$this->obj['subtitle']);
    }

    /**
     * @return void
     */
    public function testSetSummary()
    {
        $this->assertEquals('', (string)$this->obj->summary());
        $ret = $this->obj->setSummary('Bar foo baz');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Bar foo baz', (string)$this->obj->summary());

        $this->obj['summary'] = 'Foobar';
        $this->assertEquals('Foobar', (string)$this->obj->summary());

        $this->obj->set('summary', 'foo');
        $this->assertEquals('foo', (string)$this->obj['summary']);
    }

    /**
     * @return void
     */
    public function testSetContent()
    {
        $this->assertEquals('', (string)$this->obj->content());
        $ret = $this->obj->setContent('Bar foo');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Bar foo', (string)$this->obj->content());

        $this->obj['content'] = 'Foobar';
        $this->assertEquals('Foobar', (string)$this->obj->content());

        $this->obj->set('content', 'foo');
        $this->assertEquals('foo', (string)$this->obj['content']);
    }

    /**
     * @return void
     */
    public function testSetStartDate()
    {
        $this->assertEquals(null, $this->obj->startDate());
        $ret = $this->obj->setStartDate('2016-02-02');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals(new DateTime('2016-02-02'), $ret->startDate());

        $this->obj->setStartDate(null);
        $this->assertEquals(null, $this->obj->startDate());

        $this->expectException('\InvalidArgumentException');
        $this->obj->setStartDate([]);
    }

    /**
     * @return void
     */
    public function testSetStartDateInvalidString()
    {
        $this->expectException('\Exception');
        $this->obj->setStartDate('foo.bar');
    }

    /**
     * @return void
     */
    public function testSetEndDate()
    {
        $this->assertEquals(null, $this->obj->endDate());
        $ret = $this->obj->setEndDate('2016-02-02');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals(new DateTime('2016-02-02'), $ret->endDate());

        $this->obj->setEndDate(null);
        $this->assertEquals(null, $this->obj->endDate());

        $this->expectException('\InvalidArgumentException');
        $this->obj->setEndDate([]);
    }

    /**
     * @return void
     */
    public function testSetEndDateInvalidString()
    {
        $this->expectException('\Exception');
        $this->obj->setEndDate('foo.bar');
    }

    /**
     * @return void
     */
    public function testCategoryType()
    {
        $this->assertEquals(EventCategory::class, $this->obj->categoryType());
    }

    /**
     * @return void
     */
    public function testMetaTitleDefaultsToTitle()
    {
        $this->assertEquals('', (string)$this->obj->metaTitle());

        $this->obj->setTitle('Foo Bar');
        $this->obj->generateDefaultMetaTags();
        $this->assertEquals('Foo Bar', (string)$this->obj->metaTitle());

        $this->obj->setMetaTitle('Barfoo');
        $this->assertEquals('Barfoo', (string)$this->obj->metaTitle());
    }

    /**
     * @return void
     */
    public function testMetaDescriptionDefaultsToDescription()
    {
        $this->assertEquals('', (string)$this->obj->metaDescription());

        $this->obj->setContent('Foo Bar');
        $this->obj->generateDefaultMetaTags();
        $this->assertEquals('Foo Bar', (string)$this->obj->metaDescription());

        $this->obj->setMetaDescription('Barfoo');
        $this->assertEquals('Barfoo', (string)$this->obj->metaDescription());
    }

    /**
     * @return void
     */
    /*
    public function testMetaImageDefaultsToImage()
    {
        $this->assertEquals('', (string)$this->obj->metaImage());

        $this->obj->setImage('Foo.png');
        $this->assertSame($this->obj->image(), $this->obj->metaImage());
        $this->assertEquals('Foo.png', (string)$this->obj->metaImage());

        $this->obj->setMetaImage('Bar.jpg');
        $this->assertEquals('Bar.jpg', (string)$this->obj->metaImage());
    }
    */

    /**
     * @return void
     */
    public function testSaveGeneratesSlug()
    {
        $this->assertEquals('', $this->obj->slug());
        $this->obj->setData([
            'title' => 'foo'
        ]);
        $this->obj->save();

        $this->assertEquals('en/events/foo', (string)$this->obj->slug());
    }

    /**
     * @return void
     */
    public function testUpdateGeneratesSlug()
    {
        $this->assertEquals('', $this->obj->slug());
        $this->obj->setData([
            'title' => 'foo'
        ]);
        $this->obj->update();

        $this->assertEquals('en/events/foo', (string)$this->obj->slug());
    }
}
