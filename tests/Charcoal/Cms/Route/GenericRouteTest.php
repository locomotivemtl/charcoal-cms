<?php

namespace Charcoal\Tests\Cms\Route;

use InvalidArgumentException;

// From 'charcoal-object'
use Charcoal\Object\ObjectRoute;

// From 'charcoal-cms'
use Charcoal\Cms\Section;
use Charcoal\Cms\Route\GenericRoute;

/**
 * Test GenericRoute
 */
class GenericRouteTest extends SectionRouteTest
{
    /**
     * Asserts that `GenericRoute::__invoke()` method returns an HTTP Response object
     * with a 500 status code if the resolved model does not have a template identifier.
     *
     * The route's config throws an Exception when a model's template controller is invalid.
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

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Route view controller must be a string.');
        $response = $router($container, $request, $response);
    }

    /**
     * Asserts that `SectionRoute::__invoke()` method returns an HTTP Response object
     * with a 404 status code if the path does resolve but the routable model does not.
     *
     * @return void
     */
    public function testInvokeOnExistingObjectRouteWithMissingModel()
    {
        $container = $this->getContainer();

        $route = $container['model/factory']->create(ObjectRoute::class);
        $route->setData([
            'lang'         => 'en',
            'slug'         => 'en/imaginary',
            'routeObjType' => Section::objType(),
            'routeObjId'   => 99,
        ])->save();

        $container = $this->getContainer();
        $router    = $this->createRouter([
            'path' => '/en/imaginary',
        ]);

        $result = $router->pathResolvable($container);
        $this->assertFalse($result);

        $request   = $this->createHttpRequest();
        $response  = $this->createHttpResponse();

        $response = $router($container, $request, $response);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Create the dynamic route to test.
     *
     * @param  array $data The dynamic route dependencies.
     * @return GenericRoute
     */
    protected function createRouter(array $data = [])
    {
        return new GenericRoute($data + [
            'config' => [],
            'path'   => '',
        ]);
    }
}
