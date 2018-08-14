<?php

namespace Charcoal\Admin\Widget\FormGroup;

// from 'charcoal-admin'
use Charcoal\Admin\Widget\FormPropertyWidget;
use Charcoal\Admin\Widget\MultiGroupWidget;

// from 'charcoal-ui'
use Charcoal\Ui\Form\FormInterface;
use Charcoal\Ui\FormGroup\FormGroupInterface;
use Charcoal\Ui\FormGroup\FormGroupTrait;
use Charcoal\Ui\UiItemInterface;
use Charcoal\Ui\UiItemTrait;

/**
 * Class NestedFormGroup
 */
class MultiGroupFormGroup extends MultiGroupWidget implements
    FormGroupInterface,
    UiItemInterface
{
    use FormGroupTrait;
    use UiItemTrait;

    /**
     * @return string
     */
    public function template()
    {
        return 'charcoal/admin/widget/multi-group';
    }

    /**
     * @return array
     */
    public function dataFromObject()
    {
        $obj         = $this->obj();
        $objMetadata = $obj->metadata();

        $adminMetadata   = (isset($objMetadata['admin']) ? $objMetadata['admin'] : null);
        $adminFormGroups = (isset($adminMetadata['form_groups']) ? $adminMetadata['form_groups'] : null);

        $groups = $this->groups();

        if (!$adminFormGroups) {
            return [];
        }

        foreach ($groups as $group) {
            if ($adminFormGroups && isset($adminFormGroups[$group->ident()])) {
                $metadata = array_replace_recursive($adminFormGroups[$group->ident()], $group->data());

                $this->updateFormGroup($group, $metadata);
            }
        }

        return [];
    }

    /**
     * @return $this|array
     * @throws \Exception If the model factory was not set before being accessed.
     */
    public function dataFromMetadata()
    {
        $data = $this->widgetMetadata();

        if (!$data) {
            return [];
        }

        $adminMetadata   = (isset($data['admin']) ? $data['admin'] : null);
        $adminFormGroups = (isset($adminMetadata['form_groups']) ? $adminMetadata['form_groups'] : null);

        if (isset($data['groups']) && isset($adminFormGroups)) {
            $extraFormGroups = array_intersect(
                array_keys($adminFormGroups),
                array_keys($data['groups'])
            );
            foreach ($extraFormGroups as $groupIdent) {
                $data['groups'][$groupIdent] = array_replace_recursive(
                    $adminFormGroups[$groupIdent],
                    $data['groups'][$groupIdent]
                );
            }
        }

        foreach ($data['properties'] as $ident => $property) {
            $this->form()->getOrCreateFormProperty($ident, $property);
        }

        return $data;
    }

    /**
     * Retrieve the object's properties as form controls.
     *
     * @param  array $group An optional group to use.
     * @throws \UnexpectedValueException If a property data is invalid.
     * @return FormPropertyWidget[]
     */
    public function formProperties(array $group = null)
    {
        if (!key_exists(self::DATA_SOURCE_METADATA, array_flip($this->dataSources())) ||
            !$this->widgetMetadata()) {
            return iterator_to_array($this->form()->formProperties());
        }

        $propertyIdentPattern = '%1$s[%2$s]';
        $entry                = null;

        try {
            $store = $this->storageProperty();
        } catch (\Exception $e) {
            $store = null;
        }

        if ($store) {
            $entry = $this->obj()[$store->ident()];
            if (is_string($entry)) {
                $entry = $store->parseVal($entry);
            }
        }

        $props = $this->widgetMetadata()['properties'];

        // We need to sort form properties by form group property order if a group exists
        if (!empty($group)) {
            $props = array_merge(array_flip($group), $props);
        }

        $out = [];

        foreach ($props as $propertyIdent => $propertyMetadata) {
            if (!is_array($propertyMetadata)) {
                throw new \UnexpectedValueException(sprintf(
                    'Invalid property data for "%1$s", received %2$s',
                    $propertyIdent,
                    (is_object($propertyMetadata) ? get_class($propertyMetadata) : gettype($propertyMetadata))
                ));
            }

            if ($store) {
                $propertyMetadata['input_name'] = sprintf(
                    $propertyIdentPattern,
                    $store['input_name'] ?: $store->ident(),
                    $propertyIdent
                );
            }

            if (!empty($entry) && isset($entry[$propertyIdent])) {
                $val = $entry[$propertyIdent];

                $propertyMetadata['property_val'] = $val;
            }

            $formProperty = $this->form()->getOrCreateFormProperty($propertyIdent, $propertyMetadata);

            if (!$formProperty->hidden()) {
                $out[$propertyIdent] = $formProperty;
            }
        }

        return $out;
    }

    /**
     * So that the formTrait can access the current From widget.
     *
     * @return FormInterface|self
     */
    protected function formWidget()
    {
        if (!key_exists(self::DATA_SOURCE_METADATA, array_flip($this->dataSources()))) {
            return $this->form();
        }

        return $this;
    }
}
