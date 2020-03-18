<?php

namespace Charcoal\Cms\Tests;

// From 'charcoal-cms'
use Charcoal\Cms\Tag;

use Charcoal\Tests\AbstractTestCase;
use Charcoal\Tests\Cms\ContainerIntegrationTrait;

/**
 *
 */
class TagTest extends AbstractTestCase
{
    use ContainerIntegrationTrait;

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
        $dependencies = $this->getModelDependenciesWithContainer();

        $this->obj = new Tag($dependencies);
    }

    /**
     * @return void
     */
    public function testSetData()
    {
        $ret = $this->obj->setData([
            'name'       => 'Foo?',
            'color'      => 'Bar',
            'variations' => [
                'en' => 'a,b,c',
            ],
            'search_weight' => 42,
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
