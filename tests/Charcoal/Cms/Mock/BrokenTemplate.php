<?php

namespace Charcoal\Tests\Cms\Mock;

// From 'charcoal-app'
use Charcoal\App\Template\AbstractTemplate;

// From 'charcoal-cms'
use Charcoal\Cms\EventInterface;
use Charcoal\Cms\NewsInterface;
use Charcoal\Cms\SectionInterface;

/**
 * Broken Template Controller
 */
class BrokenTemplate extends AbstractTemplate
{
    /**
     * @var EventInterface $event
     */
    private $event;

    /**
     * @var NewsInterface $news
     */
    private $news;

    /**
     * @var SectionInterface $section
     */
    private $section;

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

    /**
     * @return NewsInterface
     */
    public function news()
    {
        return $this->news;
    }

    /**
     * @param  NewsInterface $news The current news.
     * @return self
     */
    public function setNews(NewsInterface $news)
    {
        $this->news = $news;

        return $this;
    }

    /**
     * @return SectionInterface
     */
    public function section()
    {
        return $this->section;
    }

    /**
     * @param  SectionInterface $section The current section.
     * @return self
     */
    public function setSection(SectionInterface $section)
    {
        $this->section = $section;

        return $this;
    }
}
