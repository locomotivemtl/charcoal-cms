<?php

namespace Charcoal\Cms\Tests;

// From 'charcoal-cms'
use Charcoal\Cms\EventCategory;
use Charcoal\Cms\Event;
use Charcoal\Tests\AbstractTestCase;
use Charcoal\Tests\Cms\ContainerIntegrationTrait;

/**
 *
 */
class EventCategoryTest extends AbstractTestCase
{
    use ContainerIntegrationTrait;

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
        $dependencies = $this->getModelDependenciesWithContainer();

        $this->obj = new EventCategory($dependencies);
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
        $this->obj->setName([ 'fr' => 'Titre' ]);
        $this->assertFalse($this->obj->validate());
        $this->obj->setName([ 'fr' => 'Titre', 'en' => 'Title' ]);
        $this->assertTrue($this->obj->validate());
    }
}
