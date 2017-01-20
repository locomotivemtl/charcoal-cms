<?php

namespace Charcoal\Cms\Tests;

use PHPUnit_Framework_TestCase;

use Psr\Log\NullLogger;
use Cache\Adapter\Void\VoidCachePool;

use Charcoal\Model\Service\MetadataLoader;

use Charcoal\Cms\Text;
use Charcoal\Cms\TextCategory;

/**
 *
 */
class TextTest extends PHPUnit_Framework_TestCase
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

        $this->obj = new Text([
            'logger'=> new NullLogger(),
            'metadata_loader' => $metadataLoader
        ]);
    }

    public function testSetData()
    {
        $ret = $this->obj->setData([
            'title'=>'Example title',
            'subtitle'=>'Subtitle',
            'content'=>'foobar'
        ]);

        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Example title', (string)$this->obj->title());
        $this->assertEquals('Subtitle', (string)$this->obj->subtitle());
        $this->assertEquals('foobar', (string)$this->obj->content());
    }

    public function testCategoryType()
    {
        $this->assertEquals(TextCategory::class, $this->obj->categoryType());
    }
}
