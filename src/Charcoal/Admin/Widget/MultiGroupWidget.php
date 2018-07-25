<?php

namespace Charcoal\Admin\Widget;

use Charcoal\Admin\Ui\ObjectContainerInterface;
use Charcoal\Admin\Ui\ObjectContainerTrait;
use Charcoal\Factory\FactoryInterface;
use InvalidArgumentException;
use RuntimeException;

use Charcoal\Admin\AdminWidget;

// From Pimple
use Pimple\Container;

// From 'charcoal-core'
use Charcoal\Model\MetadataInterface;
use Charcoal\Model\Service\MetadataLoader;

// From 'charcoal-property'
use Charcoal\Property\Structure\StructureMetadata;

// From 'charcoal-ui'
use Charcoal\Ui\Form\FormInterface;
use Charcoal\Ui\Form\FormTrait;
use Charcoal\Ui\Layout\LayoutAwareInterface;
use Charcoal\Ui\Layout\LayoutAwareTrait;
use Charcoal\Ui\PrioritizableInterface;

// From 'charcoal-cms'
use Charcoal\Cms\TemplateableInterface;

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
     * Store the metadata loader instance.
     *
     * @var MetadataLoader
     */
    private $metadataLoader;

    /**
     * @var boolean
     */
    protected $isAttachmentMetadataFinalized;

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
     * @var FormWidget
     */
    protected $form;

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
     * @param  Container $container The DI container.
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->setWidgetFactory($container['widget/factory']);

        // Satisfies FormInterface
        $this->setFormGroupFactory($container['form/group/factory']);
        $this->setMetadataLoader($container['metadata/loader']);
    }

    /**
     * Set a metadata loader.
     *
     * @param  MetadataLoader $loader The loader instance, used to load metadata.
     * @return self
     */
    protected function setMetadataLoader(MetadataLoader $loader)
    {
        $this->metadataLoader = $loader;

        return $this;
    }

    /**
     * Retrieve the metadata loader.
     *
     * @throws RuntimeException If the metadata loader was not previously set.
     * @return MetadataLoader
     */
    protected function metadataLoader()
    {
        if ($this->metadataLoader === null) {
            throw new RuntimeException(sprintf(
                'Metadata Loader is not defined for "%s"',
                \get_class($this)
            ));
        }

        return $this->metadataLoader;
    }

    /**
     * Load a metadata file.
     *
     * @param  string $metadataIdent A metadata file path or namespace.
     * @return MetadataInterface
     */
    protected function loadMetadata($metadataIdent)
    {
        $metadataLoader = $this->metadataLoader();
        $metadata       = $metadataLoader->load($metadataIdent, $this->createMetadata());

        return $metadata;
    }

    /**
     * @throws InvalidArgumentException If structureMetadata $data is note defined.
     * @return MetadataInterface
     */
    protected function createMetadata()
    {
        return new StructureMetadata();
    }

    /**
     * Sets data on this widget.
     *
     * @param  array $data Key-value array of data to append.
     * @return self
     */
    public function setData(array $data)
    {
        $this->isMergingData = true;
        /**
         * @todo Kinda hacky, but works with the concept of form.
         *     Should work embeded in a form group or in a dashboard.
         */
        $data = array_merge($_GET, $data);

        parent::setData($data);

        /** Merge any available presets */
        // $data = $this->mergePresets($data);

        // parent::setData($data);

        $this->addGroupFromMetadata();

        $this->isMergingData = false;

        return $this;
    }

    /**
     * @return string
     */
    public function defaultGroupType()
    {
        return 'charcoal/admin/widget/form-group/generic';
    }

    /**
     * Load attachments group widget and them as form groups.
     *
     * @param boolean $reload Allows to reload the data.
     * @throws InvalidArgumentException If structureMetadata $data is note defined.
     * @throws RuntimeException If the metadataLoader is not defined.
     * @return void
     */
    protected function addGroupFromMetadata($reload = false)
    {
        if ($reload || !$this->isAttachmentMetadataFinalized) {
            $obj                 = $this->obj();
            $formGroupsMetadata  = $obj->metadata()->get('admin.form_groups');

            foreach ($this->formGroups() as $ident => $metadata) {
                if (isset($formGroupsMetadata[$ident])) {
                    $metadata = array_replace_recursive($formGroupsMetadata[$ident], $metadata);
                }

                $this->addGroup($ident, $metadata);
            }

            $this->isAttachmentMetadataFinalized = true;
        }
    }

    /**
     * Set the form object's template controller identifier.
     *
     * @param  mixed $ident The template controller identifier.
     * @return self
     */
    public function setControllerIdent($ident)
    {
        if (class_exists($ident)) {
            $this->controllerIdent = $ident;

            return $this;
        }

        if (substr($ident, -9) !== '-template') {
            $ident .= '-template';
        }

        $this->controllerIdent = $ident;

        return $this;
    }

    /**
     * Retrieve the form object's template controller identifier.
     *
     * @return mixed
     */
    public function controllerIdent()
    {
        return $this->controllerIdent;
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
     * @return FormWidget
     */
    public function form()
    {
        return $this->form;
    }

    /**
     * @param FormInterface $form Form for MultiGroupWidget.
     * @return self
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;

        return $this;
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
     * So that the formTrait can access the current From widget.
     *
     * @return FormWidget
     */
    protected function formWidget()
    {
        return $this->form();
    }

    /**
     * Makes the form properties accessible from the object.
     *
     * @return \Charcoal\Property\PropertyInterface[]
     */
    public function formProperties()
    {
        return $this->obj()->properties();
    }

    /**
     * Retrieve the widget's data options for JavaScript components.
     *
     * @return array
     */
    public function widgetDataForJs()
    {
        return [
            'conditional_liaisons' => $this->widgetId()
        ];
    }
}
