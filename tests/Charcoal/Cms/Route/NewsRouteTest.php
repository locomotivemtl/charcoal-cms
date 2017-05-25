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
use Charcoal\Cms\News;
use Charcoal\Cms\Route\NewsRoute;

/**
 *
 */
class NewsRouteTest extends \PHPUnit_Framework_TestCase
{
    use \Charcoal\Tests\Cms\ContainerIntegrationTrait;

    /**
     * Tested Class.
     *
     * @var NewsRoute
     */
    private $obj;

    /**
     * Set up the test.
     */
    public function setUp()
    {
        $container = $this->getContainer();

        $this->getContainerProvider()->registerTemplateFactory($container);

        $route = $container['model/factory']->get(ObjectRoute::class);
        if ($route->source()->tableExists() === false) {
            $route->source()->createTable();
        }

        $this->obj = new NewsRoute([
            'config' => [],
            'path'   => '/en/news/foo'
        ]);
    }

    /**
     *
     */
    public function testPathResolvable()
    {
        $container = $this->getContainer();

        $locales   = $container['locales/manager'];
        $locales->setCurrentLocale($locales->currentLocale());

        // Create the news table
        $news = $container['model/factory']->create(News::class);
        $news->source()->createTable();

        $ret = $this->obj->pathResolvable($container);
        $this->assertFalse($ret);

        // Now try with a resolvable news.
        $news->setData([
            'id'             => 1,
            'title'          => 'Foo',
            'slug'           => new Translation('foo', $locales),
            'template_ident' => 'bar'
        ]);
        $id = $news->save();

        $ret = $this->obj->pathResolvable($container);
        //$this->assertTrue($ret);
    }

    /**
     *
     */
    public function testInvoke()
    {
        $container = $this->getContainer();
        $request   = $this->getMock(RequestInterface::class);
        $response  = new Response();

        // Create the news table
        $obj = $container['model/factory']->create(News::class);
        $obj->source()->createTable();

        $route  = $this->obj;
        $return = $route($container, $request, $response);
        $this->assertEquals(404, $return->getStatusCode());
    }
}
