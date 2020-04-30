<?php

namespace Charcoal\Cms\Support\Traits;

// From Slim
use Slim\Exception\ContainerException;

// From 'charcoal-object'
use Charcoal\Object\CategoryInterface;

// From 'charcoal-cms'
use Charcoal\Cms\EventInterface;
use Charcoal\Cms\Service\Manager\EventManager;

/**
 *
 */
trait EventManagerAwareTrait
{
    /**
     * Currently displayed event.
     * @var array $event City/Object/Event
     */
    private $currentEvent;

    /**
     * @var EventManager $eventManager
     */
    private $eventManager;

    // ==========================================================================
    // FUNCTIONS
    // ==========================================================================

    /**
     * Formatted event list
     * Return the entries for the current page
     * @return \Generator|void
     */
    public function eventsList()
    {
        $eventList = $this->eventManager()->entries();
        foreach ($eventList as $event) {
            yield $this->eventFormatShort($event);
        }
    }

    /**
     * Formatted event archive list
     * Returns the entries for the current page.
     * @return \Generator|void
     */
    public function eventArchiveList()
    {
        $entries = $this->eventManager()->archive();
        foreach ($entries as $entry) {
            yield $this->eventFormatShort($entry);
        }
    }

    /**
     * Current event.
     * @return array The properties of the current event.
     */
    public function currentEvent()
    {
        if ($this->currentEvent) {
            return $this->currentEvent;
        }

        // The the current event
        $event = $this->eventManager()->entry();

        // Format the event
        if ($event) {
            $this->currentEvent = $this->eventFormatFull($event);
        }

        return $this->currentEvent;
    }

    /**
     * @return mixed
     */
    public function featEvents()
    {
        $entries = $this->eventManager()->featList();

        if (!$entries) {
            return;
        }

        foreach ($entries as $entry) {
            if ($entry->id()) {
                yield $this->eventFormatFull($entry);
            }
        }
    }

    /**
     * Next event in list.
     * @return array The next event properties.
     */
    public function nextEvent()
    {
        $next = $this->eventManager()->next();

        if (!$next) {
            return null;
        }

        return $this->eventFormatNav($next);
    }

    /**
     * Next event in list.
     * @return array The next event properties.
     */
    public function prevEvent()
    {
        $prev = $this->eventManager()->prev();

        if (!$prev) {
            return null;
        }

        return $this->eventFormatNav($prev);
    }

    /**
     * Amount of events (total)
     * @return integer How many events?
     */
    public function numEvent()
    {
        return $this->eventManager()->numEvent();
    }

    /**
     * @return float
     */
    public function numEventPages()
    {
        return $this->eventManager()->numPages();
    }

    /**
     * @return boolean
     */
    public function eventHasPager()
    {
        return $this->eventManager()->hasPager();
    }

    /**
     * @return \Generator
     */
    public function eventCategoryList()
    {
        $cats = $this->eventManager()->loadCategoryItems();
        foreach ($cats as $cat) {
            yield $this->eventFormatCategory($cat);
        }
    }

    /**
     * @param EventInterface $event Charcoal\Cms\EventInterface.
     * @return CategoryInterface
     */
    protected function eventCategory(EventInterface $event)
    {
        $id = $event->category();

        return $this->eventManager()->categoryItem($id);
    }

    // ==========================================================================
    // FORMATTER
    // ==========================================================================

    /**
     * @param EventInterface $event Charcoal\Cms\EventInterface.
     * @return string
     */
    protected function getEventStartDateFormat(EventInterface $event)
    {
        return $this->dateHelper()->formatDate(
            $event->startDate()
        );
    }

    /**
     * @param EventInterface $event Charcoal\Cms\EventInterface.
     * @return string
     */
    protected function getEventEndDateFormat(EventInterface $event)
    {
        return $this->dateHelper()->formatDate(
            $event->endDate()
        );
    }

    /**
     * @param EventInterface $event Charcoal\Cms\EventInterface.
     * @return string
     */
    protected function getEventDateFormat(EventInterface $event)
    {
        return $this->dateHelper()->formatDate([
            $event->startDate(),
            $event->endDate()
        ]);
    }

    /**
     * @param EventInterface $event Charcoal\Cms\EventInterface.
     * @return string
     */
    protected function getEventTimeFormat(EventInterface $event)
    {
        if ($event->dateNotes() != '') {
            return $event->dateNotes();
        }

        return $this->dateHelper()->formatTime([
            $event->startDate(),
            $event->endDate(),
        ]);
    }

    /**
     * Formatting expected in templates
     * @param EventInterface $event Charcoal\Cms\EventInterface.
     * @return array The needed event properties.
     */
    protected function eventFormatShort(EventInterface $event)
    {
        return [
            'title'         => (string)$event->title(),
            'url'           => (string)$event->url(),
            'startDate'     => $this->getEventStartDateFormat($event),
            'startDateTime' => $event->startDate()->format('Y-m-d h:i'),
            'endDate'       => $this->getEventEndDateFormat($event),
            'endDateTime'   => $event->endDate()->format('Y-m-d h:i'),
            'date'          => $this->getEventDateFormat($event),
            'time'          => $this->getEventTimeFormat($event),
            'active'        => ($this->currentEvent() && ($this->currentEvent()['id'] == $event->id()))
        ];
    }

    /**
     * Formatting expected in templates
     * @param EventInterface $event Charcoal\Cms\EventInterface.
     * @return array The needed event properties.
     */
    protected function eventFormatNav(EventInterface $event)
    {
        return [
            'startDate'     => $this->getEventStartDateFormat($event),
            'startDateTime' => $event->startDate()->format('Y-m-d h:i'),
            'endDate'       => $this->getEventEndDateFormat($event),
            'endDateTime'   => $event->endDate()->format('Y-m-d h:i'),
            'date'          => $this->getEventDateFormat($event),
            'time'          => $this->getEventTimeFormat($event),
            'title'         => (string)$event->title(),
            'url'           => $event->url()
        ];
    }

    /**
     * @param EventInterface $event The current event.
     * @return array The needed properties.
     */
    protected function eventFormatFull(EventInterface $event)
    {
        $contentBlocks = $event->getAttachments('content-blocks');
        $gallery = $event->getAttachments('image-gallery');
        $documents = $event->getAttachments('document');

        return [
            'id'               => $event->id(),
            'title'            => (string)$event->title(),
            'summary'          => (string)$event->summary(),
            'content'          => (string)$event->content(),
            'image'            => $event->image(),
            'startDate'        => $this->getEventStartDateFormat($event),
            'startDateTime'    => $event->startDate()->format('Y-m-d h:i'),
            'endDate'          => $this->getEventEndDateFormat($event),
            'endDateTime'      => $event->endDate()->format('Y-m-d h:i'),
            'date'             => $this->getEventDateFormat($event),
            'time'             => $this->getEventTimeFormat($event),
            'contentBlocks'    => $contentBlocks,
            'hasContentBlocks' => !!(count($contentBlocks)),
            'documents'        => $documents,
            'hasDocuments'     => !!(count($documents)),
            'gallery'          => $gallery,
            'hasGallery'       => !!(count($gallery)),
            'url'              => $event->url(),
            'metaTitle'        => (string)$event->metaTitle(),
            'locationName'     => (string)$event->locationName(),
            'dateNotes'        => (string)$event->dateNotes(),
            'category'         => $event->category()
        ];
    }

    /**
     * @param CategoryInterface $category The category item.
     * @return array The formatted category item.
     */
    protected function eventFormatCategory(CategoryInterface $category)
    {
        return [
            'id'   => $category->id(),
            'name' => (string)$category->name(),
        ];
    }

    // ==========================================================================
    // DEPENDENCIES
    // ==========================================================================

    /**
     * @return EventManager
     * @throws ContainerException When dependency is missing.
     */
    protected function eventManager()
    {
        if (!$this->eventManager instanceof EventManager) {
            throw new ContainerException(sprintf(
                'Missing dependency for %s: %s',
                get_called_class(),
                EventManager::class
            ));
        }

        return $this->eventManager;
    }

    /**
     * @param EventManager $eventManager The event Manager class.
     * @return self
     */
    protected function setEventManager(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;

        return $this;
    }

    /**
     * dateHelperAwareTrait dependency
     * @return mixed
     */
    abstract protected function dateHelper();
}
