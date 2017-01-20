<?php

namespace Charcoal\Cms;

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
