<?php

namespace Charcoal\Cms\News;

// local dependencies
use Charcoal\Cms\AbstractNews;
use Charcoal\Cms\Mixin\ContentNewsInterface;

/**
 * Content News
 *
 * News object that uses tinyMCE as content source.
 */
class ContentNews extends AbstractNews implements
    ContentNewsInterface
{
}
