<?php

namespace Charcoal\Cms\Route;

use RuntimeException;
use InvalidArgumentException;

// From Pimple
use Pimple\Container;

// From PSR-7
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

// From 'charcoal-factory'
use Charcoal\Factory\FactoryInterface;

// From 'charcoal-translator'
use Charcoal\Translator\TranslatorAwareTrait;

// From 'charcoal-core'
use Charcoal\Model\ModelInterface;
use Charcoal\Loader\CollectionLoader;

// From 'charcoal-app'
use Charcoal\App\Route\TemplateRoute;

// From 'charcoal-object'
use Charcoal\Object\ObjectRoute;
use Charcoal\Object\ObjectRouteInterface;
use Charcoal\Object\RoutableInterface;

// From 'charcoal-cms'
use Charcoal\Cms\TemplateableInterface;

/**
 * Generic Object Route Handler
 *
 * Uses implementations of {@see ObjectRouteInterface}
 * to match routes for catch-all routing patterns.
 */
class GenericRoute extends TemplateRoute
{
    use TranslatorAwareTrait;

    /**
     * The URI path.
     *
     * @var string
     */
    private $path;

    /**
     * The object route.
     *
     * @var ObjectRouteInterface
     */
    private $objectRoute;

    /**
     * The target object of the {@see GenericRoute Chainable::$objectRoute}.
     *
     * @var ModelInterface|RoutableInterface
     */
    private $contextObject;

    /**
     * Store the factory instance for the current class.
     *
     * @var FactoryInterface
     */
    private $modelFactory;

    /**
     * Store the collection loader for the current class.
     *
     * @var CollectionLoader
     */
    private $collectionLoader;

    /**
     * Track the state of required dependencies.
     *
     * @var boolean
     */
    protected $hasDependencies = false;

    /**
     * The class name of the object route model.
     *
     * Must be a fully-qualified PHP namespace and an implementation of
     * {@see ObjectRouteInterface}. Used by the model factory.
     *
     * @var string
     */
    protected $objectRouteClass = ObjectRoute::class;

    /**
     * Store the available templates.
     *
     * @var array
     */
    protected $availableTemplates = [];

    /**
     * Returns new template route object.
     *
     * @param array $data Class depdendencies.
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->setPath(ltrim($data['path'], '/'));
    }

    /**
     * Determine if the URI path resolves to an object.
     *
     * @param  Container $container A DI (Pimple) container.
     * @return boolean
     */
    public function pathResolvable(Container $container)
    {
        if ($this->hasDependencies === false) {
            $this->setDependencies($container);
        }

        $objectRoute = $this->getObjectRouteFromPath();
        if (!$objectRoute || !$this->isValidObjectRoute($objectRoute)) {
            return false;
        }

        $contextObject = $this->getContextObject();
        if (!$contextObject || !$this->isValidContextObject($contextObject)) {
            return false;
        }

        return true;
    }

    /**
     * Resolve the dynamic route.
     *
     * @param  Container         $container A DI (Pimple) container.
     * @param  RequestInterface  $request   A PSR-7 compatible Request instance.
     * @param  ResponseInterface $response  A PSR-7 compatible Response instance.
     * @return ResponseInterface
     */
    public function __invoke(
        Container $container,
        RequestInterface $request,
        ResponseInterface $response
    ) {
        if ($this->hasDependencies === false) {
            $this->setDependencies($container);
        }

        $response = $this->resolveLatestObjectRoute($request, $response);
        if (!$response->isRedirect()) {
            $objectRoute = $this->getObjectRouteFromPath();
            if (!$objectRoute || !$this->isValidObjectRoute($objectRoute)) {
                // If the ObjectRoute is invalid, it probably does not exist
                // which also means a Model does not exist.
                return $response->withStatus(404);
            }

            $this->resolveTemplateContextObject();

            $contextObject = $this->getContextObject();
            if (!$contextObject || !$this->isValidContextObject($contextObject)) {
                // If the Model is invalid, it probably does not exist.
                return $response->withStatus(404);
            }

            $templateContent = $this->templateContent($container, $request);

            $config = $this->config();
            $templateIdent = $config['template'];

            if ($templateContent === $templateIdent || $templateContent === '') {
                $container['logger']->warning(sprintf(
                    '[%s] Missing or bad template identifier on model [%s] for ID [%s]',
                    get_class($this),
                    get_class($this->getContextObject()),
                    $templateIdent
                ));
                return $response->withStatus(500);
            }

            $response->write($templateContent);
        }

        return $response;
    }

    /**
     * Create a route object.
     *
     * @return ObjectRouteInterface
     */
    public function createRouteObject()
    {
        $route = $this->modelFactory()->create($this->objectRouteClass());

        return $route;
    }

    /**
     * Retrieve the class name of the object route model.
     *
     * @return string
     */
    public function objectRouteClass()
    {
        return $this->objectRouteClass;
    }

    /**
     * Inject dependencies from a DI Container.
     *
     * @param  Container $container A dependencies container instance.
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        $this->setTranslator($container['translator']);
        $this->setModelFactory($container['model/factory']);
        $this->setCollectionLoader($container['model/collection/loader']);

        if (isset($container['config']['templates'])) {
            $this->availableTemplates = $container['config']['templates'];
        }

        $this->hasDependencies = true;
    }

    /**
     * Determine if the current object route is the latest object route.
     *
     * This method loads the latest object route from the datasource and compares
     * their creation dates. Both instances could be the same object (ID).
     *
     * @param  RequestInterface  $request  A PSR-7 compatible Request instance.
     * @param  ResponseInterface $response A PSR-7 compatible Response instance.
     * @return ResponseInterface
     */
    protected function resolveLatestObjectRoute(
        RequestInterface $request,
        ResponseInterface $response
    ) {
        $current = $this->getObjectRouteFromPath();
        $latest  = $this->getLatestObjectPathHistory($current);

        // Redirect if latest route is newer
        if ($latest && $latest->getCreationDate() > $current->getCreationDate()) {
            $redirect = $this->parseRedirect($latest->getSlug(), $request);
            $response = $response->withRedirect($redirect, 301);
        }

        return $response;
    }

    /**
     * @return self
     */
    protected function resolveTemplateContextObject()
    {
        $config = $this->config();

        $objectRoute   = $this->getObjectRouteFromPath();
        $contextObject = $this->getContextObject();

        $currentLang   = $objectRoute->getLang();
        if ($currentLang) {
            // Set language according to the route's language
            $this->setLocale($currentLang);
        }

        $templateChoice = [];

        // Templateable Objects have specific methods
        if ($contextObject instanceof TemplateableInterface) {
            $identProperty   = $contextObject->property('templateIdent');
            $templateIdent   = $contextObject['templateIdent'] ?: $objectRoute->getRouteTemplate();
            $controllerIdent = $contextObject['controllerIdent'] ?: $templateIdent;
            $templateChoice  = $identProperty->choice($templateIdent);
        } else {
            // Use global templates to verify for custom paths
            $templateIdent   = $objectRoute->getRouteTemplate();
            $controllerIdent = $templateIdent;
            foreach ($this->availableTemplates as $templateKey => $templateData) {
                if (!isset($templateData['value'])) {
                    $templateData['value'] = $templateKey;
                }

                if ($templateData['value'] === $templateIdent) {
                    $templateChoice = $templateData;
                    break;
                }
            }
        }

        // Template ident defined in template global config
        // Check for custom path / controller
        if (isset($templateChoice['template'])) {
            $templatePath = $templateChoice['template'];
            $templateController = $templateChoice['template'];
        } else {
            $templatePath = $templateIdent;
            $templateController = $controllerIdent;
        }

        // Template controller defined in choices, affect it.
        if (isset($templateChoice['controller'])) {
            $templateController = $templateChoice['controller'];
        }

        $config['template'] = $templatePath;
        $config['controller'] = $templateController;

        // Always be an array
        $templateOptions = [];

        // Custom template options
        if (isset($templateChoice['template_options'])) {
            $templateOptions = $templateChoice['template_options'];
        }

        // Overwrite from custom object template_options
        if ($contextObject instanceof TemplateableInterface) {
            if (!empty($contextObject['templateOptions'])) {
                $templateOptions = $contextObject['templateOptions'];
            }
        }

        if (isset($templateOptions) && $templateOptions) {
            // Not sure what this was about?
            $config['template_data'] = array_merge($config['template_data'], $templateOptions);
        }

        // Merge Route options from object-route
        $routeOptions = $objectRoute->getRouteOptions();
        if ($routeOptions && count($routeOptions)) {
            $config['template_data'] = array_merge($config['template_data'], $routeOptions);
        }

        $this->setConfig($config);

        return $this;
    }

    /**
     * @param  Container        $container A DI (Pimple) container.
     * @param  RequestInterface $request   The request to intialize the template with.
     * @return string
     */
    protected function createTemplate(Container $container, RequestInterface $request)
    {
        $template = parent::createTemplate($container, $request);

        $contextObject = $this->getContextObject();
        $template['contextObject'] = $contextObject;

        return $template;
    }

    /**
     * Set the class name of the object route model.
     *
     * @param  string $className The class name of the object route model.
     * @throws InvalidArgumentException If the class name is not a string.
     * @return self
     */
    protected function setObjectRouteClass($className)
    {
        if (!is_string($className)) {
            throw new InvalidArgumentException(
                'Route class name must be a string.'
            );
        }

        $this->objectRouteClass = $className;

        return $this;
    }

    /**
     * Asserts that the object route is valid, throws an Exception if not.
     *
     * @param  RoutableInterface $contextObject A context object to test.
     * @throws InvalidArgumentException If the context object is invalid.
     * @return void
     */
    protected function assertValidContextObject(RoutableInterface $contextObject)
    {
        if (!$this->isValidContextObject($contextObject)) {
            throw new InvalidArgumentException('Invalid context object');
        }
    }

    /**
     * Determine if the object route is valid.
     *
     * @param  RoutableInterface $contextObject A context object to test.
     * @return boolean
     */
    protected function isValidContextObject(RoutableInterface $contextObject)
    {
        if (!$contextObject->id()) {
            return false;
        }

        if ($contextObject instanceof RoutableInterface) {
            return $contextObject->isActiveRoute();
        }

        if (isset($contextObject['active'])) {
            return (bool)$contextObject['active'];
        }

        return true;
    }

    /**
     * Get the object associated with the matching object route.
     *
     * @return RoutableInterface
     */
    protected function getContextObject()
    {
        if ($this->contextObject === null) {
            $this->contextObject = $this->loadContextObject();
        }

        return $this->contextObject;
    }

    /**
     * Load the object associated with the matching object route.
     *
     * Validating if the object ID exists is delegated to the
     * {@see GenericRoute Chainable::pathResolvable()} method.
     *
     * @throws RuntimeException If the object route is incomplete.
     * @return RoutableInterface
     */
    protected function loadContextObject()
    {
        $route = $this->getObjectRouteFromPath();

        $this->assertValidObjectRoute($route);

        $obj = $this->modelFactory()->create($route->getRouteObjType());
        $obj->load($route->getRouteObjId());

        return $obj;
    }

    /**
     * Asserts that the object route is valid, throws an Exception if not.
     *
     * @param  ObjectRouteInterface $route An object route to test.
     * @throws InvalidArgumentException If the object route is incomplete.
     * @return void
     */
    protected function assertValidObjectRoute(ObjectRouteInterface $route)
    {
        if (!$this->isValidObjectRoute($route)) {
            throw new InvalidArgumentException('Incomplete object route');
        }
    }

    /**
     * Determine if the object route is valid.
     *
     * @param  ObjectRouteInterface $route An object route to test.
     * @return boolean
     */
    protected function isValidObjectRoute(ObjectRouteInterface $route)
    {
        return ($route->id() && $route->getRouteObjType() && $route->getRouteObjId());
    }

    /**
     * Get the object route matching the URI path.
     *
     * @return ObjectRouteInterface
     */
    protected function getObjectRouteFromPath()
    {
        if ($this->objectRoute === null) {
            $this->objectRoute = $this->loadObjectRouteFromPath();
        }

        return $this->objectRoute;
    }

    /**
     * Load the object route matching the URI path.
     *
     * @return ObjectRouteInterface
     */
    protected function loadObjectRouteFromPath()
    {
        // Load current slug
        // Slugs are unique
        // Slug can be duplicated by adding the front "/" to it hence the order by last_modification_date
        $route = $this->createRouteObject();
        $table = '`'.$route->source()->table().'`';
        $where = '`lang` = :lang AND (`slug` = :route1 OR `slug` = :route2)';
        $order = '`last_modification_date` DESC';
        $query = 'SELECT * FROM '.$table.' WHERE '.$where.' ORDER BY '.$order.' LIMIT 1';

        $route->loadFromQuery($query, [
            'route1' => '/'.$this->path(),
            'route2' => $this->path(),
            'lang'   => $this->translator()->getLocale(),
        ]);

        return $route;
    }

    /**
     * Retrieve the latest object route from the given object route's
     * associated object.
     *
     * The object routes are ordered by descending creation date (latest first).
     * Should never MISS, the given object route should exist.
     *
     * @param  ObjectRouteInterface $route Routable Object.
     * @return ObjectRouteInterface|null
     */
    public function getLatestObjectPathHistory(ObjectRouteInterface $route)
    {
        if (!$this->isValidObjectRoute($route)) {
            return null;
        }

        $loader = $this->collectionLoader();
        $loader
            ->setModel($route)
            ->addFilter('active', true)
            ->addFilter('route_obj_type', $route->getRouteObjType())
            ->addFilter('route_obj_id', $route->getRouteObjId())
            ->addFilter('lang', $route->getLang())
            ->addOrder('creation_date', 'desc')
            ->setPage(1)
            ->setNumPerPage(1);

        if ($route->getRouteOptionsIdent()) {
            $loader->addFilter('route_options_ident', $route->getRouteOptionsIdent());
        } else {
            $loader->addFilter('route_options_ident', '', [ 'operator' => 'IS NULL' ]);
        }

        return $loader->load()->first();
    }

    /**
     * SETTERS
     */

    /**
     * Set the specified URI path.
     *
     * @param string $path The path to use for route resolution.
     * @return self
     */
    protected function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Set an object model factory.
     *
     * @param FactoryInterface $factory The model factory, to create objects.
     * @return self
     */
    protected function setModelFactory(FactoryInterface $factory)
    {
        $this->modelFactory = $factory;

        return $this;
    }

    /**
     * Set a model collection loader.
     *
     * @param CollectionLoader $loader The collection loader.
     * @return self
     */
    public function setCollectionLoader(CollectionLoader $loader)
    {
        $this->collectionLoader = $loader;

        return $this;
    }

    /**
     * Sets the environment's current locale.
     *
     * @param  string $langCode The locale's language code.
     * @return void
     */
    protected function setLocale($langCode)
    {
        $translator = $this->translator();
        $translator->setLocale($langCode);

        $available = $translator->locales();
        $fallbacks = $translator->getFallbackLocales();

        array_unshift($fallbacks, $langCode);
        $fallbacks = array_unique($fallbacks);

        $locales = [];
        foreach ($fallbacks as $code) {
            if (isset($available[$code])) {
                $locale = $available[$code];
                if (isset($locale['locales'])) {
                    $choices = (array)$locale['locales'];
                    array_push($locales, ...$choices);
                } elseif (isset($locale['locale'])) {
                    array_push($locales, $locale['locale']);
                }
            }
        }

        $locales = array_unique($locales);

        if ($locales) {
            setlocale(LC_ALL, $locales);
        }
    }

    /**
     * GETTERS
     */

    /**
     * Retrieve the URI path.
     *
     * @return string
     */
    protected function path()
    {
        return $this->path;
    }

    /**
     * Retrieve the object model factory.
     *
     * @throws RuntimeException If the model factory was not previously set.
     * @return FactoryInterface
     */
    public function modelFactory()
    {
        if (!isset($this->modelFactory)) {
            throw new RuntimeException(
                sprintf('Model Factory is not defined for "%s"', get_class($this))
            );
        }

        return $this->modelFactory;
    }

    /**
     * Retrieve the model collection loader.
     *
     * @throws RuntimeException If the collection loader was not previously set.
     * @return CollectionLoader
     */
    protected function collectionLoader()
    {
        if (!isset($this->collectionLoader)) {
            throw new RuntimeException(
                sprintf('Collection Loader is not defined for "%s"', get_class($this))
            );
        }

        return $this->collectionLoader;
    }

    /**
     * @return boolean
     */
    protected function cacheEnabled()
    {
        $obj = $this->getContextObject();
        return $obj['cache'] ?: false;
    }

    /**
     * @return integer
     */
    protected function cacheTtl()
    {
        $obj = $this->getContextObject();
        return $obj['cache_ttl'] ?: 0;
    }

    /**
     * @return string
     */
    protected function cacheIdent()
    {
        $obj = $this->getContextObject();
        return $obj->objType().'.'.$obj->id();
    }
}
