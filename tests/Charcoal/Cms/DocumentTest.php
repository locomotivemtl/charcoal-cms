<?php

namespace Charcoal\Cms\Tests;

use PHPUnit_Framework_TestCase;

use Psr\Log\NullLogger;
use Cache\Adapter\Void\VoidCachePool;

use Charcoal\Model\Service\MetadataLoader;

use Charcoal\Cms\Document;
use Charcoal\Cms\DocumentCategory;

/**
 *
 */
class DocumentTest extends PHPUnit_Framework_TestCase
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

        $this->obj = new Document([
            'logger'=> new NullLogger(),
            'metadata_loader' => $metadataLoader
        ]);
    }

    public function testSetData()
    {
        $ret = $this->obj->setData([
            'name'=>'foo',
            'file'=>'foobar',
            'base_path'=>'baz',
            'base_url'=>'http://example.com/c'
        ]);
        $this->assertSame($ret, $this->obj);

        $this->assertEquals('foo', (string)$this->obj->name());
        $this->assertEquals('foobar', $this->obj->file());
        $this->assertEquals('baz/', $this->obj->basePath());
        $this->assertEquals('http://example.com/c/', $this->obj->baseUrl());
    }

    public function testDefaults()
    {
        $this->assertEquals('', $this->obj['name']);
        $this->assertEquals('', $this->obj['file']);
        $this->assertEquals('', $this->obj['base_path']);
        $this->assertEquals('/', $this->obj['base_url']);
    }

    public function testSetName()
    {
        $ret = $this->obj->setName('test');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('test', (string)$this->obj->name());

        $this->obj['name'] = 'Foo';;
        $this->assertEquals('Foo', (string)$this->obj->name());

        $this->obj->set('name', 'bar');
        $this->assertSame('bar', (string)$this->obj['name']);
    }

    public function testSetFile()
    {
        $ret = $this->obj->setFile('test');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('test', $this->obj->file());

        $this->obj['file'] = 'foo.bar';
        $this->assertEquals('foo.bar', $this->obj->file());

        $this->obj->set('file', 'bar.exe');
        $this->assertEquals('bar.exe', $this->obj['file']);
    }

    public function testSetBasePath()
    {
        $ret = $this->obj->setBasePath('foo');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('foo/', $this->obj->basePath());

        $this->obj['base_path'] = 'foo/bar/';
        $this->assertEquals('foo/bar/', $this->obj->basePath());

        $this->obj->set('base_path', 'Foo');
        $this->assertEquals('Foo/', $this->obj['base_path']);
    }

    public function testSetBaseUrl()
    {
        $ret = $this->obj->setBaseUrl('foo');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('foo/', $this->obj->baseUrl());

        $this->obj['base_url'] = 'foo/bar/';
        $this->assertEquals('foo/bar/', $this->obj->baseUrl());

        $this->obj->set('base_url', 'Foo');
        $this->assertEquals('Foo/', $this->obj['base_url']);
    }

    public function testPath()
    {
        $this->obj['base_path'] = 'foo';
        $this->obj['file'] = 'bar.doc';
        $this->assertEquals('foo/bar.doc', $this->obj->path());

        $this->obj['base_path'] = 'foo/bar';
        $this->obj['file'] = 'a/b/c/d.doc';
        $this->assertEquals('foo/bar/a/b/c/d.doc', $this->obj->path());
    }

    public function testUrl()
    {
        $this->obj['base_url'] = 'https://example.com';
        $this->obj['file'] = 'bar.doc';
        $this->assertEquals('https://example.com/bar.doc', $this->obj->url());

        $this->obj['base_url'] = 'https://example.com/foo/bar';
        $this->obj['file'] = 'a/b/c/d.doc';
        $this->assertEquals('https://example.com/foo/bar/a/b/c/d.doc', $this->obj->url());
    }

    public function testFilename()
    {
        $this->obj['file'] = 'a/b/c/d.doc';
        $this->assertEquals('d.doc', $this->obj->filename());
    }

    public function testCategoryType()
    {
        $this->assertEquals(DocumentCategory::class, $this->obj->categoryType());
    }
}
