<?php

namespace Charcoal\Relation\Traits;

use InvalidArgumentException;

// From 'charcoal-core'
use Charcoal\Model\ModelInterface;

// From 'charcoal-cms'
use Charcoal\Admin\Widget\PivotWidget;
use Charcoal\Relation\Interfaces\PivotableInterface;
use Charcoal\Relation\Pivot;

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
     * Store the widget instance currently displaying pivots.
     *
     * @var PivotWidget
     */
    protected $pivotWidget;

    /**
     * Retrieve the objects associated to the current object.
     *
     * @param  string|null $targetObjectType Filter the pivots by an object type identifier.
     * @throws InvalidArgumentException If the $targetObjectType is invalid.
     * @return Collection|Pivot[]
     */
    public function pivots($targetObjectType = null)
    {
        if ($targetObjectType === null) {
            throw new InvalidArgumentException('The $targetObjectType must be a objType.');
        } elseif (!is_string($targetObjectType)) {
            throw new InvalidArgumentException('The $targetObjectType must be a string.');
        }

        $sourceObjectType = $this->objType();
        $sourceObjectId = $this->id();

        $pivotProto = $this->modelFactory()->get(Pivot::class);
        $pivotTable = $pivotProto->source()->table();

        $targetObjProto = $this->modelFactory()->get($targetObjectType);
        $targetObjTable = $targetObjProto->source()->table();

        if (!$targetObjProto->source()->tableExists() || !$pivotProto->source()->tableExists()) {
            return [];
        }

        $query = '
            SELECT
                target_obj.*,
                pivot_obj.target_object_id AS target_object_id,
                pivot_obj.position AS position
            FROM
                `'.$targetObjTable.'` AS target_obj
            LEFT JOIN
                `'.$pivotTable.'` AS pivot_obj
            ON
                pivot_obj.target_object_id = target_obj.id
            WHERE
                target_obj.active = 1
            AND
                pivot_obj.source_object_type = "'.$sourceObjectType.'"
            AND
                pivot_obj.source_object_id = "'.$sourceObjectId.'"
            AND
                pivot_obj.target_object_type = "'.$targetObjectType.'"

            ORDER BY pivot_obj.position';

        $loader = $this->collectionLoader();
        $loader->setModel($targetObjProto);

        $collection = $loader->loadFromQuery($query);

        $this->pivots[$targetObjectType] = $collection;

        return $this->pivots[$targetObjectType];
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
     * Retrieve the pivot widget.
     *
     * @return PivotWidget
     */
    protected function pivotWidget()
    {
        return $this->pivotWidget;
    }

    /**
     * Set the pivot widget.
     *
     * @param  PivotWidget $widget The widget displaying pivots.
     * @return string
     */
    protected function setPivotWidget(PivotWidget $widget)
    {
        $this->pivotWidget = $widget;

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
