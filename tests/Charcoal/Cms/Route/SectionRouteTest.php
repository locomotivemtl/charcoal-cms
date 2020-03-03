<?php

namespace Charcoal\Tests\Cms\Route;

use InvalidArgumentException;

// From 'charcoal-cms'
use Charcoal\Cms\Section;
use Charcoal\Cms\Route\SectionRoute;

/**
 * Test SectionRoute
 */
class SectionRouteTest extends AbstractRouteTestCase
{
    /**
     * Asserts that `SectionRoute::__invoke()` method returns an HTTP Response object
     * with a 404 status code if the path does not resolve to any routable model.
     *
     * @return void
     */
    public function testInvokeOnNonexistentModel()
    {
        $container = $this->getContainer();
        $router    = $this->createRouter([
            'path' => '/en/nonexistent',
        ]);

        $result = $router->pathResolvable($container);
        $this->assertFalse($result);

        $request   = $this->createHttpRequest();
        $response  = $this->createHttpResponse();

        $response = $router($container, $request, $response);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Asserts that `SectionRoute::__invoke()` method returns an HTTP Response object
     * with a 500 status code and the "templateIdent" as the body
     * if the resolved model has a nonexistent template controller.
     *
     * The "config.view.defaultController" option ensures the "template/factory" service
     * does not throw an Exception when a model's template controller can not be found.
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
            'path' => '/en/charcoal',
        ]);

        $result = $router->pathResolvable($container);
        $this->assertTrue($result);

        $request   = $this->createHttpRequest();
        $response  = $this->createHttpResponse();

        $response = $router($container, $request, $response);
        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * Asserts that `SectionRoute::__invoke()` method returns an HTTP Response object
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
            'path' => '/en/charcoal',
        ]);

        $result = $router->pathResolvable($container);
        $this->assertTrue($result);

        $request   = $this->createHttpRequest();
        $response  = $this->createHttpResponse();

        $response = $router($container, $request, $response);
        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * Asserts that `SectionRoute::__invoke()` method returns an HTTP Response object
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
            'path' => '/en/charcoal',
        ]);

        $result = $router->pathResolvable($container);
        $this->assertTrue($result);

        $request   = $this->createHttpRequest();
        $response  = $this->createHttpResponse();

        $response = $router($container, $request, $response);
        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * Asserts that `SectionRoute::__invoke()` method returns an HTTP Response object
     * with a 2XX status code if the path does resolve to a specific routable model.
     *
     * @return void
     */
    public function testInvokeOnExistingModelWithTemplateIdent()
    {
        $this->insertMockRoutableContextObjects([
            'templateIdent' => 'charcoal/tests/cms/mock/home',
        ]);

        $container = $this->getContainer();
        $router    = $this->createRouter([
            'path' => '/en/charcoal',
        ]);

        $result = $router->pathResolvable($container);
        $this->assertTrue($result);

        $request   = $this->createHttpRequest();
        $response  = $this->createHttpResponse();

        $response = $router($container, $request, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('<h1>Home</h1>', (string)$response->getBody());
    }

    /**
     * Create the dynamic route to test.
     *
     * @param  array $data The dynamic route dependencies.
     * @return SectionRoute
     */
    protected function createRouter(array $data = [])
    {
        return new SectionRoute($data + [
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

        $section = $container['model/factory']->get(Section::class);
        if ($section->source()->tableExists() === false) {
            $section->source()->createTable();
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
        $factory   = $container['model/factory'];

        $factory->create(Section::class)
                ->setData($data + [
                    'id'    => 1,
                    'title' => 'Charcoal',
                    'slug'  => 'charcoal',
                ])->save();

        $factory->create(Section::class)
                ->setData($data + [
                    'id'     => 2,
                    'master' => 1,
                    'title'  => 'Memo',
                    'slug'   => 'memo',
                ])->save();
    }
}
