<?php

namespace Charcoal\Cms\Tests;

// From 'charcoal-cms'
use Charcoal\Cms\FaqCategory;
use Charcoal\Cms\Faq;
use Charcoal\Tests\AbstractTestCase;
use Charcoal\Tests\Cms\ContainerIntegrationTrait;

/**
 *
 */
class FaqCategoryTest extends AbstractTestCase
{
    use ContainerIntegrationTrait;

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
        $dependencies = $this->getModelDependenciesWithContainer();

        $this->obj = new FaqCategory($dependencies);
    }

    /**
     * @return void
     */
    public function testItemType()
    {
        $this->assertEquals(Faq::class, $this->obj->itemType());
    }
}
