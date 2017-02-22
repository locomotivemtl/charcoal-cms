<?php

namespace Charcoal\Cms\ServiceProvider;

// Pimple dependencies
use Charcoal\Cms\Service\Loader\EventLoader;
use Charcoal\Cms\Service\Loader\NewsLoader;
use Charcoal\Cms\Service\Manager\EventManager;
use Charcoal\Cms\Service\Manager\NewsManager;
use Charcoal\Cms\Support\Helpers\DateHelper;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

// Local dependencies
use Charcoal\Cms\Config;

// Cms Loaders
use Charcoal\Cms\Service\Loader\SectionLoader;

// dependencies from `charcoal-core`
use Charcoal\Model\AbstractModel;

// dependencies from `charcoal-cms`
use Charcoal\Cms\SectionInterface;
use Charcoal\Cms\Config\CmsConfig;

// dependencies from `charcoal-factory`
use Charcoal\Factory\GenericFactory;

/**
 * Cms Service Provider
 *
 * Provide the following service to container:
 *
 * - `cms/section/factory`
 */
class CmsServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param \Pimple\Container $container A container instance.
     * @return void
     */
    public function register(Container $container)
    {
        /**
         * @param Container $container
         * @return CmsConfig Website configurations (from cms.json).
         */
        $container['cms/config'] = function (Container $container) {
            $appConfig = $container['config'];
            $cms = $appConfig->get('cms');

            $cmsConfig = new CmsConfig();

            $cmsConfig->addFile(__DIR__.'/../../../../config/cms.json');
            $cmsConfig->setData($cms);

            $config = $cmsConfig->get('config_obj');

            if ($config) {
                $factory = $container['model/factory'];

                $configId = $cmsConfig->get('config_obj_id') ?: 1;

                $model = $factory
                    ->create($config)
                    ->load($configId);

                if (!!$model->id()) {
                    $cmsConfig->addModel($model);
                }
            }

            return $cmsConfig;
        };

        /**
         * @param Container $container Pimple DI Container.
         * @return DateHelper
         */
        $container['date/helper'] = function (Container $container) {

            return new DateHelper([
                'date_formats' => $container['cms/config']->get('date_formats'),
                'time_formats' => $container['cms/config']->get('time_formats'),
                'translator'   => $container['translator']
            ]);
        };

        $this->registerSectionServices($container);
        $this->registerNewsServices($container);
    }

    /**
     * @param Container $container Pimple DI Container.
     * @return void
     */
    private function registerSectionServices(Container $container)
    {
        /**
         * @param Container $container Pimple DI Container.
         * @return GenericFactory
         */
        $container['cms/section/factory'] = function (Container $container) {
            return new GenericFactory([
                'base_class'       => SectionInterface::class,
                'arguments'        => $container['model/factory']->arguments(),
                'resolver_options' => [
                    'suffix' => 'Section'
                ]
            ]);
        };

        /**
         * @param Container $container Pimple DI Container.
         * @return SectionLoader
         */
        $container['cms/section/loader'] = function (Container $container) {
            $sectionLoader = new SectionLoader([
                'loader'        => $container['model/collection/loader'],
                'factory'       => $container['model/factory'],
                'cache'         => $container['cache'],
                'translator'    => $container['translator']
            ]);

            // Cms.json
            $sectionConfig = $container['cms/config']->sectionConfig();

            $sectionLoader->setObjType($sectionConfig->get('objType'));
            $sectionLoader->setBaseSection($sectionConfig->get('baseSection'));
            $sectionLoader->setSectionTypes($sectionConfig->get('sectionTypes'));

            return $sectionLoader;
        };
    }

    /**
     * @param Container $container Pimple DI Container.
     * @return void
     */
    private function registerNewsServices(Container $container)
    {
        /**
         * @param Container $container Pimple DI Container.
         * @return NewsLoader
         */
        $container['cms/news/loader'] = function (Container $container) {
            $newsLoader = new NewsLoader([
                'loader'  => $container['model/collection/loader'],
                'factory' => $container['model/factory'],
                'cache'   => $container['cache'],
                'translator'    => $container['translator']
            ]);

            $newsConfig = $container['cms/config']->newsConfig();

            // Cms.json
            $objType = $newsConfig->get('obj_type');
            $newsLoader->setObjType($objType);

            return $newsLoader;
        };

        /**
         * @param Container $container
         * @return NewsManager
         */
        $container['cms/news/manager'] = function (Container $container) {

            $newsManager = new NewsManager([
                'loader'      => $container['model/collection/loader'],
                'factory'     => $container['model/factory'],
                'news/loader' => $container['cms/news/loader'],
                'cache'       => $container['cache'],
                'cms/config'  => $container['cms/config'],
                'translator'  => $container['translator']
            ]);

            return $newsManager;
        };
    }

    /**
     * @param Container $container Pimple DI Container.
     * @return void
     */
    private function registerEventServices(Container $container)
    {
        /**
         * @param Container $container Pimple DI Container.
         * @return EventLoader
         */
        $container['cms/event/loader'] = function (Container $container) {
            $eventLoader = new EventLoader([
                'loader'        => $container['model/collection/loader'],
                'factory'       => $container['model/factory'],
                'cache'         => $container['cache'],
                'translator'    => $container['translator']
            ]);

            $eventConfig = $container['cms/config']->eventConfig();

            // Cms.json
            $objType = $eventConfig->get('obj_type');
            $eventLoader->setObjType($objType);

            return $eventLoader;
        };

        /**
         * @param Container $container
         * @return EventManager
         */
        $container['cms/event/manager'] = function (Container $container) {

            $eventManager = new EventManager([
                'loader'       => $container['model/collection/loader'],
                'factory'      => $container['model/factory'],
                'event/loader' => $container['cms/event/loader'],
                'cache'        => $container['cache'],
                'cms/config'   => $container['cms/config'],
                'translator'   => $container['translator']
            ]);

            return $eventManager;
        };
    }
}
