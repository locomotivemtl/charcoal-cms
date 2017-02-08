<?php

namespace Charcoal\Cms\Tests;

// From 'charcoal-cms'
use Charcoal\Cms\Text;
use Charcoal\Cms\TextCategory;

/**
 *
 */
class TextTest extends \PHPUnit_Framework_TestCase
{
    use \Charcoal\Tests\Cms\ContainerIntegrationTrait;

    /**
     * Tested Class.
     *
     * @var Text
     */
    private $obj;

    /**
     * Set up the test.
     */
    public function setUp()
    {
        $container = $this->getContainer();

        $this->obj = new Text([
            'container' => $container,
            'logger'    => $container['logger']
        ]);
    }

    public function testSetData()
    {
        $ret = $this->obj->setData([
            'title'    => 'Example title',
            'subtitle' => 'Subtitle',
            'content'  => 'foobar'
        ]);

        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Example title', (string)$this->obj->title());
        $this->assertEquals('Subtitle', (string)$this->obj->subtitle());
        $this->assertEquals('foobar', (string)$this->obj->content());
    }

    public function testCategoryType()
    {
        $this->assertEquals(TextCategory::class, $this->obj->categoryType());
    }
}
