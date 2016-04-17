<?php

namespace Charcoal\Cms;

use \Charcoal\Cms\AbstractEvent;

/**
 * CMS Event.
 */
final class Event extends AbstractEvent
{
    /**
     * CategorizableTrait > category_type()
     *
     * @return string
     */
    public function categoryType()
    {
        return 'charcoal/cms/event-category';
    }


    /**
     * MetatagTrait > canonical_url
     *
     * @return string
     * @todo
     */
    public function canonicalUrl()
    {
        return '';
    }
}
