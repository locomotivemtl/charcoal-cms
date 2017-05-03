<?php

namespace Charcoal\Relation;

// From 'charcoal-core'
use Charcoal\Model\AbstractModel;

/**
 * A basic object relationship table
 *
 * To resolve many-to-many relationships between objects.
 */
class Pivot extends AbstractModel implements
    PivotInterface
{
    /**
     * The primary object ID.
     *
     * @var mixed
     */
    protected $sourceObjectId;

    /**
     * The primary object type.
     *
     * @var string
     */
    protected $sourceObjectType;

    /**
     * The foreign object ID.
     *
     * @var mixed
     */
    protected $targetObjectId;

    /**
     * The foreign object type.
     *
     * @var mixed
     */
    protected $targetObjectType;

    /**
     * Associations are active by default.
     *
     * @var boolean
     */
    protected $active = true;

    /**
     * The association's position amongst other associations.
     *
     * @var integer
     */
    protected $position = 0;

    /**
     * Retrieve the source object type.
     *
     * @return string
     */
    public function sourceObjectType()
    {
        return $this->sourceObjectType;
    }

    /**
     * Set the source object type.
     *
     * @param string $type The object type identifier.
     * @throws InvalidArgumentException If provided argument is not of type 'string'.
     * @return self
     */
    public function setSourceObjectType($type)
    {
        if (!is_string($type)) {
            throw new InvalidArgumentException('Object type must be a string.');
        }

        $this->sourceObjectType = $type;

        return $this;
    }

    /**
     * Retrieve the source object ID.
     *
     * @return mixed
     */
    public function sourceObjectId()
    {
        return $this->sourceObjectId;
    }

    /**
     * Set the source object ID.
     *
     * @param mixed $id The object ID to join the pivot to.
     * @throws InvalidArgumentException If provided argument is not a string or numerical value.
     * @return self
     */
    public function setSourceObjectId($id)
    {
        if (!is_scalar($id)) {
            throw new InvalidArgumentException(
                'Object ID must be a string or numerical value.'
            );
        }

        $this->sourceObjectId = $id;

        return $this;
    }

    /**
     * Retrieve the target object ID.
     *
     * @return mixed
     */
    public function targetObjectType()
    {
        return $this->targetObjectType;
    }

    /**
     * Set the target object type.
     *
     * @param string $type The object type identifier.
     * @throws InvalidArgumentException If provided argument is not of type 'string'.
     * @return self
     */
    public function setTargetObjectType($type)
    {
        if (!is_string($type)) {
            throw new InvalidArgumentException('Object type must be a string.');
        }

        $this->targetObjectType = $type;

        return $this;
    }

    /**
     * Retrieve the target object ID.
     *
     * @return mixed
     */
    public function targetObjectId()
    {
        return $this->targetObjectId;
    }

    /**
     * Set the target object ID.
     *
     * @param mixed $id The object ID to pivot upon with.
     * @throws InvalidArgumentException If provided argument is not a string or numerical value.
     * @return self
     */
    public function setTargetObjectId($id)
    {
        if (!is_scalar($id)) {
            throw new InvalidArgumentException(
                'Object ID must be a string or numerical value.'
            );
        }

        $this->targetObjectId = $id;

        return $this;
    }

    /**
     * Retrieve the pivot's position.
     *
     * @return integer
     */
    public function position()
    {
        return $this->position;
    }

    /**
     * Define the pivot's position amongst other pivot targets to the source object.
     *
     * @param integer $position The position (for ordering purpose).
     * @throws InvalidArgumentException If the position is not an integer (or numeric integer string).
     * @return self
     */
    public function setPosition($position)
    {
        if ($position === null) {
            $this->position = null;
            return $this;
        }

        if (!is_numeric($position)) {
            throw new InvalidArgumentException(
                'Position must be an integer.'
            );
        }

        $this->position = (int)$position;

        return $this;
    }

    /**
     * @return boolean
     */
    public function active()
    {
        return $this->active;
    }

    /**
     * @param boolean $active The active flag.
     * @return Content Chainable
     */
    public function setActive($active)
    {
        $this->active = !!$active;

        return $this;
    }
}
