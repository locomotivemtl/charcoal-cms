<?php

namespace Charcoal\Admin\Widget\Cms;

use Pimple\Container;

// From 'charcoal-admin'
use Charcoal\Admin\Widget\TableWidget;

/**
 * The hierarchical table widget displays a collection in a tabular (table) format.
 */
class SectionTableWidget extends TableWidget
{
    use SectionTableTrait;

    /**
     * Inject dependencies from a DI Container.
     *
     * @param  Container $container A dependencies container instance.
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->setSectionFactory($container['cms/section/factory']);
    }
}
