<?php

namespace Charcoal\Cms;

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
