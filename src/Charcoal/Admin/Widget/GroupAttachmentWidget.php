<?php

namespace Charcoal\Admin\Widget;

use Charcoal\Admin\Widget\AttachmentWidget;
use Charcoal\App\Template\TemplateInterface;
use Charcoal\Cms\TemplateableInterface;
use Charcoal\Model\ModelInterface;
use Charcoal\Model\Service\MetadataLoader;
use Charcoal\Property\Structure\StructureMetadata;
use Charcoal\Ui\Form\FormInterface;
use Charcoal\Ui\Form\FormTrait;
use Charcoal\Ui\Layout\LayoutAwareInterface;
use Charcoal\Ui\Layout\LayoutAwareTrait;
use Charcoal\Ui\PrioritizableInterface;
use Pimple\Container;

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
                get_class($this)
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
     * @return MetadataInterface
     */
    protected function createMetadata()
    {
        return new StructureMetadata();
    }

    /**
     * @inheritDoc
     */
    public function setData(array $data)
    {
        parent::setData($data);

        $this->addAttachmentGroupFromMetadata();
    }

    protected function addAttachmentGroupFromMetadata($reload = false)
    {
        if ($this->obj() instanceof TemplateableInterface) {
            $this->setControllerIdent($this->obj()->templateIdentClass());
        }

        if ($reload || !$this->isAttachmentMetadataFinalized) {
            $obj = $this->obj();
            $objectMetadata = $obj->metadata();
            $controllerInterface = $this->controllerIdent();

            $interfaces = [];
            array_push($interfaces, $this->objType(), $controllerInterface);

            // error_log(var_export($objectMetadata, true));
            $structureMetadata   = new StructureMetadata();
            if (count($interfaces)) {
                $controllerMetadataIdent = sprintf('widget/metadata/%s/%s', $obj->objType(), $obj->id());
                $structureMetadata = $this->metadataLoader()->load(
                    $controllerMetadataIdent,
                    $structureMetadata,
                    $interfaces
                );
            }

            $attachmentWidgets = $structureMetadata->get('attachments.widgets');
            foreach ($attachmentWidgets as $ident => $metadata) {
                $this->addGroup($ident, $metadata);
            }

            $this->isAttachmentMetadataFinalized = true;
        }
    }

    /**
     * Set the form object's template controller identifier.
     *
     * @param  mixed $ident The template controller identifier.
     * @return TemplateableInterface Chainable
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
     * Convert a snake-cased namespace to CamelCase.
     *
     * @param  string $ident The namespace to convert.
     * @return string Returns a valid PHP namespace.
     */
    private function identToClassname($ident)
    {
        $key = $ident;

        if (isset(static::$camelCache[$key])) {
            return static::$camelCache[$key];
        }

        // Change "foo-bar" to "fooBar"
        $parts = explode('-', $ident);
        array_walk(
            $parts,
            function (&$i) {
                $i = ucfirst($i);
            }
        );
        $ident = implode('', $parts);

        // Change "/foo/bar" to "\Foo\Bar"
        $classname = str_replace('/', '\\', $ident);
        $parts     = explode('\\', $classname);

        array_walk(
            $parts,
            function (&$i) {
                $i = ucfirst($i);
            }
        );

        $classname = trim(implode('\\', $parts), '\\');

        static::$camelCache[$key]       = $classname;
        static::$snakeCache[$classname] = $key;

        return $classname;
    }
}
