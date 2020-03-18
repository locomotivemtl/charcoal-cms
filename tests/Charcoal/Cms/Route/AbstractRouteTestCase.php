<?php

namespace Charcoal\Tests\Cms\Route;

// From PSR-7
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

// From Slim
use Slim\Http\Response;

// From 'charcoal-object'
use Charcoal\Object\ObjectRoute;

// From 'charcoal-cms'
use Charcoal\App\App;
use Charcoal\Tests\AbstractTestCase;
use Charcoal\Tests\Cms\ContainerIntegrationTrait;

/**
 * Basic Dynamic Route Test
 */
abstract class AbstractRouteTestCase extends AbstractTestCase
{
    use ContainerIntegrationTrait;

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

        /**
         * Necessary Evil
         *
         * This is required because certain features of Charcoal
         * still rely on a singleton instance, such as
         * {@see \Charcoal\App\Route\TemplateRouteConfig}.
         */
        App::instance($container);

        $this->setUpObjectRouteModel();
        $this->setUpRoutableContextModel();
    }

    /**
     * Create the centralized object route database table to test lookup.
     *
     * @return void
     */
    protected function setUpObjectRouteModel()
    {
        $container = $this->getContainer();

        $route = $container['model/factory']->get(ObjectRoute::class);
        if ($route->source()->tableExists() === false) {
            $route->source()->createTable();
        }
    }

    /**
     * Create an HTTP Request object.
     *
     * @return RequestInterface
     */
    protected function createHttpRequest()
    {
        return $this->createMock(RequestInterface::class);
    }

    /**
     * Create an HTTP Response object.
     *
     * @return ResponseInterface
     */
    protected function createHttpResponse()
    {
        return new Response();
    }

    /**
     * Assertion when given an empty path.
     *
     * @return void
     */
    public function testPathResolvableOnEmptyPath()
    {
        $container = $this->getContainer();
        $router    = $this->createRouter([
            'path' => '',
        ]);

        $result = $router->pathResolvable($container);
        $this->assertFalse($result);
    }

    /**
     * Asserts that `__invoke()` method returns an HTTP Response object
     * with a 404 status code if the path does not resolve to any routable model.
     *
     * @return void
     */
    abstract public function testInvokeOnNonexistentModel();

    /**
     * Asserts that an Exception is raised from the `__invoke()` method
     * if the resolved model has a nonexistent template controller.
     *
     * The "template/factory" service throws an Exception when a model's template controller can not be found.
     *
     * The "config.view.defaultController" option ensures the "template/factory" service
     * does not throw an Exception when a model's template controller can not be found.
     *
     * @return void
     */
    abstract public function testInvokeOnExistingModelWithMissingTemplateController();

    /**
     * Asserts that `__invoke()` method returns an HTTP Response object
     * with a 500 status code if the resolved model does not have a template identifier.
     *
     * @return void
     */
    abstract public function testInvokeOnExistingModelWithoutTemplateIdent();

    /**
     * Asserts that `__invoke()` method returns an HTTP Response object
     * with a 500 status code if the resolved model does not have a rendered template view.
     *
     * @return void
     */
    abstract public function testInvokeOnExistingModelWithBadTemplateIdent();

    /**
     * Asserts that `__invoke()` method returns an HTTP Response object
     * with a 2XX status code if the path does resolve to a specific routable model.
     *
     * @return void
     */
    abstract public function testInvokeOnExistingModelWithTemplateIdent();

    /**
     * Create the dynamic route to test.
     *
     * @param  array $data The dynamic route dependencies.
     * @return \Charcoal\App\Route\RouteInterface
     */
    abstract protected function createRouter(array $data = []);

    /**
     * Create the model's database table to test lookup.
     *
     * @return void
     */
    abstract protected function setUpRoutableContextModel();

    /**
     * Insert an entity into the model's database table to test a resolvable route.
     *
     * @param  array $data The model data.
     * @return void
     */
    abstract protected function insertMockRoutableContextObjects(array $data = []);
}
