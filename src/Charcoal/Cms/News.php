<?php

namespace Charcoal\Cms;

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
