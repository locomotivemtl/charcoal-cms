<?php

namespace Charcoal\Cms\Support\Interfaces;

/**
 *
 */
interface EventManagerAwareInterface
{
    /**
     * Formatted event list
     * Return the entries for the current page
     * @return \Generator|void
     */
    public function eventsList();

    /**
     * Formatted event archive list
     * Returns the entries for the current page.
     * @return \Generator|void
     */
    public function eventArchiveList();

    /**
     * Current event.
     * @return array The properties of the current event.
     */
    public function currentEvent();

    /**
     * @return mixed
     */
    public function featEvents();

    /**
     * Next event in list.
     * @return array The next event properties.
     */
    public function nextEvent();

    /**
     * Next event in list.
     * @return array The next event properties.
     */
    public function prevEvent();

    /**
     * Amount of events (total)
     * @return integer How many events?
     */
    public function numEvent();

    /**
     * @return float
     */
    public function numEventPages();

    /**
     * @return boolean
     */
    public function eventHasPager();

    /**
     * @return \Generator
     */
    public function eventCategoryList();
}
