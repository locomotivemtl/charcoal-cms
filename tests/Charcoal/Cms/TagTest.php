<?php

namespace Charcoal\Cms\Tests;

// From 'charcoal-cms'
use Charcoal\Cms\Tag;

use Charcoal\Tests\AbstractTestCase;

/**
 *
 */
class TagTest extends AbstractTestCase
{
    use \Charcoal\Tests\Cms\ContainerIntegrationTrait;

    /**
     * Tested Class.
     *
     * @var Tag
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

        $this->obj = new Tag([
            'container' => $container,
            'logger'    => $container['logger'],
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
            'name' => 'Foo?',
            'color'   => 'Bar',
            'variations' => [
                'en' => 'a,b,c'
            ],
            'search_weight' => 42
        ]);
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Foo?', (string)$this->obj->name());
        $this->assertEquals('Bar', (string)$this->obj->color());
        $this->assertEquals('a,b,c', $this->obj->variations());
        $this->assertEquals(42, $this->obj->searchWeight());
    }

    /**
     * @return void
     */
    public function testSetName()
    {
        $ret = $this->obj->setName('Foo?');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Foo?', $this->obj->name());
    }

    /**
     * @return void
     */
    public function testSetColor()
    {
        $ret = $this->obj->setColor('Bar');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Bar', $this->obj->color());
    }

    public function testSetVariations()
    {
        $ret = $this->obj->setVariations('foo,bar,baz');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('foo,bar,baz', $this->obj->variations());
    }

    public function testSetSearchWeight()
    {
        $ret = $this->obj->setSearchWeight(1984);
        $this->assertSame($ret, $this->obj);
        $this->assertEquals(1984, $this->obj->searchWeight());
    }
}
