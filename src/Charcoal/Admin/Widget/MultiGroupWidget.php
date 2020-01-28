<?php

namespace Charcoal\Admin\Widget;

use RuntimeException;

// from 'charcoal-admin'
use Charcoal\Admin\Ui\ObjectContainerInterface;
use Charcoal\Admin\Ui\ObjectContainerTrait;
use Charcoal\Admin\AdminWidget;

// form 'charcoal-factory'
use Charcoal\Factory\FactoryInterface;

// from 'charcoal-property'
use Charcoal\Property\ModelStructureProperty;
use Charcoal\Property\PropertyInterface;
use Charcoal\Property\StructureProperty;

// From Pimple
use Pimple\Container;

// From 'charcoal-ui'
use Charcoal\Ui\Form\FormInterface;
use Charcoal\Ui\Form\FormTrait;
use Charcoal\Ui\Layout\LayoutAwareInterface;
use Charcoal\Ui\Layout\LayoutAwareTrait;
use Charcoal\Ui\PrioritizableInterface;

/**
 * Class TemplateAttachmentWidget
 */
class MultiGroupWidget extends AdminWidget implements
    FormInterface,
    LayoutAwareInterface,
    ObjectContainerInterface
{
    use FormTrait;
    use LayoutAwareTrait;
    use ObjectContainerTrait;

    /**
     * The cache of snake-cased words.
     *
     * @var array
     */
    protected static $snakeCache = [];

    /**
     * The cache of camel-cased words.
     *
     * @var array
     */
    protected static $camelCache = [];

    /**
     * @var string
     */
    protected $controllerIdent;

    /**
     * @var FactoryInterface
     */
    protected $widgetFactory;

    /**
     * @var boolean
     */
    protected $isMergingData;

    /**
     * @var array|mixed
     */
    protected $formGroups;

    /**
     * @var array|mixed
     */
    protected $widgetMetadata;

    /**
     * @var array|mixed
     */
    protected $extraFormGroups;

    /**
     * The form group's storage target.
     *
     * @var ModelStructureProperty|null
     */
    protected $storageProperty;

    /**
     * Comparison function used by {@see uasort()}.
     *
     * @param  PrioritizableInterface $a Sortable entity A.
     * @param  PrioritizableInterface $b Sortable entity B.
     * @return integer Sorting value: -1 or 1.
     */
    protected function sortItemsByPriority(
        PrioritizableInterface $a,
        PrioritizableInterface $b
    ) {
        $priorityA = $a->priority();
        $priorityB = $b->priority();

        if ($priorityA === $priorityB) {
            return 0;
        }

        return ($priorityA < $priorityB) ? (-1) : 1;
    }

    /**
     * @param array $data The widget data.
     * @return self
     */
    public function setData(array $data)
    {
        parent::setData($data);

        $this->mergeDataSources($data);

        return $this;
    }

    /**
     * @param  Container $container The DI container.
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->setWidgetFactory($container['widget/factory']);

        // Satisfies FormInterface
        $this->setFormGroupFactory($container['form/group/factory']);

        // Satisfies LayoutAwareInterface dependencies
        $this->setLayoutBuilder($container['layout/builder']);
    }

    /**
     * @return string
     */
    public function defaultGroupType()
    {
        return 'charcoal/admin/widget/form-group/generic';
    }

    /**
     * Retrieve the default data sources (when setting data on an entity).
     *
     * @return string[]
     */
    protected function defaultDataSources()
    {
        return [self::DATA_SOURCE_OBJECT];
    }

    /**
     * Set an widget factory.
     *
     * @param FactoryInterface $factory The factory to create widgets.
     * @return self
     */
    protected function setWidgetFactory(FactoryInterface $factory)
    {
        $this->widgetFactory = $factory;

        return $this;
    }

    /**
     * Retrieve the widget factory.
     *
     * @throws RuntimeException If the widget factory was not previously set.
     * @return FactoryInterface
     */
    public function widgetFactory()
    {
        if (!isset($this->widgetFactory)) {
            throw new RuntimeException(sprintf(
                'Widget Factory is not defined for "%s"',
                get_class($this)
            ));
        }

        return $this->widgetFactory;
    }

    /**
     * @return mixed
     */
    public function formGroups()
    {
        return $this->formGroups;
    }

    /**
     * @param mixed $formGroups FormGroups for MultiGroupWidget.
     * @return self
     */
    public function setFormGroups($formGroups)
    {
        $this->formGroups = $formGroups;

        return $this;
    }

    /**
     * @return array|mixed
     */
    public function widgetMetadata()
    {
        return $this->widgetMetadata;
    }

    /**
     * @param array|mixed $widgetMetadata WidgetMetadata for MultiGroupWidget.
     * @return self
     */
    public function setWidgetMetadata($widgetMetadata)
    {
        if (is_string($widgetMetadata) && $this->view()) {
            $widgetMetadata = $this->view()->renderTemplate($widgetMetadata, $this->obj());

            if ($widgetMetadata !== '') {
                $widgetMetadata = json_decode($widgetMetadata, true);
            } else {
                $widgetMetadata = null;
            }
        }

        $this->widgetMetadata = $widgetMetadata;

        return $this;
    }

    /**
     * Set the form group's storage target.
     *
     * Must be a property of the form's object model that will receive an associative array
     * of the form group's data.
     *
     * @param  string|ModelStructureProperty $propertyIdent The property identifier—or instance—of a storage property.
     * @throws \InvalidArgumentException If the property identifier is not a string.
     * @throws \UnexpectedValueException If a property is invalid.
     * @return self
     */
    public function setStorageProperty($propertyIdent)
    {
        $property = null;
        if ($propertyIdent instanceof PropertyInterface) {
            $property      = $propertyIdent;
            $propertyIdent = $property->ident();
        } elseif (!is_string($propertyIdent)) {
            throw new \InvalidArgumentException(
                'Storage Property identifier must be a string'
            );
        }

        $obj = $this->obj();
        if (!$obj->hasProperty($propertyIdent)) {
            throw new \UnexpectedValueException(sprintf(
                'The "%1$s" property is not defined on [%2$s]',
                $propertyIdent,
                get_class($obj)
            ));
        }

        if ($property === null) {
            $property = $obj->property($propertyIdent);
        }

        if ($property instanceof StructureProperty) {
            $this->storageProperty = $property;
        } else {
            throw new \UnexpectedValueException(sprintf(
                '"%s" [%s] is not a model structure property on [%s].',
                $propertyIdent,
                (is_object($property) ? get_class($property) : gettype($property)),
                (is_object($obj) ? get_class($obj) : gettype($obj))
            ));
        }

        return $this;
    }

    /**
     * Retrieve the form group's storage property master.
     *
     * @throws RuntimeException If the storage property was not previously set.
     * @return ModelStructureProperty
     */
    public function storageProperty()
    {
        if ($this->storageProperty === null) {
            throw new RuntimeException(sprintf(
                'Storage property owner is not defined for "%s"',
                get_class($this)
            ));
        }

        return $this->storageProperty;
    }
}
