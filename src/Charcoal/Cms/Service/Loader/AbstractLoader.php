<?php

namespace Charcoal\Cms\Service\Loader;

use RuntimeException;
use Exception;

// From 'charcoal-translator'
use Charcoal\Translator\TranslatorAwareTrait;

// From 'charcoal-factory'
use Charcoal\Factory\FactoryInterface;

// From 'charcoal-core'
use Charcoal\Loader\CollectionLoader;

/**
 * Abstract Loader
 */
class AbstractLoader
{
    use TranslatorAwareTrait;

    /**
     * Store the factory instance for the current class.
     *
     * @var FactoryInterface
     */
    protected $modelFactory;

    /**
     * Store the collection loader for the current class.
     *
     * @var CollectionLoader
     */
    protected $collectionLoader;

    /**
     * @var object $objType The object to load.
     */
    protected $objType;

    /**
     * NewsLoader constructor.
     * @param array $data The Data.
     * @throws Exception When there is missing data.
     */
    public function __construct(array $data)
    {
        if (!isset($data['factory'])) {
            throw new Exception(sprintf(
                'Model Factory must be defined in the %s constructor.',
                get_called_class()
            ));
        }
        if (!isset($data['loader'])) {
            throw new Exception(sprintf(
                'CollectionLoader must be defined in the %s constructor.',
                get_called_class()
            ));
        }

        if (isset($data['translator'])) {
            $this->setTranslator($data['translator']);
        }
        $this->setModelFactory($data['factory']);
        $this->setCollectionLoader($data['loader']);
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
     * Set a model collection loader.
     *
     * @param CollectionLoader $loader The collection loader.
     * @return self
     */
    protected function setCollectionLoader(CollectionLoader $loader)
    {
        $this->collectionLoader = $loader;

        return $this;
    }

    /**
     * Retrieve the model collection loader.
     *
     * @throws RuntimeException If the collection loader was not previously set.
     * @return CollectionLoader
     */
    public function collectionLoader()
    {
        if (!isset($this->collectionLoader)) {
            throw new RuntimeException(
                sprintf('Collection Loader is not defined for "%s"', get_class($this))
            );
        }

        return $this->collectionLoader;
    }
}
