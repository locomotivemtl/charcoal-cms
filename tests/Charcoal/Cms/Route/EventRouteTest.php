<?php

namespace Charcoal\Tests\Cms\Route;

// From PSR-7
use Psr\Http\Message\RequestInterface;

// From Slim
use Slim\Http\Response;

// From 'charcoal-object'
use Charcoal\Object\ObjectRoute;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

// From 'charcoal-cms'
use Charcoal\Cms\Event;
use Charcoal\Cms\Route\EventRoute;
use Charcoal\Tests\AbstractTestCase;

/**
 *
 */
class EventRouteTest extends AbstractTestCase
{
    use \Charcoal\Tests\Cms\ContainerIntegrationTrait;

    /**
     * Tested Class.
     *
     * @var EventRoute
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
        $provider  = $this->getContainerProvider();

        $provider->registerTemplateFactory($container);

        $route = $container['model/factory']->get(ObjectRoute::class);
        if ($route->source()->tableExists() === false) {
            $route->source()->createTable();
        }

        $this->obj = new EventRoute([
            'config' => [],
            'path'   => '/en/events/foo',
        ]);
    }

    /**
     * @return void
     */
    public function testPathResolvableThrowsException()
    {
        $container = $this->getContainer();

        $locales   = $container['locales/manager'];
        $locales->setCurrentLocale($locales->currentLocale());

        // Create the event table
        $event = $container['model/factory']->create(Event::class);
        $event->source()->createTable();

        $this->expectException(\PDOException::class);
        $ret = $this->obj->pathResolvable($container);
    }

    /**
     * @return void
     */
    public function testPathResolvable()
    {
        $container = $this->getContainer();

        $locales   = $container['locales/manager'];
        $locales->setCurrentLocale($locales->currentLocale());

        // Create the event table
        $event = $container['model/factory']->create(Event::class);
        $event->source()->createTable();

        // Now try with a resolvable event.
        $event->setData([
            'id'             => 1,
            'title'          => 'Foo',
            'slug'           => new Translation('foo', $locales),
            'template_ident' => 'bar',
        ]);
        $id = $event->save();

        $ret = $this->obj->pathResolvable($container);
        $this->assertTrue($ret);
    }

    /**
     * @return void
     */
    public function testInvokeThrowsException()
    {
        $container = $this->getContainer();
        $request   = $this->createMock(RequestInterface::class);
        $response  = new Response();

        // Create the event table
        $obj = $container['model/factory']->create(Event::class);
        $obj->source()->createTable();

        $route  = $this->obj;
        $this->expectException(\PDOException::class);
        $return = $route($container, $request, $response);
    }

    /**
     * @return void
     */
    /*public function testInvoke()
    {
        $container = $this->getContainer();
        $request   = $this->createMock(RequestInterface::class);
        $response  = new Response();

        // Create the event table
        $obj = $container['model/factory']->create(Event::class);
        $obj->source()->createTable();

        $route  = $this->obj;
        $return = $route($container, $request, $response);
        $this->assertEquals(404, $return->getStatusCode());
    }*/
}
