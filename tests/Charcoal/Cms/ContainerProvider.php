<?php

namespace Charcoal\Tests\Cms;

use PDO;

// From PSR-3
use Psr\Log\NullLogger;

// From 'cache/void-adapter' (PSR-6)
use Cache\Adapter\Void\VoidCachePool;

// From 'tedivm/stash' (PSR-6)
use Stash\Pool;
use Stash\Driver\Ephemeral;

// From 'symfony/translator'
use Symfony\Component\Translation\Loader\ArrayLoader;

// From 'charcoal-factory'
use Charcoal\Factory\GenericFactory as Factory;

// From 'charcoal-core'
use Charcoal\Model\Service\MetadataLoader;
use Charcoal\Loader\CollectionLoader;
use Charcoal\Source\DatabaseSource;

// From 'charcoal-view'
use Charcoal\View\GenericView;
use Charcoal\View\Mustache\MustacheEngine;
use Charcoal\View\Mustache\MustacheLoader;

// From 'charcoal-translator'
use Charcoal\Translator\LocalesManager;
use Charcoal\Translator\Translator;

// From 'charcoal-app'
use Charcoal\App\AppConfig;
use Charcoal\App\AppContainer as Container;

// From 'charcoal-cms'
use Charcoal\Cms\Config\CmsConfig;
use Charcoal\Cms\Support\Helpers\DateHelper;
use Charcoal\Tests\Cms\Mock\GenericTemplate;

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
            return new AppConfig([
                'base_path' => realpath(__DIR__.'/../../..'),
                'view'      => [
                    'default_controller' => GenericTemplate::class
                ],
                'templates' => []
            ]);
        };
    }

    public function registerTemplateDependencies(Container $container)
    {
        $container->extend('config', function (AppConfig $config) {
            $config['templates'] = [
                [
                    'value'  => 'foo',
                    'label'  => [
                        'en' => 'Foofoo',
                        'fr' => 'Oofoof'
                    ],
                    'controller' => 'templateable/foo'
                ],
                [
                    'value'  => 'baz',
                    'label'  => [
                        'en' => 'Bazbaz',
                        'fr' => 'Zabzab'
                    ],
                    'template' => 'templateable/baz'
                ],
                [
                    'value'  => 'qux',
                    'label'  => [
                        'en' => 'Quxqux',
                        'fr' => 'Xuqxuq'
                    ],
                    'class' => 'templateable/qux'
                ],
                [
                    'value'  => 'xyz',
                    'label'  => [
                        'en' => 'Xyzzy',
                        'fr' => 'YzzyX'
                    ]
                ]
            ];

            return $config;
        });
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

    public function registerView(Container $container)
    {
        $container['view/loader'] = function (Container $container) {
            return new MustacheLoader([
                'logger'    => $container['logger'],
                'base_path' => $container['config']['base_path'],
                'paths'     => [
                    'views'
                ]
            ]);
        };

        $container['view/engine'] = function (Container $container) {
            return new MustacheEngine([
                'logger' => $container['logger'],
                'cache'  => MustacheEngine::DEFAULT_CACHE_PATH,
                'loader' => $container['view/loader']
            ]);
        };

        $container['view'] = function (Container $container) {
            return new GenericView([
                'logger' => $container['logger'],
                'engine' => $container['view/engine']
            ]);
        };
    }

    public function registerTranslator(Container $container)
    {
        $container['locales/manager'] = function (Container $container) {
            $manager = new LocalesManager([
                'locales' => [
                    'en' => [ 'locale' => 'en-US' ]
                ]
            ]);

            $manager->setCurrentLocale($manager->currentLocale());

            return $manager;
        };

        $container['translator'] = function (Container $container) {
            return new Translator([
                'manager' => $container['locales/manager']
            ]);
        };
    }

    /**
     * Setup the application's translator service.
     *
     * @param  Container $container A DI container.
     * @return void
     */
    public function registerMultilingualTranslator(Container $container)
    {
        $container['locales/manager'] = function (Container $container) {
            $manager = new LocalesManager([
                'locales' => [
                    'en'  => [
                        'locale' => 'en-US',
                        'name'   => [
                            'en' => 'English',
                            'fr' => 'Anglais',
                            'es' => 'Inglés'
                        ]
                    ],
                    'fr' => [
                        'locale' => 'fr-CA',
                        'name'   => [
                            'en' => 'French',
                            'fr' => 'Français',
                            'es' => 'Francés'
                        ]
                    ],
                    'de' => [
                        'locale' => 'de-DE'
                    ],
                    'es' => [
                        'locale' => 'es-MX'
                    ]
                ],
                'default_language'   => 'en',
                'fallback_languages' => [ 'en' ]
            ]);

            $manager->setCurrentLocale($manager->currentLocale());

            return $manager;
        };

        $container['translator'] = function (Container $container) {
            $translator = new Translator([
                'manager' => $container['locales/manager']
            ]);

            $translator->addLoader('array', new ArrayLoader());
            $translator->addResource('array', [ 'locale.de' => 'German'   ], 'en', 'messages');
            $translator->addResource('array', [ 'locale.de' => 'Allemand' ], 'fr', 'messages');
            $translator->addResource('array', [ 'locale.de' => 'Deutsch'  ], 'es', 'messages');
            $translator->addResource('array', [ 'locale.de' => 'Alemán'   ], 'de', 'messages');

            return $translator;
        };
    }

    public function registerMetadataLoader(Container $container)
    {
        $container['metadata/loader'] = function (Container $container) {
            return new MetadataLoader([
                'logger'    => $container['logger'],
                'base_path' => $container['config']['base_path'],
                'paths'     => [
                    'metadata',
                    'tests/Fixtures/metadata',
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
                'map' => [
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
            return new CollectionLoader([
                'logger'  => $container['logger'],
                'cache'   => $container['cache'],
                'factory' => $container['model/factory']
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
