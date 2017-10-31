<?php

namespace Charcoal\Property;

use InvalidArgumentException;

// From 'charcoal-property'
use Charcoal\Property\ModelStructureProperty;
use Charcoal\Property\TemplateProperty;

/**
 * Template Options Property
 */
class TemplateOptionsProperty extends ModelStructureProperty
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
            $interface = (string)$interface;
            if (empty($interface)) {
                throw new InvalidArgumentException(
                    'Invalid template structure interface'
                );
            }
        }

        return parent::addStructureInterface($interface);
    }
}
