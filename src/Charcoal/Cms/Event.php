<?php

namespace Charcoal\Cms;

/**
 * CMS Event
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
        return EventCategory::class;
    }
}
