<?php

namespace Charcoal\Tests\Property;

use RuntimeException;

// From 'charcoal-property'
use Charcoal\Property\Structure\StructureModel;

// From 'charcoal-cms'
use Charcoal\Cms\TemplateableTrait;
use Charcoal\Tests\AbstractTestCase;
use Charcoal\Tests\Cms\ContainerIntegrationTrait;
use Charcoal\Tests\Cms\Mock\TemplateableModel;

/**
 * Template Property Test
 */
class TemplateableTraitTest extends AbstractTestCase
{
    use ContainerIntegrationTrait;

    /**
     * Tested Class.
     *
     * @var TemplateableTrait
     */
    public $obj;

    /**
     * Set up the test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->dropTable();

        $container = $this->getContainer();
        $provider  = $this->getContainerProvider();

        $provider->withTemplatesConfig($container);

        $dependencies = $this->getModelDependencies();
        $dependencies['metadata'] = $this->getModelMetadata();

        $this->obj = new TemplateableModel($dependencies);

        $src = $this->obj->source();
        $src->setTable('charcoal_models');

        if ($src->tableExists() === false) {
            $src->createTable();
        }
    }

    /**
     * Tear down the test.
     *
     * Drop any existing SQL table.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->dropTable();
    }

    /**
     * Drop the SQL table.
     *
     * @return void
     */
    private function dropTable()
    {
        $container = $this->getContainer();

        $container['database']->query('DROP TABLE IF EXISTS `charcoal_models`;');
    }

    /**
     * Retrieve the model's mock metadata.
     *
     * @return array
     */
    public function getModelMetadata()
    {
        return [
            'properties' => [
                'id' => [
                    'type' => 'id',
                ],
                'name' => [
                    'type' => 'string',
                ],
                'controller_ident' => [
                    'type'    => 'string',
                    'choices' => [
                        'foo' => [
                            'controller' => 'templateable/foo',
                        ],
                        'baz' => [
                            'template' => 'templateable/baz',
                        ],
                        'qux' => [
                            'class' => 'templateable/qux',
                        ],
                    ],
                ],
                'template_ident' => [
                    'type' => 'template',
                ],
                'template_options' => [
                    'type' => 'template-options',
                ],
            ],
            'key' => 'id',
            'sources' => [
                'default' => [
                    'table' => 'charcoal_models',
                ],
            ],
            'default_source' => 'default',
        ];
    }

    /**
     * @return void
     */
    public function testMissingPropertyDependency()
    {
        $this->expectException(RuntimeException::class);
        $obj = new TemplateableModel($this->getModelDependencies());
        $obj->templateOptionsStructure();
    }

    /**
     * @return void
     */
    public function testMissingInterfaceDependency()
    {
        $this->expectException(RuntimeException::class);
        $obj = $this->getMockForTrait(TemplateableTrait::class);
        $obj->templateOptionsStructure();
    }

    /**
     * @return void
     */
    public function testTemplateIdent()
    {
        $this->assertNull($this->obj->templateIdent());

        $this->obj->setTemplateIdent('foobar');
        $this->assertEquals('foobar', $this->obj->templateIdent());
    }

    /**
     * @return void
     */
    public function testControllerIdent()
    {
        $this->assertNull($this->obj->controllerIdent());

        $this->obj->setControllerIdent('foobar');
        $this->assertEquals('foobar', $this->obj->controllerIdent());
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
    public function testSavingTemplateOptions()
    {
        $obj = $this->obj;
        $obj->setTemplateIdent('foo');
        $obj->setTemplateOptions([ 'foo' => 'Huxley' ]);

        $result = $obj->save();
        $this->assertEquals(1, $result);

        # $result = $obj->update([ 'name' ]);
        # $this->assertTrue($result);

        $obj->setTemplateIdent(null);
        $result = $obj->update([ 'template_options' ]);
        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function testTemplateOptionsStructure()
    {
        $templateData         = [ 'foo' => 'Huxley' ];
        $templateMetadataFile = realpath('tests/Charcoal/Cms/Fixture/metadata/templateable/foo-template.json');
        $templateMetadata     = json_decode(file_get_contents($templateMetadataFile), true);

        $obj = $this->obj;
        $obj->setTemplateIdent('foo');
        $obj->setTemplateOptions($templateData);

        $struct = $obj->templateOptionsStructure();

        $this->assertInstanceOf(StructureModel::class, $struct);
        $this->assertEquals($templateData, $struct->data());
        $this->assertEquals($templateMetadata, $struct->metadata()->data());
    }
}
