<?php

namespace Charcoal\Property;

use InvalidArgumentException;

// From 'charcoal-property'
use Charcoal\Property\PropertyInterface;
use Charcoal\Property\StructureProperty;
use Charcoal\Property\TemplateProperty;

/**
 * Template Options Property
 */
class TemplateOptionsProperty extends StructureProperty
{
    /**
     * Retrieve the property's type identifier.
     *
     * @return string
     */
    public function type()
    {
        return 'template-options';
    }

    /**
     * Add the given metadata interfaces for the property to use as a structure.
     *
     * @see    StructureProperty::addStructureInterface()
     * @param  string $interface A metadata interface to use.
     * @throws InvalidArgumentException If the template property value is invalid.
     * @return TemplateOptionsProperty
     */
    public function addStructureInterface($interface)
    {
        if ($interface instanceof TemplateProperty) {
            $choice = $interface->choice($interface->val());
            if (isset($choice['controller'])) {
                $interface = $choice['controller'];
            } elseif (isset($choice['template'])) {
                $interface = $choice['template'];
            } else {
                throw new InvalidArgumentException(
                    'Template structure interface invalid'
                );
            }
        }

        return parent::addStructureInterface($interface);
    }
}
