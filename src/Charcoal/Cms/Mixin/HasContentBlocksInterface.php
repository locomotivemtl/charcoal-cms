<?php

namespace Charcoal\Cms\Mixin;

// From 'charcoal-attachment'
use Charcoal\Attachment\Interfaces\AttachmentAwareInterface;

/**
 * Defines flexible nodes of palpable content
 * (e.g., a magazine or newspaper article, a blog entry).
 *
 * See `HasContentBlocksTrait` for a basic implementation.
 */
interface HasContentBlocksInterface
{
    /**
     * Retrieve this object's content blocks.
     *
     * @return Collection|Attachment[]
     */
    public function contentBlocks();

    /**
     * Determine if this object has any content blocks.
     *
     * @return boolean
     */
    public function hasContentBlocks();

    /**
     * Count the number of content blocks associated to this object.
     *
     * @return integer
     */
    public function numContentBlocks();

    /**
     * @return Translation
     */
    public function defaultMetaDescription();
}
