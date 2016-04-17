<?php

namespace Charcoal\Cms\Tests;

use \Charcoal\Cms\Section;

class SectionTest extends \PHPUnit_Framework_TestCase
{
    public $obj;

    public function setUp()
    {
        $this->obj = new Section();
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

    public function testSetTitle()
    {
        $this->assertEquals('', (string)$this->obj->title());
        $ret = $this->obj->setTitle('Foo bar');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Foo bar', (string)$this->obj->title());
    }

    public function testSetSubtitle()
    {
        $this->assertEquals('', (string)$this->obj->subtitle());
        $ret = $this->obj->setSubtitle('Bar foo');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Bar foo', (string)$this->obj->subtitle());
    }

    public function testSetContent()
    {
        $this->assertEquals('', (string)$this->obj->content());
        $ret = $this->obj->setContent('Bar foo');
        $this->assertSame($ret, $this->obj);
        $this->assertEquals('Bar foo', (string)$this->obj->content());
    }

    public function testMetaTitleDefaultsToTitle()
    {
        $this->assertEquals('', (string)$this->obj->metaTitle());

        $this->obj->setTitle('Foo Bar');
        $this->assertSame($this->obj->title(), $this->obj->metaTitle());
        $this->assertEquals('Foo Bar', (string)$this->obj->metaTitle());

        $this->obj->setMetaTitle('Barfoo');
        $this->assertEquals('Barfoo', (string)$this->obj->metaTitle());
    }

    public function testMetaDescriptionDefaultsToDescription()
    {
        $this->assertEquals('', (string)$this->obj->metaDescription());

        $this->obj->setContent('Foo Bar');
        $this->assertSame($this->obj->content(), $this->obj->metaDescription());
        $this->assertEquals('Foo Bar', (string)$this->obj->metaDescription());

        $this->obj->setMetaDescription('Barfoo');
        $this->assertEquals('Barfoo', (string)$this->obj->metaDescription());
    }

    public function testMetaImageDefaultsToImage()
    {
        $this->assertEquals('', (string)$this->obj->metaImage());

        $this->obj->setImage('Foo.png');
        $this->assertSame($this->obj->image(), $this->obj->metaImage());
        $this->assertEquals('Foo.png', (string)$this->obj->metaImage());

        $this->obj->setMetaImage('Bar.jpg');
        $this->assertEquals('Bar.jpg', (string)$this->obj->metaImage());
    }
}
