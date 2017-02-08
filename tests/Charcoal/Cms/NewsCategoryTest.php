<?php

namespace Charcoal\Cms\Tests;

// From 'charcoal-cms'
use Charcoal\Cms\NewsCategory;
use Charcoal\Cms\News;

/**
 *
 */
class NewsCategoryTest extends \PHPUnit_Framework_TestCase
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
     */
    public function setUp()
    {
        $container = $this->getContainer();

        $this->obj = new NewsCategory([
            'container' => $container,
            'logger'    => $container['logger']
        ]);
    }

    public function testItemType()
    {
        $this->assertEquals(News::class, $this->obj->itemType());
    }
}
