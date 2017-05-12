<?php

namespace Charcoal\Relation\Interfaces;

/**
 * Defines an object that is the target of an intermediary pivot object.
 */
interface PivotableInterface
{
    /**
     * Retrieve the object's pivot source object.
     *
     * @return string
     */
    public function belongsTo();

    /**
     * Retrieve the object's pivot list heading.
     *
     * @return string
     */
    public function pivotHeading();

    /**
     * Retrieve the pivot's target object glyphicon identifier.
     *
     * @return string
     */
    public function pivotGlyphicon();
}
