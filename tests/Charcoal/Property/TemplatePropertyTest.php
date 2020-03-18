<?php

namespace Charcoal\Tests\Property;

use PDO;
use ReflectionClass;
use InvalidArgumentException;

// From 'charcoal-cms'
use Charcoal\Property\TemplateProperty;
use Charcoal\Tests\AbstractTestCase;
use Charcoal\Tests\Cms\ContainerIntegrationTrait;

/**
 * Template Property Test
 */
class TemplatePropertyTest extends AbstractTestCase
{
    use ContainerIntegrationTrait;

    /**
     * Tested Class.
     *
     * @var TemplateProperty
     */
    public $obj;

    /**
     * Set up the test.
     *
     * @return void
     */
    public function setUp()
    {
        $container = $this->getContainer();
        $provider  = $this->getContainerProvider();

        $provider->withMultilingualConfig($container);
        $provider->withTemplatesConfig($container);

        $dependencies = $this->getPropertyDependenciesWithContainer();

        $this->obj = new TemplateProperty($dependencies);
    }

    /**
     * @return void
     */
    public function testType()
    {
        $this->assertEquals('template', $this->obj->type());
    }

    /**
     * @return void
     */
    public function testSqlExtra()
    {
        $this->assertEquals('', $this->obj->sqlExtra());
    }

    /**
     * @return void
     */
    public function testSqlType()
    {
        $this->obj->setMultiple(false);
        $this->assertEquals('VARCHAR(255)', $this->obj->sqlType());

        $this->obj->setMultiple(true);
        $this->assertEquals('TEXT', $this->obj->sqlType());
    }

    /**
     * @return void
     */
    public function testSqlPdoType()
    {
        $this->assertEquals(PDO::PARAM_STR, $this->obj->sqlPdoType());
    }

    /**
     * @return void
     */
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
                'label' => 'Bazbaz',
            ],
        ]);
        $this->obj->setChoices([
            'zyx' => [
                'template' => 'templateable/zyx',
            ],
        ]);
        $this->assertArrayHasKey('zyx', $this->obj->choices());
    }

    /**
     * @return void
     */
    public function testChoicesInvalidKey()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->obj->addChoice(3, 'boo');
    }

    /**
     * @return void
     */
    public function testChoicesInvalidString()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->obj->addChoice('boo', 'boo');
    }

    /**
     * @return void
     */
    public function testChoicesInvalidBoolean()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->obj->addChoice('boo', true);
    }

    /**
     * @return void
     */
    public function testChoicesInvalidArray()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->obj->addChoice('boo', [ 'foo' => 'boo' ]);
    }

    /**
     * @return void
     */
    public function testChoicesInvalidType()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->obj->addChoice('xyz', null);
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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
