<?php

namespace Charcoal\Tests\Property;

use InvalidArgumentException;

// From 'charcoal-property'
use Charcoal\Property\TemplateProperty;
use Charcoal\Property\TemplateOptionsProperty;

/**
 * Template Property Test
 */
class TemplateOptionsPropertyTest extends \PHPUnit_Framework_TestCase
{
    use \Charcoal\Tests\Cms\ContainerIntegrationTrait;

    /**
     * Tested Class.
     *
     * @var TemplateOptionsProperty
     */
    public $obj;

    /**
     * Set up the test.
     */
    public function setUp()
    {
        $container = $this->getContainer();

        $this->getContainerProvider()->registerMultilingualTranslator($container);

        $container['config']['templates'] = [
            [
                'value'  => 'foo',
                'label'  => [
                    'en' => 'Foofoo',
                    'fr' => 'Oofoof'
                ],
                'controller' => 'charcoal/tests/cms/mocks/generic'
            ]
        ];

        $this->obj = new TemplateOptionsProperty([
            'container'  => $container,
            'database'   => $container['database'],
            'logger'     => $container['logger'],
            'translator' => $container['translator']
        ]);
    }

    public function testType()
    {
        $this->assertEquals('template-options', $this->obj->type());
    }

    public function testAddStructureInterface()
    {
        $container = $this->getContainer();
        $property  = $container['property/factory']->create(TemplateProperty::class);

        $property->setVal('foo');
        $return = $this->obj->addStructureInterface($property);
        $this->assertSame($return, $this->obj);

        $interfaces = $this->obj->structureInterfaces();
        $this->assertEquals([ 'charcoal/tests/cms/mocks/generic' ], $interfaces);
    }

    public function testAddStructureInterfaceException()
    {
        $container = $this->getContainer();
        $property  = $container['property/factory']->create(TemplateProperty::class);

        $this->setExpectedException(InvalidArgumentException::class);
        $this->obj->addStructureInterface($property);
    }
}
