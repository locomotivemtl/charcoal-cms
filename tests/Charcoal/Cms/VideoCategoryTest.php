<?php

namespace Charcoal\Cms\Tests;

use \Psr\Log\NullLogger;
use \Cache\Adapter\Void\VoidCachePool;

use \Charcoal\Model\Service\MetadataLoader;

use \Charcoal\Cms\VideoCategory;

class VideoCategoryTest extends \PHPUnit_Framework_TestCase
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

        $this->obj = new VideoCategory([
            'logger'=> new NullLogger(),
            'metadata_loader' => $metadataLoader
        ]);
    }

    public function testItemType()
    {
        $this->assertEquals('charcoal/cms/video', $this->obj->itemType());
    }
}
