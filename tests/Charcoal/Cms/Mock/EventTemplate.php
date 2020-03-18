<?php

namespace Charcoal\Tests\Cms\Mock;

// From 'charcoal-app'
use Charcoal\App\Template\AbstractTemplate;

// From 'charcoal-cms'
use Charcoal\Cms\EventInterface;

/**
 * Event Template Controller
 */
class EventTemplate extends AbstractTemplate
{
    /**
     * @var EventInterface $event
     */
    private $event;

    /**
     * @return EventInterface
     */
    public function event()
    {
        return $this->event;
    }

    /**
     * @param  EventInterface $event The current event.
     * @return self
     */
    public function setEvent(EventInterface $event)
    {
        $this->event = $event;

        return $this;
    }
}
