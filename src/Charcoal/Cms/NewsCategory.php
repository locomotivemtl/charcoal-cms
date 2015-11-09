<?php

namespace Charcoal\Cms;

// Module `charcoal-base` dependencies
use \Charcoal\Object\Content;
use \Charcoal\Object\CategoryInterface;
use \Charcoal\Object\CategoryTrait;

class FaqCategory extends Content implements CategoryInterface
{
    use CategoryTrait;

    /**
    * CategoryTrait > item_type()
    *
    * @return string
    */
    public function item_type()
    {
        return 'charcoal/cms/news';
    }

    /**
    * @return Collection
    */
    public function load_items()
    {
        return [];
    }
}
