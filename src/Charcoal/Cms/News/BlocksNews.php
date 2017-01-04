<?php

namespace Charcoal\Cms\News;

// Local dependencies
use Charcoal\Cms\AbstractNews;
use Charcoal\Cms\Mixin\Traits\BlocksNewsTrait;
use Charcoal\Cms\Mixin\BlocksNewsInterface;

// dependencies from `charcoal-core`
use Charcoal\Loader\CollectionLoader;

// Psr-7 dependencies
use RuntimeException;

// dependencies from `pimple`
use Pimple\Container;

/**
 * Blocks News
 *
 * News object that uses blocks content attachments as content source.
 */
class BlocksNews extends AbstractNews implements
    BlocksNewsInterface
{
    use BlocksNewsTrait;

    /**
     * Store the collection loader for the current class.
     *
     * @var CollectionLoader
     */
    protected $collectionLoader;

    // ==========================================================================
    // INIT
    // ==========================================================================

    /**
     * Inject dependencies from a DI Container.
     *
     * @param  Container $container A dependencies container instance.
     * @return void
     */
    public function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->setCollectionLoader($container['model/collection/loader']);
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
