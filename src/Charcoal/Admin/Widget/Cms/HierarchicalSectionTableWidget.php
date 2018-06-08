<?php

namespace Charcoal\Admin\Widget\Cms;

// From Pimple
use Pimple\Container;

// From 'charcoal-core'
use Charcoal\Model\ModelInterface;

// From 'charcoal-property'
use Charcoal\Property\PropertyInterface;

// From 'charcoal-object'
use Charcoal\Object\HierarchicalCollection;

// From 'charcoal-admin'
use Charcoal\Admin\Property\Display\HierarchicalDisplay;
use Charcoal\Admin\Widget\TableWidget;

/**
 * The hierarchical table widget displays a collection in a tabular (table) format.
 */
class HierarchicalSectionTableWidget extends TableWidget
{
    use SectionTableTrait;

    /**
     * Provide a template to fullfill UIItem interface.
     *
     * @return string
     */
    public function template()
    {
        return 'charcoal/admin/widget/table';
    }

    /**
     * Sort the objects before they are displayed as rows.
     *
     * @see \Charcoal\Admin\Ui\CollectionContainerTrait::sortObjects()
     * @return array
     */
    public function sortObjects()
    {
        $collection = new HierarchicalCollection($this->objects(), false);
        $collection
            ->setPage($this->page())
            ->setNumPerPage($this->numPerPage())
            ->sortTree();

        return $collection->all();
    }

    /**
     * Inject dependencies from a DI Container.
     *
     * @param  Container $container A dependencies container instance.
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->setSectionFactory($container['cms/section/factory']);
    }

    /**
     * Setup the property's display value before its assigned to the object row.
     *
     * This method is useful for classes using this trait.
     *
     * @param  ModelInterface    $object   The current row's object.
     * @param  PropertyInterface $property The current property.
     * @return void
     */
    protected function setupDisplayPropertyValue(
        ModelInterface $object,
        PropertyInterface $property
    ) {
        parent::setupDisplayPropertyValue($object, $property);

        if ($this->display instanceof HierarchicalDisplay) {
            $this->display->setCurrentLevel($object->hierarchyLevel());
        }
    }
}
