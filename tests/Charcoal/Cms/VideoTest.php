<?php

namespace Charcoal\Cms\Tests;

// From 'charcoal-cms'
use Charcoal\Cms\Video;
use Charcoal\Cms\VideoCategory;

/**
 *
 */
class VideoTest extends \PHPUnit_Framework_TestCase
{
    use \Charcoal\Tests\Cms\ContainerIntegrationTrait;

    /**
     * Tested Class.
     *
     * @var Video
     */
    private $obj;

    /**
     * Set up the test.
     */
    public function setUp()
    {
        $container = $this->getContainer();

        $this->obj = new Video([
            'container' => $container,
            'logger'    => $container['logger']
        ]);
    }

    public function testSetData()
    {
        $ret = $this->obj->setData([
            'name'      => 'foo',
            'file'      => 'foobar',
            'base_path' => 'baz',
            'base_url'  => 'http://example.com/c'
        ]);
        $this->assertSame($ret, $this->obj);

        $this->assertEquals('foo', (string)$this->obj->name());
        $this->assertEquals('foobar', $this->obj->file());
        $this->assertEquals('baz/', $this->obj->basePath());
        $this->assertEquals('http://example.com/c/', $this->obj->baseUrl());
    }

    public function testCategoryType()
    {
        $this->assertEquals(VideoCategory::class, $this->obj->categoryType());
    }
}
