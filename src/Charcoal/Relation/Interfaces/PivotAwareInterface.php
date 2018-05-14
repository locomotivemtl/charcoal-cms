<?php

namespace Charcoal\Relation\Interfaces;

/**
 * Defines an object that is the source of an intermediary pivot object.
 */
interface PivotAwareInterface
{
    /**
     * Retrieve the object's type identifier.
     *
     * @return string
     */
    public function objType();

    /**
     * Retrieve the object's unique ID.
     *
     * @return mixed
     */
    public function id();
}
