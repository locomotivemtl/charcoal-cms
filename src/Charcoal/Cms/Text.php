<?php

namespace Charcoal\Cms;

use \Charcoal\Cms\AbstractText;

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
        return 'charcoal/cms/text-category';
    }
}
