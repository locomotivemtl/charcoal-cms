<?php

namespace Charcoal\Cms\Tests;

// From 'charcoal-cms'
use Charcoal\Cms\DocumentCategory;
use Charcoal\Cms\Document;

/**
 *
 */
class DocumentCategoryTest extends \PHPUnit_Framework_TestCase
{
    use \Charcoal\Tests\Cms\ContainerIntegrationTrait;

    /**
     * Tested Class.
     *
     * @var DocumentCategory
     */
    private $obj;

    /**
     * Set up the test.
     */
    public function setUp()
    {
        $container = $this->getContainer();

        $this->obj = new DocumentCategory([
            'container' => $container,
            'logger'    => $container['logger']
        ]);
    }

    public function testItemType()
    {
        $this->assertEquals(Document::class, $this->obj->itemType());
    }
}
