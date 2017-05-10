<?php

namespace Charcoal\Relation\Traits;

use InvalidArgumentException;

// From 'charcoal-cms'
use Charcoal\Admin\Widget\PivotWidget;
use Charcoal\Relation\Interfaces\PivotableInterface;
use Charcoal\Relation\Pivot;

/**
 * Provides support for pivots on objects.
 *
 * Used by target objects that are pivoted upon by another source object.
 *
 * Abstract methods needs to be implemented.
 *
 * Implementation of {@see \Charcoal\Relation\Interfaces\PivotableInterface}
 *
 * ## Required Services
 *
 * - 'model/factory' — {@see \Charcoal\Model\ModelFactory}
 * - 'model/collection/loader' — {@see \Charcoal\Loader\CollectionLoader}
 */
trait PivotableTrait
{
    /**
     * Retrieve the source object associated to the current target object.
     *
     * @param  string|null $sourceObjectType Filter the pivots by an object type identifier.
     * @throws InvalidArgumentException If the $sourceObjectType is invalid.
     * @return Collection|Pivot[]
     */
    public function belongsTo($sourceObjectType = null)
    {
        if ($sourceObjectType === null) {
            throw new InvalidArgumentException('The $sourceObjectType must be a objType.');
        } elseif (!is_string($sourceObjectType)) {
            throw new InvalidArgumentException('The $sourceObjectType must be a string.');
        }

        $targetObjectType = $this->objType();
        $targetObjectId = $this->id();

        $pivotProto = $this->modelFactory()->get(Pivot::class);
        $pivotTable = $pivotProto->source()->table();

        $sourceObjProto = $this->modelFactory()->get($sourceObjectType);
        $sourceObjTable = $sourceObjProto->source()->table();

        if (!$sourceObjProto->source()->tableExists() || !$pivotProto->source()->tableExists()) {
            return [];
        }

        $query = '
            SELECT
                source_obj.*,
                pivot_obj.source_object_id AS source_object_id,
                pivot_obj.position AS position
            FROM
                `'.$sourceObjTable.'` AS source_obj
            LEFT JOIN
                `'.$pivotTable.'` AS pivot_obj
            ON
                pivot_obj.source_object_id = source_obj.id
            WHERE
                source_obj.active = 1
            AND
                pivot_obj.target_object_type = "'.$targetObjectType.'"
            AND
                pivot_obj.target_object_id = "'.$targetObjectId.'"
            AND
                pivot_obj.source_object_type = "'.$sourceObjectType.'"

            ORDER BY pivot_obj.position';

        $loader = $this->collectionLoader();
        $loader->setModel($sourceObjProto);

        $collection = $loader->loadFromQuery($query);

        return $collection[0];
    }

    /**
     * Save hook called after saving the model.
     *
     * @return boolean
     */
    public function postPivotSave()
    {
        return true;
    }

    /**
     * Retrieve the pivot's target object heading template.
     *
     * To be extended on objects that use PivotableTrait.
     *
     * @return Translation|string|null
     */
    public function pivotHeading()
    {
        return $this->defaultPivotHeading();
    }

    /**
     * Retrieve the default pivot target object heading template.
     *
     * @return Translation|string|null
     */
    protected function defaultPivotHeading()
    {
        return $this->translator()->translation('{{ objType }} #{{ id }}', [
            '{{ objType }}' => $this->objType(),
            '{{ id }}'      => $this->id()
        ]);
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
     * Retrieve the object's label.
     *
     * @return string
     */
    abstract public function collectionLoader();
}
