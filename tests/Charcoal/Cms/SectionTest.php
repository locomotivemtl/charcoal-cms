<?php

namespace Charcoal\Cms\Tests;

// From 'charcoal-object'
use Charcoal\Object\ObjectRoute;

// From 'charcoal-cms'
use Charcoal\Cms\Section;
use Charcoal\Tests\AbstractTestCase;
use Charcoal\Tests\Cms\ContainerIntegrationTrait;

/**
 *
 */
class SectionTest extends AbstractTestCase
{
    use ContainerIntegrationTrait;

    /**
     * Tested Class.
     *
     * @var Section
     */
    private $obj;

    /**
     * Set up the test.
     *
     * @return void
     */
    public function setUp()
    {
        $container = $this->getContainer();

        $route = $container['model/factory']->get(ObjectRoute::class);
        if ($route->source()->tableExists() === false) {
            $route->source()->createTable();
        }

        $dependencies = $this->getModelDependenciesWithContainer();

        $this->obj = new Section($dependencies);
    }

    /**
     * @return void
     */
    public function testSetData()
    {
        $obj = $this->obj;
        $ret = $obj->setData([
            'section_type'     => Section::TYPE_EXTERNAL,
            'title'            => 'foo',
            'subtitle'         => 'bar',
            'content'          => 'baz',
            'image'            => 'foobar.png',
            'template_ident'   => 'foobar',
            'template_options' => [
                'x' => 'y',
            ],
        ]);
        $this->assertSame($ret, $obj);

        $this->assertEquals(Section::TYPE_EXTERNAL, $obj->sectionType());
        $this->assertEquals('foo', (string)$obj->title());
        $this->assertEquals('bar', (string)$obj->subtitle());
        $this->assertEquals('baz', (string)$obj->content());
        $this->assertEquals('foobar.png', (string)$obj->image());
        $this->assertEquals('foobar', $obj->templateIdent());
        $this->assertEquals([ 'x' => 'y' ], $obj->templateOptions());
    }

    /**
     * @return void
     */
    public function testSetSectionType()
    {
        $ret = $this->obj->setSectionType(Section::TYPE_EMPTY);
        $this->assertSame($ret, $this->obj);
        $this->assertEquals(Section::TYPE_EMPTY, $this->obj->sectionType());

        $this->expectException('\InvalidArgumentException');
        $this->obj->setSectionType(false);
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
    public function testMetaTitleDefaultsToTitle()
    {
        $this->assertEquals('', (string)$this->obj->metaTitle());

        $this->obj->setTitle('Foo Bar');
        $this->obj->generateDefaultMetaTags();
        $this->assertEquals('Foo Bar', (string)$this->obj->metaTitle());

        $this->obj->setMetaTitle('Barfoo');
        $this->assertEquals('Barfoo', (string)$this->obj->metaTitle());
    }

    /**
     * @return void
     */
    public function testMetaDescriptionDefaultsToDescription()
    {
        $this->assertEquals('', (string)$this->obj->metaDescription());

        $this->obj->setContent('Foo Bar');
        $this->obj->generateDefaultMetaTags();
        $this->assertEquals('Foo Bar', (string)$this->obj->metaDescription());

        $this->obj->setMetaDescription('Barfoo');
        $this->assertEquals('Barfoo', (string)$this->obj->metaDescription());
    }

    /**
     * @return void
     */
    public function testMetaImageDefaultsToImage()
    {
        $this->assertEquals('', (string)$this->obj->metaImage());

        $this->obj->setImage('Foo.png');
        $this->obj->generateDefaultMetaTags();
        $this->assertEquals('Foo.png', (string)$this->obj->metaImage());

        $this->obj->setMetaImage('Bar.jpg');
        $this->assertEquals('Bar.jpg', (string)$this->obj->metaImage());
    }

    /**
     * @return void
     */
    public function testSaveGeneratesSlug()
    {
        $this->assertEquals('', $this->obj['slug']);
        $this->obj->setData([
            'title' => 'foo',
        ]);
        $this->obj->save();

        $this->assertEquals('en/foo', (string)$this->obj['slug']);
    }

    /**
     * @return void
     */
    public function testUpdateGeneratesSlug()
    {
        $this->assertEquals('', $this->obj['slug']);
        $this->obj->setData([
            'title' => 'foo',
        ]);
        $this->obj->update();

        $this->assertEquals('en/foo', (string)$this->obj['slug']);
    }
}
