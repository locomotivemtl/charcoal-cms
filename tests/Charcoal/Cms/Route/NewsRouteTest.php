<?php

namespace Charcoal\Tests\Cms\Route;

use InvalidArgumentException;

// From 'charcoal-cms'
use Charcoal\Cms\News;
use Charcoal\Cms\Route\NewsRoute;

/**
 * Test NewsRoute
 */
class NewsRouteTest extends AbstractRouteTestCase
{
    /**
     * Asserts that `NewsRoute::__invoke()` method returns an HTTP Response object
     * with a 404 status code if the path does not resolve to any routable model.
     *
     * @return void
     */
    public function testInvokeOnNonexistentModel()
    {
        $container = $this->getContainer();
        $router    = $this->createRouter([
            'path' => '/en/news/nonexistent',
        ]);

        $result = $router->pathResolvable($container);
        $this->assertFalse($result);

        $request   = $this->createHttpRequest();
        $response  = $this->createHttpResponse();

        $response = $router($container, $request, $response);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Asserts that an Exception is raised from the `NewsRoute::__invoke()` method
     * if the resolved model has a nonexistent template controller.
     *
     * The "template/factory" service throws an Exception when a model's template controller can not be found.
     *
     * @return void
     */
    public function testInvokeOnExistingModelWithMissingTemplateController()
    {
        $this->insertMockRoutableContextObjects([
            'templateIdent' => 'nonexistent',
        ]);

        $container = $this->getContainer();
        $router    = $this->createRouter([
            'path' => '/en/news/hello-world',
        ]);

        $result = $router->pathResolvable($container);
        $this->assertTrue($result);

        $request   = $this->createHttpRequest();
        $response  = $this->createHttpResponse();

        $this->expectException(InvalidArgumentException::class);
        $response = $router($container, $request, $response);
    }

    /**
     * Asserts that `NewsRoute::__invoke()` method returns an HTTP Response object
     * with a 500 status code if the resolved model does not have a template identifier.
     *
     * @return void
     */
    public function testInvokeOnExistingModelWithoutTemplateIdent()
    {
        $this->insertMockRoutableContextObjects([
            'templateIdent' => '',
        ]);

        $container = $this->getContainer();
        $router    = $this->createRouter([
            'path' => '/en/news/hello-world',
        ]);

        $result = $router->pathResolvable($container);
        $this->assertTrue($result);

        $request   = $this->createHttpRequest();
        $response  = $this->createHttpResponse();

        $response = $router($container, $request, $response);
        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * Asserts that `NewsRoute::__invoke()` method returns an HTTP Response object
     * with a 500 status code if the resolved model does not have a rendered template view.
     *
     * @return void
     */
    public function testInvokeOnExistingModelWithBadTemplateIdent()
    {
        $this->insertMockRoutableContextObjects([
            'templateIdent' => 'charcoal/tests/cms/mock/broken',
        ]);

        $container = $this->getContainer();
        $router    = $this->createRouter([
            'path' => '/en/news/hello-world',
        ]);

        $result = $router->pathResolvable($container);
        $this->assertTrue($result);

        $request   = $this->createHttpRequest();
        $response  = $this->createHttpResponse();

        $response = $router($container, $request, $response);
        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * Asserts that `NewsRoute::__invoke()` method returns an HTTP Response object
     * with a 2XX status code if the path does resolve to a specific routable model.
     *
     * @return void
     */
    public function testInvokeOnExistingModelWithTemplateIdent()
    {
        $this->insertMockRoutableContextObjects([
            'templateIdent' => 'charcoal/tests/cms/mock/news',
        ]);

        $container = $this->getContainer();
        $router    = $this->createRouter([
            'path' => '/en/news/hello-world',
        ]);

        $result = $router->pathResolvable($container);
        $this->assertTrue($result);

        $request   = $this->createHttpRequest();
        $response  = $this->createHttpResponse();

        $response = $router($container, $request, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('<h1>News</h1>', (string)$response->getBody());
    }

    /**
     * Create the dynamic route to test.
     *
     * @param  array $data The dynamic route dependencies.
     * @return NewsRoute
     */
    protected function createRouter(array $data = [])
    {
        return new NewsRoute($data + [
            'config' => [],
            'path'   => '',
        ]);
    }

    /**
     * Create the model's database table to test lookup.
     *
     * @return void
     */
    protected function setUpRoutableContextModel()
    {
        $container = $this->getContainer();

        $news = $container['model/factory']->get(News::class);
        if ($news->source()->tableExists() === false) {
            $news->source()->createTable();
        }
    }

    /**
     * Insert an entity into the model's database table to test a resolvable route.
     *
     * @param  array $data The model data.
     * @return void
     */
    protected function insertMockRoutableContextObjects(array $data = [])
    {
        $container = $this->getContainer();

        $news = $container['model/factory']->create(News::class);

        $news->setData($data + [
            'id'    => 1,
            'title' => 'Hello, World!',
            'slug'  => 'hello-world',
        ]);

        $news->save();
    }
}
