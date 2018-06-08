<?php

namespace Charcoal\Cms\Support\Interfaces;

// From 'charcoal-object'
use Charcoal\Object\CategoryInterface;

// From 'charcoal-cms'
use Charcoal\Cms\NewsInterface;

/**
 *
 */
interface NewsManagerAwareInterface
{
    /**
     * Formatted news list
     * Returns the entries for the current page.
     * @return \Generator|void
     */
    public function newsList();

    /**
     * Formatted news archive list
     * Returns the entries for the current page.
     * @return \Generator|void
     */
    public function newsArchiveList();

    /**
     * Current news.
     * @return array The properties of the current news
     */
    public function currentNews();

    /**
     * @return \Generator
     */
    public function featNews();

    /**
     * Next news in list.
     * @return array The next news properties.
     */
    public function nextNews();

    /**
     * Next news in list.
     * @return array The next news properties.
     */
    public function prevNews();

    /**
     * Amount of news (total)
     * @return integer How many news?
     */
    public function numNews();

    /**
     * @return float
     */
    public function numNewsPages();

    /**
     * @return boolean
     */
    public function newsHasPager();

    /**
     * @return \Generator
     */
    public function newsCategoryList();

    /**
     * @param NewsInterface $news Charcoal\Cms\NewsInterface;.
     * @return CategoryInterface
     */
    public function newsCategory(NewsInterface $news);
}
