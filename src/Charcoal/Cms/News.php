<?php

namespace Charcoal\Cms;

// Intra-module (`charcoal-cms`) dependencies
use Charcoal\Cms\AbstractNew;
use Charcoal\Cms\NewsCategory;

/**
* CMS News
*/
final class News extends AbstractNews
{
    /**
     * CategorizableTrait > categoryType()
     *
     * @return string
     */
    public function categoryType()
    {
        return NewsCategory::class;
    }
}
