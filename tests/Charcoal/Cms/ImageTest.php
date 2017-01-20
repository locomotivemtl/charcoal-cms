<?php

namespace Charcoal\Cms\Tests;

use PHPUnit_Framework_TestCase;

use Psr\Log\NullLogger;
use Cache\Adapter\Void\VoidCachePool;

use Charcoal\Model\Service\MetadataLoader;

use Charcoal\Cms\Image;
use Charcoal\Cms\ImageCategory;

/**
 *
 */
class ImageTest extends PHPUnit_Framework_TestCase
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

        $this->obj = new Image([
            'logger'=> new NullLogger(),
            'metadata_loader' => $metadataLoader
        ]);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(Image::class, $this->obj);
    }

    public function testCategoryType()
    {
        $this->assertEquals(ImageCategory::class, $this->obj->categoryType());
    }
}
