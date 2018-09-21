<?php

namespace Charcoal\Cms;

/**
 * Taggable Interface implementation
 */
trait TaggableTrait
{
    /**
     * @var mixed
     */
    protected $tags;

    /**
     * @param mixed $tags The object tags.
     * @return self
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * @return mixed
     */
    public function tags()
    {
        return $this->tags;
    }
}
