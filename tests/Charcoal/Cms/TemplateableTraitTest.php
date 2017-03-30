<?php

namespace Charcoal\Tests\Property;

use InvalidArgumentException;

// From 'charcoal-property'
use Charcoal\Cms\TemplateableTrait;

/**
 * Template Property Test
 */
class TemplateableTraitTest extends \PHPUnit_Framework_TestCase
{
    use \Charcoal\Tests\Cms\ContainerIntegrationTrait;

    /**
     * Tested Class.
     *
     * @var TemplateableTrait
     */
    public $obj;

    /**
     * Set up the test.
     */
    public function setUp()
    {
        $this->obj = $this->getMockForTrait(TemplateableTrait::class);
    }

    public function testTemplateIdent()
    {
        $this->assertNull($this->obj->templateIdent());

        $this->obj->setTemplateIdent('foobar');
        $this->assertEquals('foobar', $this->obj->templateIdent());
    }

    public function testControllerIdent()
    {
        $this->assertNull($this->obj->controllerIdent());

        $this->obj->setControllerIdent('foobar');
        $this->assertEquals('foobar', $this->obj->controllerIdent());
    }

    public function testTemplateOptions()
    {
        $this->assertInternalType('array', $this->obj->templateOptions());
        $this->assertEmpty($this->obj->templateOptions());

        $this->obj->setTemplateOptions(false);
        $this->assertFalse($this->obj->templateOptions());

        $this->obj->setTemplateOptions(42);
        $this->assertNull($this->obj->templateOptions());

        $this->obj->setTemplateOptions('foobar');
        $this->assertNull($this->obj->templateOptions());

        $this->obj->setTemplateOptions('{"foo":"bar"}');
        $this->assertEquals([ 'foo' => 'bar' ], $this->obj->templateOptions());

        $this->obj->setTemplateOptions([ 'foo' => 'bar' ]);
        $this->assertEquals([ 'foo' => 'bar' ], $this->obj->templateOptions());

        $obj = new \stdClass();
        $this->obj->setTemplateOptions($obj);
        $this->assertEquals($obj, $this->obj->templateOptions());
    }
}
