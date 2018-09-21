<?php

namespace Charcoal\Cms;

/**
 * Taggable behavior: objects may have cms tags.
 */
interface TaggableInterface
{
    /**
     * @param mixed $tags The object tags.
     * @return self
     */
    public function setTags($tags);

    /**
     * @return mixed
     */
    public function tags();
}
