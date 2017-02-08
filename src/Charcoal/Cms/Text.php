<?php

namespace Charcoal\Cms;

// From 'charcoal-cms'
use Charcoal\Cms\AbstractText;
use Charcoal\Cms\TextCategory;

/**
 * CMS Text
 */
final class Text extends AbstractText
{
     /**
      * CategorizableTrait > categoryType()
      *
      * @return string
      */
    public function categoryType()
    {
        return TextCategory::class;
    }
}
