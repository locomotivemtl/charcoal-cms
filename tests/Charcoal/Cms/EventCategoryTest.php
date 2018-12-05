<?php

namespace Charcoal\Cms\Tests;

// From 'charcoal-cms'
use Charcoal\Cms\EventCategory;
use Charcoal\Cms\Event;
use Charcoal\Tests\AbstractTestCase;

/**
 *
 */
class EventCategoryTest extends AbstractTestCase
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
     *
     * @return void
     */
    public function setUp()
    {
        $container = $this->getContainer();

        $this->obj = new EventCategory([
            'container'        => $container,
            'logger'           => $container['logger'],
            'metadata_loader'  => $container['metadata/loader'],
            'property_factory' => $container['property/factory']
        ]);
    }

    /**
     * @return void
     */
    public function testItemType()
    {
        $this->assertEquals(Event::class, $this->obj->itemType());
    }

    /**
     * @return void
     */
    public function testValidate()
    {
        $this->assertFalse($this->obj->validate());
        $this->obj->setName(['fr'=>'Titre']);
        $this->assertFalse($this->obj->validate());
        $this->obj->setName(['fr'=>'Titre', 'en'=>'Title']);
        $this->assertTrue($this->obj->validate());
    }
}
