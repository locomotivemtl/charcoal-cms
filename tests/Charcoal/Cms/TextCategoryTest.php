<?php

namespace Charcoal\Cms\Tests;

// From 'charcoal-cms'
use Charcoal\Cms\TextCategory;
use Charcoal\Cms\Text;

/**
 *
 */
class TextCategoryTest extends \PHPUnit_Framework_TestCase
{
    use \Charcoal\Tests\Cms\ContainerIntegrationTrait;

    /**
     * Tested Class.
     *
     * @var TextCategory
     */
    private $obj;

    /**
     * Set up the test.
     */
    public function setUp()
    {
        $container = $this->getContainer();

        $this->obj = new TextCategory([
            'container'        => $container,
            'logger'           => $container['logger'],
            'metadata_loader'  => $container['metadata/loader'],
            'property_factory' => $container['property/factory']
        ]);
    }

    public function testItemType()
    {
        $this->assertEquals(Text::class, $this->obj->itemType());
    }
}
