<?php

namespace Charcoal\Admin\Property\Input\Selectize;

use InvalidArgumentException;

// From 'charcoal-admin'
use Charcoal\Admin\Property\Input\SelectizeInput;

/**
 * Relation Input Selectize
 */
class RelationInput extends SelectizeInput
{
    /**
     * The target object type to build the choices from.
     *
     * @var string
     */
    private $targetObjectType;

    /**
     * Check used to parse multi Choice map against the obj properties.
     *
     * @var boolean
     */
    protected $isChoiceObjMapFinalized = false;

    /**
     * Retrieve the target object type to build the choices from.
     *
     * @throws InvalidArgumentException If the target object type was not previously set.
     * @return string
     */
    public function targetObjectType()
    {
        if ($this->targetObjectType === null) {
            $resolved = false;
            if (!$this->resolveTargetObjectType()) {
                throw new InvalidArgumentException(
                    'Target object type could not be properly determined.'
                );
            }
        }

        return $this->targetObjectType;
    }

    /**
     * Resolve the target object type from multiple sources & contexts
     *
     * @return boolean
     */
    private function resolveTargetObjectType()
    {
        $resolved = false;
        $formData = $this->viewController()->formData();
        $param = isset($_GET['target_object_type']) ? $_GET['target_object_type'] : false;

        // Resolving through formData should be the most common occurence for this property input
        if (isset($formData['target_object_type']) &&
            is_string($formData['target_object_type']) &&
            !empty($formData['target_object_type'])
        ) {
            $this->targetObjectType = $formData['target_object_type'];
            $resolved = true;
        // Resolve through URL params
        } elseif (is_string($param) && !empty($param)) {
            $this->targetObjectType = $param;
            $resolved = true;
        }

        return $resolved;
    }

    /**
     * Retrieve the object-to-choice data map.
     *
     * @return array Returns a data map to abide.
     */
    public function choiceObjMap()
    {
        if ($this->choiceObjMap === null) {
            $map = $this->defaultChoiceObjMap();

            $model = $this->modelFactory()->get($this->targetObjectType());
            $objProperties = $model->properties();

            if ($objProperties instanceof \Iterator) {
                $objProperties = iterator_to_array($objProperties);
            }

            foreach ($map as &$mapProp) {
                $props = explode(':', $mapProp);
                foreach ($props as $p) {
                    if (isset($objProperties[$p])) {
                        $mapProp = $p;
                        break;
                    }
                }
            }

            $this->choiceObjMap = $map;
        }

        return $this->choiceObjMap;
    }
}
