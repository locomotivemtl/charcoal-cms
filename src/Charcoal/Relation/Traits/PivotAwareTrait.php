<?php

namespace Charcoal\Relation\Traits;

use InvalidArgumentException;
use RuntimeException;

// From 'charcoal-core'
use Charcoal\Model\ModelInterface;

// From 'charcoal-cms'
use Charcoal\Admin\Widget\RelationWidget;
use Charcoal\Relation\Interfaces\PivotableInterface;
use Charcoal\Relation\Pivot;

// From 'mcaskill/charcoal-support'
use Charcoal\Support\Model\Collection;

/**
 * Provides support for pivots on objects.
 *
 * Used by source objects that need a pivot to a target object.
 *
 * Abstract methods need to be implemented.
 *
 * Implementation of {@see \Charcoal\Relation\Interfaces\PivotAwareInterface}
 *
 * ## Required Services
 *
 * - 'model/factory' — {@see \Charcoal\Model\ModelFactory}
 * - 'model/collection/loader' — {@see \Charcoal\Loader\CollectionLoader}
 */
trait PivotAwareTrait
{
    /**
     * A store of cached pivots, by ID.
     *
     * @var Pivot[] $pivotCache
     */
    protected static $pivotCache = [];

    /**
     * Store a collection of node objects.
     *
     * @var Collection|Pivot[]
     */
    protected $pivots = [];

    /**
     * Store the widget instance currently displaying relations.
     *
     * @var RelationWidget
     */
    protected $relationWidget;

    /**
     * Retrieve the objects associated to the current object.
     *
     * @param  string|null   $group    Filter the pivots by a group identifier.
     * @param  string|null   $type     Filter the pivots by type.
     * @param  callable|null $callback Optional routine to apply to every object.
     * @throws InvalidArgumentException If the $group or $type is invalid.
     * @return Collection[]
     */
    public function pivots($group = null, $type = null, callable $callback = null)
    {
        if ($group === null) {
            $group = 0;
        } elseif (!is_string($group)) {
            throw new InvalidArgumentException('The $group must be a string.');
        }

        if ($type === null) {
            $type = 0;
        } else {
            if (!is_string($type)) {
                throw new InvalidArgumentException('The $type must be a string.');
            }

            $type = preg_replace('/([a-z])([A-Z])/', '$1-$2', $type);
            $type = strtolower(str_replace('\\', '/', $type));
        }

        if (isset($this->pivots[$group][$type])) {
            return $this->pivots[$group][$type];
        }

        $sourceObjectType = $this->objType();
        $sourceObjectId = $this->id();

        $pivotProto = $this->modelFactory()->get(Pivot::class);
        $pivotTable = $pivotProto->source()->table();

        if (!$pivotProto->source()->tableExists()) {
            return [];
        }

        $widget = $this->relationWidget();
        $targetObjectTypes = $widget->targetObjectTypes();

        if (!is_array($targetObjectTypes) || empty($targetObjectTypes)) {
            throw new RuntimeException('No target object types are set for this object.');
        }

        $cases = [];
        $joins = [];
        $collection = new Collection;
        foreach ($targetObjectTypes as $targetObjectType => $metadata) {
            $parts = explode('/', str_replace('-', '_', $targetObjectType));
            $targetObjectIdent = end($parts).'_obj';

            $targetObjectProto = $this->modelFactory()->get($targetObjectType);
            $targetObjectTable = $targetObjectProto->source()->table();

            if (!$targetObjectProto->source()->tableExists()) {
                continue;
            }

            $query = '
                SELECT
                    target_obj.*,
                    pivot_obj.id as pivotObjId,
                    pivot_obj.position as position
                FROM
                    `'.$targetObjectTable.'` AS target_obj
                LEFT JOIN
                    `'.$pivotTable.'` AS pivot_obj
                ON
                    pivot_obj.target_object_id = target_obj.id
                WHERE
                    1 = 1';

            // Disable `active` check in admin
            if (!$widget instanceof RelationWidget) {
                $query .= '
                AND
                    target_obj.active = 1';
            }

            $query .= '
                AND
                    pivot_obj.source_object_type = "'.$sourceObjectType.'"
                AND
                    pivot_obj.source_object_id = "'.$sourceObjectId.'"
                AND
                    pivot_obj.target_object_type = "'.$targetObjectType.'"';

            if ($group) {
                $query .= '
                AND
                    pivot_obj.group = "'.$group.'"';
            }

            $query .= '
                ORDER BY pivot_obj.position';

            $loader = $this->collectionLoader();
            $loader->setModel($targetObjectProto);

            if ($widget instanceof RelationWidget) {
                $callable = function ($targetObject) use ($pivotProto, $metadata, $callback) {
                    // Set the pivotObjType for Pivot access within the mixed object type list
                    $targetObject->pivotObjType = $pivotProto->objType();

                    if (isset($metadata['data'])) {
                        if ($targetObject instanceof PivotableInterface) {
                            $heading = $targetObject->pivotHeading();
                        } elseif (isset($metadata['label'])) {
                            $heading = $targetObject->render((string)$metadata['label'].' #'.$targetObject->id());
                        }

                        if (!$heading) {
                            $heading = $this->translator()->translation('{{ objType }} #{{ id }}', [
                                '{{ objType }}' => $targetObject->objType(),
                                '{{ id }}'      => $targetObject->id()
                            ]);
                        }

                        $metadata['data']['heading'] = $heading;

                        $targetObject->setData($metadata['data']);
                    }

                    if ($callback !== null) {
                        call_user_func_array($callback, [ &$targetObject ]);
                    }
                };
            } else {
                $callable = function ($targetObject) use ($pivotProto, $callback) {
                    // Set the pivotObjType for Pivot access within the mixed object type list
                    $targetObject->pivotObjType = $pivotProto->objType();

                    if ($callback !== null) {
                        call_user_func_array($callback, [ &$targetObject ]);
                    }
                };
            }

            $loader->setCallback($callable->bindTo($this));

            $targetCollection = $loader->loadFromQuery($query);
            $collection->merge($targetCollection);

            /*
            $cases[] = '
                WHEN (pivot_obj.target_object_type = "'.$targetObjectType.'") THEN '.$targetObjectIdent.'.*
            ';
            $joins[] = '
            LEFT JOIN
                    `'.$targetObjectTable.'` AS '.$targetObjectIdent.'
                ON
                    pivot_obj.target_object_id = '.$targetObjectIdent.'.id
                AND
                    pivot_obj.target_object_type = "'.$targetObjectType.'"
                AND
                    pivot_obj.source_object_id = "'.$sourceObjectId.'"
                AND
                    pivot_obj.source_object_type = "'.$sourceObjectType.'"
                AND
                    pivot_obj.group = "home-wall"
                AND
                    '.$targetObjectIdent.'.active = "1"
            ';
            */
        }

        /*
        $query = '
        SELECT
            CASE '.implode('', $cases).'
            END CASE'.
        implode('', $joins).'
        ORDER BY pivot_obj.position';
        */

        $this->pivots[$group] = $collection->sortBy('position');

        return $this->pivots[$group];
    }

    /**
     * Determine if the current object has any nodes.
     *
     * @return boolean Whether $this has any nodes (TRUE) or not (FALSE).
     */
    public function hasPivots()
    {
        return !!($this->numPivots());
    }

    /**
     * Count the number of nodes associated to the current object.
     *
     * @return integer
     */
    public function numPivots()
    {
        return count($this->pivots());
    }

    /**
     * Attach an node to the current object.
     *
     * @param PivotableInterface|ModelInterface $obj An object.
     * @return boolean|self
     */
    public function addPivot($obj)
    {
        if (!$obj instanceof PivotableInterface && !$obj instanceof ModelInterface) {
            return false;
        }

        $model = $this->modelFactory()->create(Pivot::class);

        $sourceObjectId = $this->id();
        $sourceObjectType = $this->objType();
        $pivotId = $obj->id();

        $model->setPivotId($pivotId);
        $model->setObjId($sourceObjectId);
        $model->setObjType($sourceObjectType);

        $model->save();

        return $this;
    }

    /**
     * Remove all pivots linked to a specific object.
     *
     * @return boolean
     */
    public function removePivots()
    {
        $pivotProto = $this->modelFactory()->get(Pivot::class);

        $loader = $this->collectionLoader();
        $loader
            ->setModel($pivotProto)
            ->addFilter('source_object_type', $this->objType())
            ->addFilter('source_object_id', $this->id());

        $collection = $loader->load();

        foreach ($collection as $obj) {
            $obj->delete();
        }

        return true;
    }

    /**
     * Retrieve the relation widget.
     *
     * @return RelationWidget
     */
    protected function relationWidget()
    {
        return $this->relationWidget;
    }

    /**
     * Set the relation widget.
     *
     * @param  RelationWidget $widget The widget displaying pivots.
     * @return string
     */
    protected function setRelationWidget(RelationWidget $widget)
    {
        $this->relationWidget = $widget;

        return $this;
    }

    /**
     * Retrieve the object's type identifier.
     *
     * @return string
     */
    abstract function objType();

    /**
     * Retrieve the object's unique ID.
     *
     * @return mixed
     */
    abstract function id();

    /**
     * Retrieve the object model factory.
     *
     * @return \Charcoal\Factory\FactoryInterface
     */
    abstract public function modelFactory();

    /**
     * Retrieve the model collection loader.
     *
     * @return \Charcoal\Loader\CollectionLoader
     */
    abstract public function collectionLoader();
}
