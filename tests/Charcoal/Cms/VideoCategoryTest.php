<?php

namespace Charcoal\Cms\Tests;

// From 'charcoal-cms'
use Charcoal\Cms\VideoCategory;
use Charcoal\Cms\Video;

/**
 *
 */
class VideoCategoryTest extends \PHPUnit_Framework_TestCase
{
    use \Charcoal\Tests\Cms\ContainerIntegrationTrait;

    /**
     * Tested Class.
     *
     * @var VideoCategory
     */
    private $obj;

    /**
     * Set up the test.
     */
    public function setUp()
    {
        $container = $this->getContainer();

        $this->obj = new VideoCategory([
            'container' => $container,
            'logger'    => $container['logger']
        ]);
    }

    public function testItemType()
    {
        $this->assertEquals(Video::class, $this->obj->itemType());
    }
}
