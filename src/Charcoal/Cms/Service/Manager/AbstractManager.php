<?php

namespace Charcoal\Cms\Service\Manager;

use RuntimeException;
use Exception;

// From 'charcoal-translator'
use Charcoal\Translator\TranslatorAwareTrait;

// From 'charcoal-factory'
use Charcoal\Factory\FactoryInterface;

// From 'charcoal-core'
use Charcoal\Loader\CollectionLoader;

/**
 * Abstract Manager
 */
class AbstractManager
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
     * @var object $adminConfig The admin config object model.
     */
    protected $adminConfig;

    /**
     * NewsManager constructor.
     * @param array $data The Data.
     * @throws Exception When $data index is not set.
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
        if (!isset($data['cms/config'])) {
            throw new Exception('You must define a global config object in your cms.json config file. (config_obj)');
        }

        if (isset($data['translator'])) {
            $this->setTranslator($data['translator']);
        }

        $this->setModelFactory($data['factory']);
        $this->setCollectionLoader($data['loader']);
        $this->setAdminConfig($data['cms/config']);
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

    /**
     * @param mixed $adminConfig The admin configuration.
     * @return self
     */
    public function setAdminConfig($adminConfig)
    {
        $this->adminConfig = $adminConfig;

        return $this;
    }

    /**
     * @return mixed
     */
    public function adminConfig()
    {
        return $this->adminConfig;
    }
}
