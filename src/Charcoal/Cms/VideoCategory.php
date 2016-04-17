<?php

namespace Charcoal\Cms;

// Module `charcoal-base` dependencies
use \Charcoal\Object\Content;
use \Charcoal\Object\CategoryInterface;
use \Charcoal\Object\CategoryTrait;

/**
 * Video category
 */
final class VideoCategory extends Content implements CategoryInterface
{
    use CategoryTrait;

    /**
     * CategoryTrait > itemType()
     *
     * @return string
     */
    public function itemType()
    {
        return 'charcoal/cms/video';
    }

    /**
     * @return Collection
     */
    public function loadCategoryItems()
    {
        return [];
    }
}
