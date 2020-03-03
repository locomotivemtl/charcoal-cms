<?php

namespace Charcoal\Tests\Property;

use InvalidArgumentException;

// From 'charcoal-cms'
use Charcoal\Property\TemplateProperty;
use Charcoal\Property\TemplateOptionsProperty;
use Charcoal\Tests\AbstractTestCase;
use Charcoal\Tests\Cms\ContainerIntegrationTrait;

/**
 * Template Property Test
 */
class TemplateOptionsPropertyTest extends AbstractTestCase
{
    use ContainerIntegrationTrait;

    /**
     * Tested Class.
     *
     * @var TemplateOptionsProperty
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

        $container['config']['templates'] = [
            [
                'value'  => 'foo',
                'label'  => [
                    'en' => 'Foofoo',
                    'fr' => 'Oofoof',
                ],
                'controller' => 'charcoal/tests/cms/mock/generic',
            ]
        ];

        $dependencies = $this->getPropertyDependenciesWithContainer();

        $this->obj = new TemplateOptionsProperty($dependencies);
    }

    /**
     * @return void
     */
    public function testType()
    {
        $this->assertEquals('template-options', $this->obj->type());
    }

    /**
     * @return void
     */
    public function testAddStructureInterface()
    {
        $container = $this->getContainer();
        $property  = $container['property/factory']->create(TemplateProperty::class);

        $property->setVal('foo');
        $return = $this->obj->addStructureInterface($property);
        $this->assertSame($return, $this->obj);

        $interfaces = $this->obj['structureInterfaces'];
        $this->assertEquals([ 'charcoal/tests/cms/mock/generic' ], $interfaces);
    }

    /**
     * @return void
     */
    public function testAddStructureInterfaceException()
    {
        $container = $this->getContainer();
        $property  = $container['property/factory']->create(TemplateProperty::class);

        $this->expectException(InvalidArgumentException::class);
        $this->obj->addStructureInterface($property);
    }
}
