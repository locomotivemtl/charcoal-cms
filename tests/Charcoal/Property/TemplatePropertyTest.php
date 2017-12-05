<?php

namespace Charcoal\Tests\Property;

use PDO;
use ReflectionClass;
use InvalidArgumentException;

// From 'charcoal-property'
use Charcoal\Property\TemplateProperty;

/**
 * Template Property Test
 */
class TemplatePropertyTest extends \PHPUnit_Framework_TestCase
{
    use \Charcoal\Tests\Cms\ContainerIntegrationTrait;

    /**
     * Tested Class.
     *
     * @var TemplateProperty
     */
    public $obj;

    /**
     * Set up the test.
     */
    public function setUp()
    {
        $container = $this->getContainer();
        $provider  = $this->getContainerProvider();

        $provider->registerMultilingualTranslator($container);
        $provider->registerTemplateDependencies($container);

        $this->obj = new TemplateProperty([
            'container'  => $container,
            'database'   => $container['database'],
            'logger'     => $container['logger'],
            'translator' => $container['translator']
        ]);
    }

    public function testType()
    {
        $this->assertEquals('template', $this->obj->type());
    }

    public function testSqlExtra()
    {
        $this->assertEquals('', $this->obj->sqlExtra());
    }

    public function testSqlType()
    {
        $this->obj->setMultiple(false);
        $this->assertEquals('VARCHAR(255)', $this->obj->sqlType());

        $this->obj->setMultiple(true);
        $this->assertEquals('TEXT', $this->obj->sqlType());
    }

    public function testSqlPdoType()
    {
        $this->assertEquals(PDO::PARAM_STR, $this->obj->sqlPdoType());
    }

    public function testChoices()
    {
        $container = $this->getContainer();
        $templates = $container['config']['templates'];

        $this->assertTrue($this->obj->hasChoices());

        $choices = $this->obj->choices();

        $this->assertEquals(array_column($templates, 'value'), array_keys($choices));

        $this->obj->addChoice('xyz', 'xyz');
        $this->obj->addChoices([
            'xyz' => 'xyz',
            'foo' => true,
            'baz' => [
                'label' => 'Bazbaz'
            ]
        ]);
        $this->obj->setChoices([
            'zyx' => [
                'template' => 'templateable/zyx'
            ]
        ]);
        $this->assertArrayHasKey('zyx', $this->obj->choices());
    }

    public function testChoicesInvalidKey()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->obj->addChoice(3, 'boo');
    }

    public function testChoicesInvalidString()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->obj->addChoice('boo', 'boo');
    }

    public function testChoicesInvalidBoolean()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->obj->addChoice('boo', true);
    }

    public function testChoicesInvalidArray()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->obj->addChoice('boo', [ 'foo' => 'boo' ]);
    }

    public function testChoicesInvalidType()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->obj->addChoice('xyz', null);
    }

    public function testDisplayVal()
    {
        $container  = $this->getContainer();
        $translator = $container['translator'];
        $templates  = $container['config']['templates'];

        $this->assertEquals('', $this->obj->displayVal(null));
        $this->assertEquals('', $this->obj->displayVal(''));

        $this->assertEquals('Foofoo', $this->obj->displayVal('foo'));
        $this->assertEquals('Oofoof', $this->obj->displayVal('foo', [ 'lang' => 'fr' ]));

        $val = $translator->translation('foo');
        /** Test translatable value with a unilingual property */
        $this->assertEquals('Foofoo', $this->obj->displayVal($val));

        /** Test translatable value with a multilingual property */
        $this->obj->setL10n(true);

        $this->assertEquals('', $this->obj->displayVal('foo'));
        $this->assertEquals('Oofoof', $this->obj->displayVal($val, [ 'lang' => 'fr' ]));
        $this->assertEquals('Foofoo', $this->obj->displayVal($val, [ 'lang' => 'de' ]));
        $this->assertEquals('Foofoo', $this->obj->displayVal($val));

        $this->obj->setL10n(false);
        $this->obj->setMultiple(true);

        $this->assertEquals('Foofoo, Bazbaz, Quxqux', $this->obj->displayVal([ 'foo', 'baz', 'qux' ]));
        $this->assertEquals('Foofoo, Bazbaz, Quxqux', $this->obj->displayVal('foo,baz,qux', [ 'lang' => 'es' ]));
        $this->assertEquals('Oofoof, Zabzab, Xuqxuq', $this->obj->displayVal('foo,baz,qux', [ 'lang' => 'fr' ]));
    }

    public function testToString()
    {
        $this->assertEquals('', (string)$this->obj);

        $this->obj->setVal('foo');
        $this->assertEquals('templateable/foo', (string)$this->obj);

        $this->obj->setVal('baz');
        $this->assertEquals('templateable/baz', (string)$this->obj);

        $this->obj->setVal('qux');
        $this->assertEquals('templateable/qux', (string)$this->obj);

        $this->obj->setVal('xyz');
        $this->assertEquals('', (string)$this->obj);
    }
}
