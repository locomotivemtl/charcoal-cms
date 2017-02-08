<?php

namespace Charcoal\Cms\Tests;

// From 'charcoal-cms'
use Charcoal\Cms\FaqCategory;
use Charcoal\Cms\Faq;

/**
 *
 */
class FaqCategoryTest extends \PHPUnit_Framework_TestCase
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
     */
    public function setUp()
    {
        $container = $this->getContainer();

        $this->obj = new FaqCategory([
            'container' => $container,
            'logger'    => $container['logger']
        ]);
    }

    public function testItemType()
    {
        $this->assertEquals(Faq::class, $this->obj->itemType());
    }
}
