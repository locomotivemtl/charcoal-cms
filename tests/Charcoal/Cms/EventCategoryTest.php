<?php

namespace Charcoal\Cms\Tests;

// From 'charcoal-cms'
use Charcoal\Cms\EventCategory;
use Charcoal\Cms\Event;

/**
 *
 */
class EventCategoryTest extends \PHPUnit_Framework_TestCase
{
    use \Charcoal\Tests\Cms\ContainerIntegrationTrait;

    /**
     * Tested Class.
     *
     * @var EventCategory
     */
    private $obj;

    /**
     * Set up the test.
     */
    public function setUp()
    {
        $container = $this->getContainer();

        $this->obj = new EventCategory([
            'container' => $container,
            'logger'    => $container['logger']
        ]);
    }

    public function testItemType()
    {
        $this->assertEquals(Event::class, $this->obj->itemType());
    }
}
