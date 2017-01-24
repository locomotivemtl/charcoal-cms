<?php

namespace Charcoal\Tests\Cms;

use PDO;
use Charcoal\Cms\Config\CmsConfig;
use Charcoal\Cms\Support\Helpers\DateHelper;

// From PSR-3
use Psr\Log\NullLogger;

// From 'cache/void-adapter' (PSR-6)
use Cache\Adapter\Void\VoidCachePool;

// From 'tedivm/stash' (PSR-6)
use Stash\Pool;
use Stash\Driver\Ephemeral;

// From Pimple
use Pimple\Container;

// From 'charcoal-factory'
use Charcoal\Factory\GenericFactory as Factory;

// From 'charcoal-core'
use Charcoal\Model\Service\MetadataLoader;
use Charcoal\Source\DatabaseSource;

// From 'charcoal-translator'
use Charcoal\Translator\LocalesManager;
use Charcoal\Translator\Translator;

// From 'charcoal-app'
use Charcoal\App\AppConfig;

/**
 * Service Container for Unit Tests
 */
class ContainerProvider
{
    public function registerBaseServices(Container $container)
    {
        $this->registerConfig($container);
        $this->registerCmsConfig($container);
        $this->registerDateHelper($container);
        $this->registerPdo($container);
        $this->registerLogger($container);
        $this->registerCache($container);
    }

    public function registerConfig(Container $container)
    {
        $container['config'] = function (Container $container) {
            return new AppConfig();
        };
    }

    public function registerCmsConfig(Container $container)
    {
        $container['cms/config'] = function (Container $container) {
            return new CmsConfig();
        };
    }

    public function registerDateHelper(Container $container)
    {
        $container['date/helper'] = function (Container $container) {
            return new DateHelper([
                'date_formats' => '',
                'time_formats' => ''
            ]);
        };
    }

    public function registerPdo(Container $container)
    {
        $container['database'] = function (Container $container) {
            $pdo = new PDO('sqlite::memory:');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $pdo;
        };
    }

    public function registerLogger(Container $container)
    {
        $container['logger'] = function (Container $container) {
            return new NullLogger();
        };
    }

    public function registerCache(Container $container)
    {
        $container['cache'] = function ($container) {
            return new Pool(new Ephemeral());
        };
    }

    public function registerTranslator(Container $container)
    {
        $container['locales/manager'] = function (Container $container) {
            return new LocalesManager([
                'locales' => [
                    'en' => [ 'locale' => 'en-US' ]
                ]
            ]);
        };

        $container['translator'] = function (Container $container) {
            return new Translator([
                'manager' => $container['locales/manager']
            ]);
        };
    }

    public function registerMetadataLoader(Container $container)
    {
        $container['metadata/loader'] = function (Container $container) {
            return new MetadataLoader([
                'logger'    => $container['logger'],
                'base_path' => realpath(__DIR__.'/../../../'),
                'paths'     => [
                    'metadata',
                    'vendor/locomotivemtl/charcoal-object/metadata'
                ],
                'cache'     => $container['cache']
            ]);
        };
    }

    public function registerSourceFactory(Container $container)
    {
        $container['source/factory'] = function ($container) {
            return new Factory([
                'map'       => [
                    'database' => DatabaseSource::class
                ],
                'arguments' => [ [
                    'logger' => $container['logger'],
                    'cache'  => $container['cache'],
                    'pdo'    => $container['database']
                ] ]
            ]);
        };
    }

    public function registerModelFactory(Container $container)
    {
        $container['model/factory'] = function ($container) {
            return new Factory([
                'arguments' => [ [
                    'container'        => $container,
                    'logger'           => $container['logger'],
                    'metadata_loader'  => $container['metadata/loader'],
                    'source_factory'   => $container['source/factory'],
                    'property_factory' => $container['property/factory']
                ] ]
            ]);
        };
    }

    public function registerPropertyFactory(Container $container)
    {
        $container['property/factory'] = function (Container $container) {
            return new Factory([
                'resolver_options' => [
                    'prefix' => '\\Charcoal\\Property\\',
                    'suffix' => 'Property'
                ],
                'arguments'      => [[
                    'container'  => $container,
                    'database'   => $container['database'],
                    'logger'     => $container['logger'],
                    'translator' => $container['translator']
                ]]
            ]);
        };
    }

    public function registerModelCollectionLoader(Container $container)
    {
        $container['model/collection/loader'] = function (Container $container) {
            return new \Charcoal\Loader\CollectionLoader([
                'logger' => $container['logger'],
                'cache'  => $container['cache']
            ]);
        };
    }

    public function registerTemplateFactory(Container $container)
    {
        $container['template/factory'] = function ($container) {
            return new Factory([
                'resolver_options' => [
                    'suffix' => 'Template'
                ],
                'arguments'        => [ [
                    'container' => $container,
                    'logger'    => $container['logger']
                ] ]
            ]);
        };
    }

    public function registerModelDependencies(Container $container)
    {
        $this->registerBaseServices($container);
        $this->registerTranslator($container);
        $this->registerMetadataLoader($container);
        $this->registerSourceFactory($container);
        $this->registerPropertyFactory($container);
        $this->registerModelFactory($container);
        $this->registerModelCollectionLoader($container);
    }
}
