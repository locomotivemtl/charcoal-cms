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

use \Charcoal\Cms\Route\EventRoute;

use \Charcoal\Tests\Cms\ContainerProvider;

/**
 *
 */
class EventRouteTest extends \PHPUnit_Framework_TestCase
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
        $this->obj = new EventRoute([
            'config' => [],
            'path' => 'en/events/foo'
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

        // Create the event table
        $event = $container['model/factory']->create('charcoal/cms/event');
        $event->source()->createTable();

        $ret = $this->obj->pathResolvable($container);
        $this->assertFalse($ret);

        // Now try with a resolvable event.
        $event->setData([
            'id' => 1,
            'title'=>'Foo',
            'slug'=>new TranslationString('foo'),
            'template_ident'=>'bar'
        ]);
        $id = $event->save();

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

        // Create the event table
        $event = $container['model/factory']->create('charcoal/cms/event');
        $event->source()->createTable();

        $obj = $this->obj;
        $ret = $obj($container, $request, $response);
        $this->assertEquals(404, $ret->getStatusCode());
    }
}
