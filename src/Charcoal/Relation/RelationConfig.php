<?php

namespace Charcoal\Relation;

use InvalidArgumentException;

// From 'charcoal-config'
use Charcoal\Config\AbstractConfig;

/**
 * Relation Configset
 */
class RelationConfig extends AbstractConfig
{
    /**
     * Available target object types.
     *
     * @var array
     */
    private $targetObjectTypes = [];

    /**
     * Set relation settings in a specific order.
     *
     * @param  array $data New config values.
     * @return RelationConfig Chainable
     */
    public function setData(array $data)
    {
        if (isset($data['targetObjectTypes'])) {
            $this->setTargetObjectTypes($data['targetObjectTypes']);
        }

        unset($data['targetObjectTypes'], $data['groups'], $data['widgets']);

        return parent::setData($data);
    }

    /**
     * Retrieve the available target object types.
     *
     * @return array
     */
    public function targetObjectTypes()
    {
        return $this->targetObjectTypes;
    }

    /**
     * Set target object types.
     *
     * @param  array $targetObjectTypes One or more object types.
     * @throws InvalidArgumentException If the object type or structure is invalid.
     * @return RelationConfig Chainable
     */
    public function setTargetObjectTypes(array $targetObjectTypes)
    {
        foreach ($targetObjectTypes as $type => $struct) {
            if (!is_array($struct)) {
                throw new InvalidArgumentException(sprintf(
                    'The object structure for "%s" must be an array',
                    $type
                ));
            }

            if (isset($struct['object_type'])) {
                $type = $struct['object_type'];
            } else {
                $struct['object_type'] = $type;
            }

            if (!is_string($type)) {
                throw new InvalidArgumentException(
                    'The object type must be a string'
                );
            }

            $this->targetObjectTypes[$type] = $struct;
        }

        return $this;
    }
}
