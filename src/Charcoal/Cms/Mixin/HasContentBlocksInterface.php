<?php

namespace Charcoal\Cms\Mixin;

// dependencies from `beneroch/charcoal-attachment`
use Charcoal\Attachment\Interfaces\AttachmentAwareInterface;

/**
 * Defines flexible nodes of palpable content
 * (e.g., a magazine or newspaper article, a blog entry).
 *
 * See `HasContentBlocksTrait` for a basic implementation.
 */
interface HasContentBlocksInterface extends AttachmentAwareInterface
{
}
