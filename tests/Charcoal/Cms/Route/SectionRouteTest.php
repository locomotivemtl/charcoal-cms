<?php

namespace Charcoal\Tests\Cms\Route;

use \PDO;

use \Psr\Log\NullLogger;
use \Cache\Adapter\Void\VoidCachePool;

use \Pimple\Container;

use \Charcoal\Factory\GenericFactory as Factory;

use \Charcoal\App\AppConfig;

use \Charcoal\Model\Service\MetadataLoader;
use \Charcoal\Source\DatabaseSource;

use \Charcoal\Translation\TranslationString;

use \Charcoal\Cms\Route\SectionRoute;

use \Charcoal\Tests\Cms\ContainerProvider;

/**
 *
 */
class SectionRouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventRoute
     */
    public $obj;

    /**
     *
     */
    public function setUp()
    {
        $this->obj = new SectionRoute([
            'config' => [],
            'path' => 'en/sections/foo'
        ]);
    }

    /**
     * @return Container
     * @see ContainerProvider
     */
    private function getContainer()
    {
        $provider = new ContainerProvider();
        $container = new Container();

        $provider->registerBaseServices($container);
        $provider->registerMetadataLoader($container);
        $provider->registerSourceFactory($container);
        $provider->registerPropertyFactory($container);
        $provider->registerModelFactory($container);
        $provider->registerModelCollectionLoader($container);

        return $container;
    }

    /**
     *
     */
    public function testPathResolvable()
    {
        $container = $this->getContainer();

        // Create the section table
        $section = $container['model/factory']->create('charcoal/cms/section');
        $section->source()->createTable();

        $ret = $this->obj->pathResolvable($container);
        $this->assertFalse($ret);

        // Now try with a resolvable section.
        $section->setData([
            'id' => 1,
            'title'=>'Foo',
            'slug'=>new TranslationString('foo'),
            'template_ident'=>'bar'
        ]);
        $id = $section->save();

        $ret = $this->obj->pathResolvable($container);
        //$this->assertTrue($ret);
    }

    /**
     *
     */
    public function testInvoke()
    {
        $container = $this->getContainer();
        $request = $this->getMock('\Psr\Http\Message\RequestInterface');
        $response = new \Slim\Http\Response();


        $container['template/factory'] = function (Container $container) {
            return new Factory([
                'resolver_options' => [
                    'suffix' => 'Template'
                ],
                'arguments' => [[
                    'container' => $container,
                    'logger' => $container['logger']
                ]]
            ]);
        };

        // Create the section table
        $section = $container['model/factory']->create('charcoal/cms/section');
        $section->source()->createTable();

        $obj = $this->obj;
        $ret = $obj($container, $request, $response);
        $this->assertEquals(404, $ret->getStatusCode());
    }
}