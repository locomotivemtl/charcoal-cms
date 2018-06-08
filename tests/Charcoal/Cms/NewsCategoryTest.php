<?php

namespace Charcoal\Cms\Tests;

// From 'charcoal-cms'
use Charcoal\Cms\NewsCategory;
use Charcoal\Cms\News;
use Charcoal\Tests\AbstractTestCase;

/**
 *
 */
class NewsCategoryTest extends AbstractTestCase
{
    use \Charcoal\Tests\Cms\ContainerIntegrationTrait;

    /**
     * Tested Class.
     *
     * @var NewsCategory
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

        $this->obj = new NewsCategory([
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
        $this->assertEquals(News::class, $this->obj->itemType());
    }
}
