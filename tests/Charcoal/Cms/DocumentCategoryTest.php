<?php

namespace Charcoal\Cms\Tests;

use PHPUnit_Framework_TestCase;

use Psr\Log\NullLogger;
use Cache\Adapter\Void\VoidCachePool;

use Charcoal\Model\Service\MetadataLoader;

use Charcoal\Cms\DocumentCategory;
use Charcoal\Cms\Document;

class DocumentCategoryTest extends PHPUnit_Framework_TestCase
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

        $this->obj = new DocumentCategory([
            'logger'=> new NullLogger(),
            'metadata_loader' => $metadataLoader
        ]);
    }

    public function testItemType()
    {
        $this->assertEquals(Document::class, $this->obj->itemType());
    }
}
