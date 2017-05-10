<?php

namespace Charcoal\Admin\Widget;

use ArrayIterator;
use RuntimeException;
use InvalidArgumentException;

// From Pimple
use Pimple\Container;

// From 'bobthecow/mustache.php'
use Mustache_LambdaHelper as LambdaHelper;

// From 'charcoal-config'
use Charcoal\Config\ConfigurableInterface;

// From 'charcoal-factory'
use Charcoal\Factory\FactoryInterface;

// From 'charcoal-admin'
use Charcoal\Admin\AdminWidget;
use Charcoal\Admin\Ui\ObjectContainerInterface;
use Charcoal\Admin\Ui\ObjectContainerTrait;

// From 'charcoal-cms'
use Charcoal\Relation\Traits\ConfigurableRelationTrait;

/**
 * The widget for displaying relations as Pivots.
 */
class RelationWidget extends AdminWidget implements
    ConfigurableInterface,
    ObjectContainerInterface
{
    use ConfigurableRelationTrait;
    use ObjectContainerTrait {
        ObjectContainerTrait::createOrLoadObj as createOrCloneOrLoadObj;
    }

    /**
     * The widget's title.
     *
     * @var string[]
     */
    private $title;

    /**
     * The object type identifier.
     *
     * @var string
     */
    // protected $sourceObjectType;

    /**
     * The Pivot target object types.
     *
     * @var array
     */
    protected $targetObjectTypes;

    /**
     * The Pivot grouping ident.
     *
     * @var string
     */
    protected $group;

    /**
     * Track the state of data merging.
     *
     * @var boolean
     */
    private $isMergingData = false;

    /**
     * Store the factory instance for the current class.
     *
     * @var FactoryInterface
     */
    private $widgetFactory;

    /**
     * Label for the relation dialog.
     *
     * @var \Charcoal\Translator\Translation|string|null
     */
    private $dialogTitle;

    /**
     * Inject dependencies from a DI Container.
     *
     * @param Container $container A dependencies container instance.
     * @return void
     */
    public function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->setModelFactory($container['model/factory']);
        $this->setWidgetFactory($container['widget/factory']);
    }

    /**
     * Retrieve the widget factory.
     *
     * @throws Exception If the widget factory was not previously set.
     * @return FactoryInterface
     */
    public function widgetFactory()
    {
        if (!isset($this->widgetFactory)) {
            throw new RuntimeException(
                sprintf('Widget Factory is not defined for "%s"', get_class($this))
            );
        }

        return $this->widgetFactory;
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
     * Retrieve the widget's Pivot grouping.
     *
     * @return string
     */
    public function group()
    {
        return $this->group;
    }

    /**
     * Set the widget's Pivot grouping.
     *
     * @param string $group The object group.
     * @return self
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Retrieve the widget's Pivot target object types.
     *
     * @return array
     */
    public function targetObjectTypes()
    {
        return $this->targetObjectTypes;
    }

    /**
     * Set the widget's available object types.
     *
     * Use the object ident as a key to which you can add filters, label and orders.
     *
     * @param  array        $objectTypes A list of available object types.
     * @return self|boolean
     */
    public function setTargetObjectTypes($objectTypes)
    {
        if (!$this->isMergingData) {
            $objectTypes = $this->mergePresetTargetObjectTypes($objectTypes);
        }

        if (empty($objectTypes) || is_string($objectTypes)) {
            return false;
        }

        $out = [];
        foreach ($objectTypes as $type => $metadata) {
            $label      = '';
            $filters    = [];
            $orders     = [];
            $numPerPage = 0;
            $page       = 1;
            $options    = [ 'label', 'filters', 'orders', 'num_per_page', 'page' ];
            $data       = array_diff_key($metadata, $options);

            // Disable a linked model
            if (isset($metadata['active']) && !$metadata['active']) {
                continue;
            }

            // Useful for replacing a pre-defined object type
            if (isset($metadata['object_type'])) {
                $type = $metadata['object_type'];
            } else {
                $metadata['object_type'] = $type;
            }

            // Useful for linking a pre-existing object
            $objId = (isset($metadata['obj_id']) ? $metadata['obj_id'] : null);

            if (isset($metadata['label'])) {
                $label = $this->translator()->translation($metadata['label']);
            }

            if (isset($metadata['filters'])) {
                $filters = $metadata['filters'];
            }

            if (isset($metadata['orders'])) {
                $orders = $metadata['orders'];
            }

            if (isset($metadata['num_per_page'])) {
                $numPerPage = $metadata['num_per_page'];
            }

            if (isset($metadata['page'])) {
                $page = $metadata['page'];
            }

            $out[$type] = [
                'objId'     => $objId,
                'label'      => $label,
                'filters'    => $filters,
                'orders'     => $orders,
                'page'       => $page,
                'numPerPage' => $numPerPage,
                'data'       => $data
            ];
        }

        $this->targetObjectTypes = $out;

        return $this;
    }

    /**
     * Formatted object types for use in templates.
     *
     * @return array
     */
    public function objectTypes()
    {
        $targetObjectTypes = $this->targetObjectTypes();

        $out = [];

        if (!$targetObjectTypes) {
            return $out;
        }

        $i = 0;
        foreach ($targetObjectTypes as $type => $metadata) {
            $i++;
            $label = $metadata['label'];

            $out[] = [
                'id'     => (isset($metadata['object_id']) ? $metadata['object_id'] : null),
                'ident'  => $this->createIdent($type),
                'label'  => $label,
                'val'    => $type,
                'active' => ($i == 1)
            ];
        }

        return $out;
    }

    /**
     * Set the widget's data.
     *
     * @param array|Traversable $data The widget data.
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
        $data = $this->mergePresets($data);

        parent::setData($data);

        $this->isMergingData = false;

        return $this;
    }

    /**
     * Set the current page listing of relations.
     *
     * @param integer $page The current page. Start at 0.
     * @throws InvalidArgumentException If the parameter is not numeric or < 0.
     * @return self
     */
    public function setPage($page)
    {
        if (!is_numeric($page)) {
            throw new InvalidArgumentException(
                'Page number needs to be numeric.'
            );
        }

        $page = (int)$page;

        if ($page < 0) {
            throw new InvalidArgumentException(
                'Page number needs to be >= 0.'
            );
        }

        $this->page = $page;

        return $this;
    }

    /**
     * Retrieve the widget's title.
     *
     * @return Translation|string[]
     */
    public function title()
    {
        return $this->title;
    }

    /**
     * Set the widget's title.
     *
     * @param mixed $title The title for the current widget.
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $this->translator()->translation($title);

        return $this;
    }

    /**
     * Retrieve the title for the relation dialog.
     *
     * @return \Charcoal\Translator\Translation|string|null
     */
    public function dialogTitle()
    {
        if ($this->dialogTitle === null) {
            $this->setDialogTitle($this->defaultDialogTitle());
        }

        return $this->dialogTitle;
    }

    /**
     * Set the title for the relation dialog.
     *
     * @param  string|string[] $title The dialog title.
     * @return self
     */
    public function relationlogTitle($title)
    {
        $this->dialogTitle = $this->translator()->translation($title);

        return $this;
    }

    /**
     * Retrieve the default title for the relation dialog.
     *
     * @return \Charcoal\Translator\Translation|string|null
     */
    protected function defaultDialogTitle()
    {
        return $this->translator()->translation('Link an object');
    }

    /**
     * Create or load the object.
     *
     * @return ModelInterface
     */
    protected function createOrLoadObj()
    {
        $obj = $this->createOrCloneOrLoadObj();

        $obj->setData([
            'relation_widget' => $this
        ]);

        return $obj;
    }

    /**
     * Relations by object type.
     *
     * @return Collection
     */
    public function relations()
    {
        $relations = $this->obj()->pivots($this->group());

        foreach ($relations as $relation) {
            yield $relation;
        }
    }

    /**
     * Determine the number of relations.
     *
     * @return boolean
     */
    public function hasRelations()
    {
        return count(iterator_to_array($this->relations()));
    }

    /**
     * Retrieves a Closure that prepends relative URIs with the project's base URI.
     *
     * @return callable
     */
    public function withBaseUrl()
    {
        static $search;

        if ($search === null) {
            $attr = [ 'href', 'link', 'url', 'src' ];
            $uri  = [ '../', './', '/', 'data', 'fax', 'file', 'ftp', 'geo', 'http', 'mailto', 'sip', 'tag', 'tel', 'urn' ];

            $search = sprintf(
                '(?<=%1$s=["\'])(?!%2$s)(\S+)(?=["\'])',
                implode('=["\']|', array_map('preg_quote', $attr)),
                implode('|', array_map('preg_quote', $uri))
            );
        }

        /**
         * Prepend the project's base URI to all relative URIs in HTML attributes (e.g., src, href).
         *
         * @param  string       $text   Text to parse.
         * @param  LambdaHelper $helper For rendering strings in the current context.
         * @return string
         */
        $lambda = function ($text, LambdaHelper $helper) use ($search) {
            $text = $helper->render($text);

            if (preg_match('~'.$search.'~i', $text)) {
                $base = $helper->render('{{ baseUrl }}');
                return preg_replace('~'.$search.'~i', $base.'$1', $text);
            } elseif ($this->baseUrl instanceof \Psr\Http\Message\UriInterface) {
                if ($text && strpos($text, ':') === false && !in_array($text[0], [ '/', '#', '?' ])) {
                    return $this->baseUrl->withPath($text);
                }
            }

            return $text;
        };
        $lambda = $lambda->bindTo($this);

        return $lambda;
    }

    /**
     * Set how many relations are displayed per page.
     *
     * @param integer $num The number of results to retrieve, per page.
     * @throws InvalidArgumentException If the parameter is not numeric or < 0.
     * @return self
     */
    public function setNumPerPage($num)
    {
        if (!is_numeric($num)) {
            throw new InvalidArgumentException(
                'Num-per-page needs to be numeric.'
            );
        }

        $num = (int)$num;

        if ($num < 0) {
            throw new InvalidArgumentException(
                'Num-per-page needs to be >= 0.'
            );
        }

        $this->numPerPage = $num;

        return $this;
    }

    /**
     * Retrieve the current widget's options as a JSON object.
     *
     * @return string A JSON string.
     */
    public function widgetOptions()
    {
        $options = [
            'target_object_types' => $this->targetObjectTypes(),
            'title'               => $this->title(),
            'obj_type'            => $this->obj()->objType(),
            'obj_id'              => $this->obj()->id(),
            'group'               => $this->group()
        ];

        return json_encode($options, true);
    }

    /**
     * Determine if the widget has an object assigned to it.
     *
     * @return boolean
     */
    public function hasObj()
    {
        return !!($this->obj()->id());
    }

    /**
     * Generate an HTML-friendly identifier.
     *
     * @param  string $string A dirty string to filter.
     * @return string
     */
    public function createIdent($string)
    {
        return preg_replace('~/~', '-', $string);
    }

    /**
     * Parse the given data and recursively merge presets from RelationConfig.
     *
     * @param  array $data The widget data.
     * @return array Returns the merged widget data.
     */
    protected function mergePresets(array $data)
    {
        if (isset($data['target_object_types'])) {
            $data['target_object_types'] = $this->mergePresetTargetObjectTypes($data['target_object_types']);
        }

        if (isset($data['preset'])) {
            $data = $this->mergePresetWidget($data);
        }

        return $data;
    }

    /**
     * Parse the given data and merge the widget preset.
     *
     * @param  array $data The widget data.
     * @return array Returns the merged widget data.
     */
    private function mergePresetWidget(array $data)
    {
        if (!isset($data['preset']) || !is_string($data['preset'])) {
            return $data;
        }

        $widgetIdent = $data['preset'];
        if ($this->hasObj()) {
            $widgetIdent = $this->obj()->render($widgetIdent);
        }

        $presetWidgets = $this->config('widgets');
        if (!isset($presetWidgets[$widgetIdent])) {
            return $data;
        }

        $widgetData = $presetWidgets[$widgetIdent];
        if (isset($widgetData['target_object_types'])) {
            $widgetData['target_object_types'] = $this->mergePresetTargetObjectTypes($widgetData['target_object_types']);
        }

        return array_replace_recursive($widgetData, $data);
    }
}
