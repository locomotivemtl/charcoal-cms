<?php

namespace Charcoal\Cms;

// Intra-module (`charcoal-cms`) dependencies
use Charcoal\Cms\AbstractFaq;
use Charcoal\Cms\FaqCategory;

/**
 *
 */
final class Faq extends AbstractFaq
{
    /**
     * CategorizableTrait > categoryType()
     *
     * @return string
     */
    public function categoryType()
    {
        return FaqCategory::class;
    }
}
