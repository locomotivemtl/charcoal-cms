<?php

namespace Charcoal\Cms\Tests;

// From 'charcoal-cms'
use Charcoal\Cms\FaqCategory;
use Charcoal\Cms\Faq;
use Charcoal\Tests\AbstractTestCase;

/**
 *
 */
class FaqCategoryTest extends AbstractTestCase
{
    use \Charcoal\Tests\Cms\ContainerIntegrationTrait;

    /**
     * Tested Class.
     *
     * @var FaqCategory
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

        $this->obj = new FaqCategory([
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
        $this->assertEquals(Faq::class, $this->obj->itemType());
    }
}
