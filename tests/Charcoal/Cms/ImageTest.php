<?php

namespace Charcoal\Cms\Tests;

// From 'charcoal-cms'
use Charcoal\Cms\Image;
use Charcoal\Cms\ImageCategory;

/**
 *
 */
class ImageTest extends \PHPUnit_Framework_TestCase
{
    use \Charcoal\Tests\Cms\ContainerIntegrationTrait;

    /**
     * Tested Class.
     *
     * @var Image
     */
    private $obj;

    /**
     * Set up the test.
     */
    public function setUp()
    {
        $container = $this->getContainer();

        $this->obj = new Image([
            'container' => $container,
            'logger'    => $container['logger']
        ]);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(Image::class, $this->obj);
    }

    public function testCategoryType()
    {
        $this->assertEquals(ImageCategory::class, $this->obj->categoryType());
    }
}
