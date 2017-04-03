<?php

namespace Charcoal\Cms\Support\Traits;

// local dependencies
use Charcoal\Cms\Support\Helpers\DateHelper;

// dependencies from `pimple`
use Slim\Exception\ContainerException;

trait DateHelperAwareTrait
{
    /**
     * @var DateHelper $dateHelper
     */
    private $dateHelper;

    /**
     * @return DateHelper
     * @throws ContainerException When a dependency is missing.
     */
    protected function dateHelper()
    {
        if (!$this->dateHelper instanceof DateHelper) {
            throw new ContainerException(sprintf(
                'Missing dependency for %s: %s',
                get_called_class(),
                DateHelper::class
            ));
        }

        return $this->dateHelper;
    }

    /**
     * @param DateHelper $dateHelper The date helper class.
     * @return self
     */
    protected function setDateHelper(DateHelper $dateHelper)
    {
        $this->dateHelper = $dateHelper;

        return $this;
    }
}
