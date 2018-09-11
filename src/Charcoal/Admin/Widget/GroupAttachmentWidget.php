<?php

namespace Charcoal\Admin\Widget;

use InvalidArgumentException;
use RuntimeException;

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
class GroupAttachmentWidget extends AttachmentWidget implements
    FormInterface,
    LayoutAwareInterface
{
    use FormTrait;
    use LayoutAwareTrait;

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
        parent::setData($data);

        $this->addAttachmentGroupFromMetadata();

        return $this;
    }

    /**
     * Load attachments group widget and them as form groups.
     *
     * @param boolean $reload Allows to reload the data.
     * @throws InvalidArgumentException If structureMetadata $data is note defined.
     * @throws RuntimeException If the metadataLoader is not defined.
     * @return void
     */
    protected function addAttachmentGroupFromMetadata($reload = false)
    {
        if ($this->obj() instanceof TemplateableInterface) {
            $this->setControllerIdent($this->obj()->templateIdentClass());
        }

        if ($reload || !$this->isAttachmentMetadataFinalized) {
            $obj                 = $this->obj();
            $controllerInterface = $this->controllerIdent();

            $interfaces = [$this->objType()];

            if ($controllerInterface) {
                array_push($interfaces, $controllerInterface);
            }

            $structureMetadata = $this->createMetadata();

            if (count($interfaces)) {
                $controllerMetadataIdent = sprintf('widget/metadata/%s/%s', $obj->objType(), $obj->id());
                $structureMetadata       = $this->metadataLoader()->load(
                    $controllerMetadataIdent,
                    $structureMetadata,
                    $interfaces
                );
            }

            $attachmentWidgets = $structureMetadata->get('attachments.widgets');
            foreach ((array)$attachmentWidgets as $ident => $metadata) {
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
     * Disable the pill nav if there is only one group.
     *
     * @return boolean
     */
    public function displayPills()
    {
        return $this->numGroups() > 1;
    }
}
