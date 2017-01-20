<?php

namespace Charcoal\Cms\Tests;

use PHPUnit_Framework_TestCase;

use Pimple\Container;

use Charcoal\Cms\Section;

use Charcoal\Tests\Cms\ContainerProvider;

/**
 *
 */
class SectionTest extends PHPUnit_Framework_TestCase
{
    public $obj;

    public function setUp()
    {
        $container = new Container();
        $provider = new ContainerProvider();

        $provider->registerBaseServices($container);
        $provider->registerMetadataLoader($container);
        $provider->registerModelFactory($container);
        $provider->registerSourceFactory($container);
        $provider->registerPropertyFactory($container);
        $provider->registerModelCollectionLoader($container);

        $this->obj = new Section([
             'container'         => $container,
            'logger'            => $container['logger'],
            'metadata_loader'   => $container['metadata/loader'],
            'property_factory'  => $container['property/factory'],
            'source_factory'    => $container['source/factory']
        ]);
    }

    public function testSetData()
    {
        $obj = $this->obj;
        $ret = $obj->setData([
            'section_type'  => Section::TYPE_EXTERNAL,
            'title'         => 'foo',
            'subtitle'      => 'bar',
            'content'       => 'baz',
            'image'         => 'foobar.png',
            'template_ident' => 'foobar',
            'template_options' => [
                'x'=>'y'
            ]
        ]);
        $this->assertSame($ret, $obj);

        $this->assertEquals(Section::TYPE_EXTERNAL, $obj->sectionType());
        $this->assertEquals('foo', (string)$obj->title());
        $this->assertEquals('bar', (string)$obj->subtitle());
        $this->assertEquals('baz', (string)$obj->content());
        $this->assertEquals('foobar.png', (string)$obj->image());
        $this->assertEquals('foobar', $obj->templateIdent());
        $this->assertEquals(['x'=>'y'], $obj->templateOptions());
    }

    public function testSetSectionType()
    {
        $ret = $this->obj->setSectionType(Section::TYPE_EMPTY);
        $this->assertSame($ret, $this->obj);
        $this->assertEquals(Section::TYPE_EMPTY, $this->obj->sectionType());

        $this->setExpectedException('\InvalidArgumentException');
        $this->obj->setSectionType(false);
    }

    public function testSetSectionTypeInvalidType()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->obj->setSectionType('FooBarFoo');
    }

    public function testSetTitle()
    {
        $this->assertEquals('', (string)$this->obj->title());
        $ret = $this->obj->setTitle('Foo bar');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Foo bar', (string)$this->obj->title());

        $this->obj['title'] = 'Bar';
        $this->assertEquals('Bar', (string)$this->obj->title());

        $this->obj->set('title', 'Hello');
        $this->assertEquals('Hello', (string)$this->obj['title']);
    }

    public function testSetSubtitle()
    {
        $this->assertEquals('', (string)$this->obj->subtitle());
        $ret = $this->obj->setSubtitle('Bar foo');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Bar foo', (string)$this->obj->subtitle());

        $this->obj['subtitle'] = 'Foobar';
        $this->assertEquals('Foobar', (string)$this->obj->subtitle());

        $this->obj->set('subtitle', 'foo');
        $this->assertEquals('foo', (string)$this->obj['subtitle']);
    }

    public function testSetContent()
    {
        $this->assertEquals('', (string)$this->obj->content());
        $ret = $this->obj->setContent('Bar foo');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Bar foo', (string)$this->obj->content());

        $this->obj['content'] = 'Foobar';
        $this->assertEquals('Foobar', (string)$this->obj->content());

        $this->obj->set('content', 'foo');
        $this->assertEquals('foo', (string)$this->obj['content']);
    }

    public function testSetImage()
    {
        $this->assertEquals('', (string)$this->obj->image());
        $ret = $this->obj->setImage('foo.png');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('foo.png', (string)$this->obj->image());

        $this->obj['image'] = 'bar.jpg';
        $this->assertEquals('bar.jpg', $this->obj->image());

        $this->obj->set('image', 'foo.webp');
        $this->assertEquals('foo.webp', $this->obj['image']);
    }

    public function testMetaTitleDefaultsToTitle()
    {
        $this->assertEquals('', (string)$this->obj->metaTitle());

        $this->obj->setTitle('Foo Bar');
        $this->obj->generateDefaultMetaTags();
        $this->assertEquals('Foo Bar', (string)$this->obj->metaTitle());

        $this->obj->setMetaTitle('Barfoo');
        $this->assertEquals('Barfoo', (string)$this->obj->metaTitle());
    }

    public function testMetaDescriptionDefaultsToDescription()
    {
        $this->assertEquals('', (string)$this->obj->metaDescription());

        $this->obj->setContent('Foo Bar');
        $this->obj->generateDefaultMetaTags();
        $this->assertEquals('Foo Bar', (string)$this->obj->metaDescription());

        $this->obj->setMetaDescription('Barfoo');
        $this->assertEquals('Barfoo', (string)$this->obj->metaDescription());
    }

    public function testMetaImageDefaultsToImage()
    {
        $this->assertEquals('', (string)$this->obj->metaImage());

        $this->obj->setImage('Foo.png');
        $this->obj->generateDefaultMetaTags();
        $this->assertEquals('Foo.png', (string)$this->obj->metaImage());

        $this->obj->setMetaImage('Bar.jpg');
        $this->assertEquals('Bar.jpg', (string)$this->obj->metaImage());
    }

    public function testSaveGeneratesSlug()
    {
        $this->assertEquals('', $this->obj->slug());
        $this->obj->setData([
            'title'=>'foo'
        ]);
        $this->obj->save();

        $this->assertEquals('en/foo', (string)$this->obj->slug());
    }

    public function testUpdateGeneratesSlug()
    {
        $this->assertEquals('', $this->obj->slug());
        $this->obj->setData([
            'title'=>'foo'
        ]);
        $this->obj->update();

        $this->assertEquals('en/foo', (string)$this->obj->slug());
    }

    public function testSaveGeneratesMetaTags()
    {
        $this->assertEquals('', (string)$this->obj->metaTitle());
        $this->assertEquals('', (string)$this->obj->metaDescription());
        $this->assertEquals('', (string)$this->obj->metaImage());

        $this->obj->setData([
            'title'=>'foo',
            'content'=>'<p>Foo bar</p>',
            'image' => 'x.jpg'
        ]);
        $this->obj->save();

        $this->assertEquals('foo', (string)$this->obj->metaTitle());
        $this->assertEquals('Foo bar', (string)$this->obj->metaDescription());
        $this->assertEquals('x.jpg', (string)$this->obj->metaImage());
    }

    public function testUpdateGeneratesMetaTags()
    {
        $this->assertEquals('', (string)$this->obj->metaTitle());
        $this->assertEquals('', (string)$this->obj->metaDescription());
        $this->assertEquals('', (string)$this->obj->metaImage());

        $this->obj->setData([
            'title'=>'foo',
            'content'=>'<p>Foo bar</p>',
            'image' => 'x.jpg'
        ]);
        $this->obj->update();

        $this->assertEquals('foo', (string)$this->obj->metaTitle());
        $this->assertEquals('Foo bar', (string)$this->obj->metaDescription());
        $this->assertEquals('x.jpg', (string)$this->obj->metaImage());
    }

    public function testLoadChildren()
    {
        $this->obj->source()->createTable();
        $this->assertEquals([], $this->obj->children()->objects());
    }
}
