<?php

namespace Charcoal\Cms;

/**
 * CMS Event
 */
final class Event extends AbstractEvent
{
    /**
     *
     * @see CategorizableTrait::$ategoryType()
     * @return string
     */
    public function categoryType()
    {
        return EventCategory::class;
    }
}
