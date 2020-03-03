<?php

namespace Charcoal\Tests\Cms;

use PDO;

// From PSR-3
use Psr\Log\NullLogger;

// From 'tedivm/stash' (PSR-6)
use Stash\Pool;

// From 'charcoal-factory'
use Charcoal\Factory\GenericFactory as Factory;

// From 'charcoal-core'
use Charcoal\Model\Service\MetadataLoader;
use Charcoal\Loader\CollectionLoader;
use Charcoal\Source\DatabaseSource;
use Charcoal\Model\ServiceProvider\ModelServiceProvider;

// From 'charcoal-user'
use Charcoal\User\ServiceProvider\AuthServiceProvider;

// From 'charcoal-translator'
use Charcoal\Translator\ServiceProvider\TranslatorServiceProvider;

// From 'charcoal-view'
use Charcoal\View\ViewServiceProvider;

// From 'charcoal-app'
use Charcoal\App\AppConfig;
use Charcoal\App\AppContainer as Container;
use Charcoal\App\Template\TemplateInterface;

// From 'charcoal-cms'
use Charcoal\Cms\Config\CmsConfig;
use Charcoal\Cms\Support\Helpers\DateHelper;
use Charcoal\Tests\Cms\Mock\GenericTemplate;

/**
 * Service Container for Unit Tests
 */
class ContainerProvider
{
    /**
     * @param  Container $container A DI container.
     * @return void
     */
    public function registerBaseServices(Container $container)
    {
        $this->registerLogger($container);
        $this->registerCache($container);
        $this->registerCmsConfig($container);
        $this->registerDateHelper($container);
        $this->registerConfig($container);
        $this->withUnilingualConfig($container);
    }

    /**
     * @param  Container $container A DI container.
     * @return void
     */
    public function registerModelDependencies(Container $container)
    {
        $this->registerDatabase($container);
        $this->registerViewServices($container);
        $this->registerModelServices($container);
    }

    /**
     * @param  Container $container A DI container.
     * @return void
     */
    public function registerConfig(Container $container)
    {
        $container['config'] = function () {
            return new AppConfig([
                'base_path' => realpath(__DIR__.'/../../..'),
                'templates' => [],
                'metadata'  => [
                    'paths' => [
                        'metadata',
                        'tests/Charcoal/Cms/Fixture/metadata',
                        'vendor/locomotivemtl/charcoal-object/metadata',
                    ],
                ],
                'view'      => [
                    'paths' => [
                        'views',
                        'tests/Charcoal/Cms/Fixture/views',
                    ],
                    'default_controller' => GenericTemplate::class,
                ],
            ]);
        };
    }

    /**
     * Extend the application configset for a unilingual setup.
     *
     * @param  Container $container A DI container.
     * @return void
     */
    public function withUnilingualConfig(Container $container)
    {
        $container->extend('config', function (AppConfig $config) {
            $config['locales'] = [
                'languages' => [
                    'en' => [
                        'locale' => 'en-US',
                    ],
                ],
                'default_language'   => 'en',
                'fallback_languages' => [ 'en' ],
            ];

            $config['translator'] = [
                'translations' => [
                    'messages' => [
                    ],
                ],
            ];

            return $config;
        });
    }

    /**
     * Extend the application configset for a multilingual setup.
     *
     * @param  Container $container A DI container.
     * @return void
     */
    public function withMultilingualConfig(Container $container)
    {
        $container->extend('config', function (AppConfig $config) {
            $config['locales'] = [
                'languages' => [
                    'en'  => [
                        'locale' => 'en-US',
                        'name'   => [
                            'en' => 'English',
                            'fr' => 'Anglais',
                            'es' => 'Inglés',
                        ],
                    ],
                    'fr' => [
                        'locale' => 'fr-CA',
                        'name'   => [
                            'en' => 'French',
                            'fr' => 'Français',
                            'es' => 'Francés',
                        ],
                    ],
                    'de' => [
                        'locale' => 'de-DE',
                    ],
                    'es' => [
                        'locale' => 'es-MX',
                    ],
                ],
                'default_language'   => 'en',
                'fallback_languages' => [ 'en' ],
            ];

            $config['translator'] = [
                'translations' => [
                    'messages' => [
                        'en' => [
                            'locale.de' => 'German',
                        ],
                        'fr' => [
                            'locale.de' => 'Allemand',
                        ],
                        'es' => [
                            'locale.de' => 'Deutsch',
                        ],
                        'de' => [
                            'locale.de' => 'Alemán',
                        ],
                    ],
                ],
            ];

            return $config;
        });
    }

    /**
     * Extend the application configset with templates.
     *
     * @param  Container $container A DI container.
     * @return void
     */
    public function withTemplatesConfig(Container $container)
    {
        $container->extend('config', function (AppConfig $config) {
            $config['templates'] = [
                [
                    'value'  => 'foo',
                    'label'  => [
                        'en' => 'Foofoo',
                        'fr' => 'Oofoof',
                    ],
                    'controller' => 'templateable/foo',
                ],
                [
                    'value'  => 'baz',
                    'label'  => [
                        'en' => 'Bazbaz',
                        'fr' => 'Zabzab',
                    ],
                    'template' => 'templateable/baz',
                ],
                [
                    'value'  => 'qux',
                    'label'  => [
                        'en' => 'Quxqux',
                        'fr' => 'Xuqxuq',
                    ],
                    'class' => 'templateable/qux',
                ],
                [
                    'value'  => 'xyz',
                    'label'  => [
                        'en' => 'Xyzzy',
                        'fr' => 'YzzyX',
                    ],
                ],
            ];

            return $config;
        });
    }

    /**
     * @param  Container $container A DI container.
     * @return void
     */
    public function registerDatabase(Container $container)
    {
        $container['database'] = function () {
            $pdo = new PDO('sqlite::memory:');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $pdo;
        };
    }

    /**
     * @param  Container $container A DI container.
     * @return void
     */
    public function registerLogger(Container $container)
    {
        $container['logger'] = function () {
            return new NullLogger();
        };
    }

    /**
     * @param  Container $container A DI container.
     * @return void
     */
    public function registerCache(Container $container)
    {
        $container['cache'] = function () {
            return new Pool();
        };
    }

    /**
     * Register the admin services.
     *
     * @param  Container $container A DI container.
     * @return void
     */
    public function registerModelServices(Container $container)
    {
        static $provider = null;

        if ($provider === null) {
            $provider = new ModelServiceProvider();
        }

        $provider->register($container);
    }

    /**
     * Register the admin services.
     *
     * @param  Container $container A DI container.
     * @return void
     */
    public function registerAuthServices(Container $container)
    {
        static $provider = null;

        if ($provider === null) {
            $provider = new AuthServiceProvider();
        }

        $provider->register($container);
    }

    /**
     * Setup the application's translator service.
     *
     * @param  Container $container A DI container.
     * @return void
     */
    public function registerTranslatorServices(Container $container)
    {
        static $provider = null;

        if ($provider === null) {
            $provider = new TranslatorServiceProvider();
        }

        $provider->register($container);
    }

    /**
     * Setup the framework's view renderer.
     *
     * @param  Container $container A DI container.
     * @return void
     */
    public function registerViewServices(Container $container)
    {
        static $provider = null;

        if ($provider === null) {
            $provider = new ViewServiceProvider();
        }

        $provider->register($container);
    }

    /**
     * @param  Container $container A DI container.
     * @return void
     */
    public function registerCmsConfig(Container $container)
    {
        $container['cms/config'] = function () {
            return new CmsConfig();
        };
    }

    /**
     * @param  Container $container A DI container.
     * @return void
     */
    public function registerDateHelper(Container $container)
    {
        $container['date/helper'] = function () {
            return new DateHelper([
                'date_formats' => '',
                'time_formats' => '',
            ]);
        };
    }

    /**
     * @param  Container $container A DI container.
     * @return void
     */
    public function registerTemplateFactory(Container $container)
    {
        $container['template/factory'] = function (Container $container) {
            return new Factory([
                'base_class'       => TemplateInterface::class,
                'resolver_options' => [
                    'suffix' => 'Template',
                ],
                'arguments'        => [
                    [
                        'container' => $container,
                        'logger'    => $container['logger'],
                    ],
                ],
            ]);
        };
    }
}
