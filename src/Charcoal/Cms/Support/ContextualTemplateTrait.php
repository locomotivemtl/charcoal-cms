<?php

namespace Charcoal\Cms\Support;

use InvalidArgumentException;

// From 'psr/http-message'
use Psr\Http\Message\UriInterface;

// From 'charcoal-core'
use Charcoal\Model\Model;
use Charcoal\Model\ModelInterface;

/**
 * Additional utilities for the routing.
 */
trait ContextualTemplateTrait
{
    /**
     * The current rendering / data context.
     *
     * @var ModelInterface|null
     */
    protected $contextObject;

    /**
     * The route group path (base URI).
     *
     * @var string|null
     */
    protected $routeGroup;

    /**
     * The route endpoint path (path URI).
     *
     * @var string|null
     */
    protected $routeEndpoint;

    /**
     * Track the state of context creation.
     *
     * @var boolean
     */
    protected $isCreatingContext = false;

    /**
     * The class name of the section model.
     *
     * A fully-qualified PHP namespace. Used for the model factory.
     *
     * @var string
     */
    protected $genericContextClass = Model::class;

    /**
     * Set the class name of the generic context model.
     *
     * @param  string $className The class name of the section model.
     * @throws InvalidArgumentException If the class name is not a string.
     * @return AbstractPropertyDisplay Chainable
     */
    public function setGenericContextClass($className)
    {
        if (!is_string($className)) {
            throw new InvalidArgumentException(
                'Generic context class name must be a string.'
            );
        }

        $this->genericContextClass = $className;

        return $this;
    }

    /**
     * Retrieve the class name of the generic context model.
     *
     * @return string
     */
    public function genericContextClass()
    {
        return $this->genericContextClass;
    }

    /**
     * Set the current renderable object relative to the context.
     *
     * @param  ModelInterface $context The context / view to render the template with.
     * @return self
     */
    public function setContextObject(ModelInterface $context)
    {
        $this->contextObject = $context;

        return $this;
    }

    /**
     * Retrieve the current object relative to the context.
     *
     * This method is meant to be reimplemented in a child template controller
     * to return the resolved object that the module considers "the context".
     *
     * @return ModelInterface|null
     */
    public function contextObject()
    {
        if ($this->contextObject === null) {
            $this->contextObject = $this->createGenericContext();
        }

        return $this->contextObject;
    }

    /**
     * Create a generic object relative to the context.
     *
     * @return ModelInterface|null
     */
    protected function createGenericContext()
    {
        if ($this->isCreatingContext) {
            return null;
        }

        $this->isCreatingContext = true;

        $obj = $this->modelFactory()->create($this->genericContextClass());

        $baseUrl = $this->baseUrl();
        if ($this->routeEndpoint) {
            $endpoint = $this->translator()->translation($this->routeEndpoint);
            foreach ($this->translator()->availableLocales() as $lang) {
                $uri = $baseUrl->withPath($endpoint[$lang]);

                if ($this->routeGroup) {
                    $uri = $uri->withBasePath($this->routeGroup[$lang]);
                }

                $base = $uri->getBasePath();
                $path = $uri->getPath();
                $path = $base.'/'.ltrim($path, '/');

                $endpoint[$lang] = $path;
            }
        } else {
            $endpoint = null;
        }

        $obj['url']   = $endpoint;
        $obj['title'] = $this->title();

        $this->isCreatingContext = false;

        return $obj;
    }

    /**
     * Retrieve the current URI of the context.
     *
     * @return UriInterface|string|null
     */
    public function currentUrl()
    {
        $context = $this->contextObject();

        if ($context && isset($context['url'])) {
            return $context['url'];
        }

        return null;
    }

    /**
     * Append a path to the base URI.
     *
     * @param  string $path The base path.
     * @return self
     */
    public function setRouteGroup($path)
    {
        $group = $this->translator()->translation($path);

        foreach ($this->translator()->availableLocales() as $lang) {
            $group[$lang] = trim($group[$lang], '/');
        }

        $this->routeGroup = $group;

        return $this;
    }

    /**
     * Append a path to the URI.
     *
     * @param  string $path The main path.
     * @return self
     */
    public function setRouteEndpoint($path)
    {
        $endpoint = $this->translator()->translation($path);

        foreach ($this->translator()->availableLocales() as $lang) {
            $endpoint[$lang] = trim($endpoint[$lang], '/');
        }

        $this->routeEndpoint = $endpoint;

        return $this;
    }

    /**
     * Retrieve the base URI of the project.
     *
     * @return UriInterface|null
     */
    abstract public function baseUrl();

    /**
     * Retrieve the title of the page (from the context).
     *
     * @return string
     */
    abstract public function title();

    /**
     * Retrieve the translator service.
     *
     * @see    \Charcoal\Translator\TranslatorAwareTrait
     * @return \Charcoal\Translator\Translator
     */
    abstract protected function translator();

    /**
     * Retrieve the object model factory.
     *
     * @return \Charcoal\Factory\FactoryInterface
     */
    abstract public function modelFactory();
}
