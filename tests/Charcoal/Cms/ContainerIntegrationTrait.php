<?php

namespace Charcoal\Tests\Cms;

// From Pimple
use Pimple\Container;

// From 'charcoal-cms/tests'
use Charcoal\Tests\Cms\ContainerProvider;

/**
 * Integrates Charcoal's service container into PHPUnit.
 *
 * Ensures Charcoal framework is set-up for each test.
 */
trait ContainerIntegrationTrait
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @see    ContainerProvider
     * @return Container
     */
    private function getContainer()
    {
        if ($this->container === null) {
            $provider  = new ContainerProvider();
            $container = new Container();

            $provider->registerModelDependencies($container);

            $this->container = $container;
        }

        return $this->container;
    }
}
