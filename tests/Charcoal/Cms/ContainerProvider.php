<?php

namespace Charcoal\Tests\Cms;

use \PDO;

use \Psr\Log\NullLogger;
use \Cache\Adapter\Void\VoidCachePool;

use \Pimple\Container;

use \Charcoal\Factory\GenericFactory as Factory;

use \Charcoal\App\AppConfig;

use \Charcoal\Model\Service\MetadataLoader;
use \Charcoal\Source\DatabaseSource;

use \Charcoal\Translation\TranslationString;

class ContainerProvider
{

    public function registerBaseServices(Container $container)
    {
        $this->registerConfig($container);
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

    public function registerPdo(Container $container)
    {
        $container['pdo'] = function (Container $container) {
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
            return new VoidCachePool();
        };
    }

    public function registerMetadataLoader(Container $container)
    {
        $container['metadata/loader'] = function (Container $container) {
            return new MetadataLoader([
                'logger' => $container['logger'],
                'base_path' => realpath(__DIR__.'/../../../'),
                    'paths' => [
                        'metadata',
                        'vendor/locomotivemtl/charcoal-base/metadata'
                    ],
                'cache'  => $container['cache']
            ]);
        };
    }

    public function registerSourceFactory(Container $container)
    {
        $container['source/factory'] = function ($container) {
            return new Factory([
                'map' => [
                    'database' => DatabaseSource::class
                ],
                'arguments'  => [[
                    'logger' => $container['logger'],
                    'cache'  => $container['cache'],
                    'pdo'    => $container['pdo']
                ]]
            ]);
        };
    }

    public function registerModelFactory(Container $container)
    {
        $container['model/factory'] = function ($container) {
            return new Factory([
                'arguments' => [[
                    'container'         => $container,
                    'logger'            => $container['logger'],
                    'metadata_loader'   => $container['metadata/loader'],
                    'source_factory'    => $container['source/factory'],
                    'property_factory'  => $container['property/factory']
                ]]
            ]);
        };
    }

    public function registerPropertyFactory(Container $container)
    {
        $container['property/factory'] = function (Container $container) {
            return new Factory([
                'resolver_options' => [
                    'prefix' => '\Charcoal\Property\\',
                    'suffix' => 'Property'
                ],
                'arguments' => [[
                    'container' => $container,
                    'logger'    => $container['logger']
                ]]
            ]);
        };
    }

    public function registerModelCollectionLoader(Container $container)
    {
        $container['model/collection/loader'] = function (Container $container) {
            return new \Charcoal\Loader\CollectionLoader([
                'logger' => $container['logger'],
                'cache' => $container['cache']
            ]);
        };
    }
}
