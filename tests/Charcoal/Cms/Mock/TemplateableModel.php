<?php

namespace Charcoal\Tests\Cms\Mock;

// From 'charcoal-core'
use Charcoal\Model\AbstractModel;

// From 'charcoal-cms'
use Charcoal\Cms\TemplateableInterface;
use Charcoal\Cms\TemplateableTrait;

/**
 *
 */
class TemplateableModel extends AbstractModel implements
    TemplateableInterface
{
    use TemplateableTrait;

    /**
     * Insert object in storage.
     *
     * @see    \Charcoal\Source\StorableTrait::preSave() For the "create" Event.
     * @return boolean
     */
    public function preSave()
    {
        $this->saveTemplateOptions();

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
        if ($properties === null || array_search('template_options', $properties)) {
            $this->saveTemplateOptions();
        }

        return parent::preUpdate($properties);
    }
}
