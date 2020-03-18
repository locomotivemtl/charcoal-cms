<?php

namespace Charcoal\Tests\Cms\Mock;

// From 'charcoal-app'
use Charcoal\App\Template\AbstractTemplate;

// From 'charcoal-cms'
use Charcoal\Cms\NewsInterface;

/**
 * News Template Controller
 */
class NewsTemplate extends AbstractTemplate
{
    /**
     * @var NewsInterface $news
     */
    private $news;

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
}
