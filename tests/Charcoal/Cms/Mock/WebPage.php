<?php

namespace Charcoal\Tests\Cms\Mock;

// From 'charcoal-cms'
use Charcoal\Cms\AbstractSection;

/**
 *
 */
class WebPage extends AbstractSection
{
    /**
     * Insert object in storage.
     *
     * @return boolean
     */
    public function preSave()
    {
        $this->generateDefaultMetaTags();

        return parent::preSave();
    }

    /**
     * Update object in storage.
     *
     * @param  array $properties Optional. The list of properties to update.
     * @return boolean
     */
    public function preUpdate(array $properties = null)
    {
        $this->generateDefaultMetaTags();

        return parent::preUpdate($properties);
    }
}
