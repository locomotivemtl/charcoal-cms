<?php

namespace Charcoal\Cms\Tests;

use Psr\Log\NullLogger;
use Cache\Adapter\Void\VoidCachePool;

use Charcoal\Model\Service\MetadataLoader;

use Charcoal\Cms\EventCategory;
use Charcoal\Cms\Event;

class EventCategoryTest extends \PHPUnit_Framework_TestCase
{

    public $obj;

    public function setUp()
    {
        $metadataLoader = new MetadataLoader([
            'logger' => new NullLogger(),
            'base_path' => __DIR__,
            'paths' => ['metadata'],
            'cache'  => new VoidCachePool()
        ]);

        $this->obj = new EventCategory([
            'logger'=> new NullLogger(),
            'metadata_loader' => $metadataLoader
        ]);
    }

    public function testItemType()
    {
        $this->assertEquals(Event::class, $this->obj->itemType());
    }
}
