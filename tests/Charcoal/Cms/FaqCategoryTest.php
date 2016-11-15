<?php

namespace Charcoal\Cms\Tests;

use \Psr\Log\NullLogger;
use \Cache\Adapter\Void\VoidCachePool;

use \Charcoal\Model\Service\MetadataLoader;

use \Charcoal\Cms\FaqCategory;

class FaqCategoryTest extends \PHPUnit_Framework_TestCase
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

        $this->obj = new FaqCategory([
            'logger'=> new NullLogger(),
            'metadata_loader' => $metadataLoader
        ]);
    }

    public function testItemType()
    {
        $this->assertEquals('charcoal/cms/faq', $this->obj->itemType());
    }
}
